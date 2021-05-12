/**
 * Convertful Options Framework
 *
 * @requires jQuery.fn.cfMod
 */
!function($, undefined){
	'use strict';
	if (window.$vof === undefined) window.$vof = {};
	if (window.$vof.mixins === undefined) window.$vof.mixins = {};

	/**
	 * Class mutator, allowing on, off, and trigger class instance events
	 * @type {{}}
	 */
	$vof.mixins.Events = {
		/**
		 * Attach a handler to an event for the class instance
		 * @param {String} handle A string containing event type, such as 'beforeShow' or 'change'
		 * @param {Function} fn A function to execute each time the event is triggered
		 */
		on: function(handle, fn){
			if (this.$$events === undefined) this.$$events = {};
			if (this.$$events[handle] === undefined) this.$$events[handle] = [];
			this.$$events[handle].push(fn);
			return this;
		},
		/**
		 * Remove a previously-attached event handler from the class instance
		 * @param {String} handle A string containing event type, such as 'beforeShow' or 'change'
		 * @param {Function} [fn] The function that is to be no longer executed.
		 * @chainable
		 */
		off: function(handle, fn){
			if (this.$$events === undefined || this.$$events[handle] === undefined) return this;
			if (fn !== undefined){
				var handlerPos = $.inArray(fn, this.$$events[handle]);
				if (handlerPos !== -1){
					this.$$events[handle].splice(handlerPos, 1);
				}
			} else {
				this.$$events[handle] = [];
			}
			return this;
		},
		/**
		 * Execute all handlers and behaviours attached to the class instance for the given event type
		 * @param {String} eventType A string containing event type, such as 'beforeShow' or 'change'
		 * @param {Array} extraParameters Additional parameters to pass along to the event handler
		 * @chainable
		 */
		trigger: function(eventType, extraParameters){
			if (this.$$events === undefined || this.$$events[eventType] === undefined || this.$$events[eventType].length === 0) return this;
			var eventsCount = this.$$events[eventType].length; //prevent loop then add event inside event
			for (var index = 0; index < eventsCount; index++){
				this.$$events[eventType][index].apply(this, extraParameters);
			}
			return this;
		}
	};

	/**
	 * $vof.Field class
	 * Boundable events: beforeShow, afterShow, change, beforeHide, afterHide
	 * @param row
	 * @param noInit bool Don't init on load. Instead the field will be inited on beforeShow event
	 * @constructor
	 */
	$vof.Field = function(row, noInit){
		this.$row = $(row);
		if (this.$row.data('vof_field')) return this.$row.data('vof_field');
		this.type = this.$row.cfMod('type');
		this.name = this.$row.data('name');
		this.$input = this.$row.find('input[name="' + this.name + '"], textarea[name="' + this.name + '"], select[name="' + this.name + '"]');
		this.inited = false;

		// Overloading by a certain type's declaration, moving parent functions to "parent" namespace: init => parentInit
		if ($vof.Field[this.type] !== undefined){
			for (var fn in $vof.Field[this.type]){
				if (!$vof.Field[this.type].hasOwnProperty(fn)) continue;
				if (this[fn] !== undefined){
					var parentFn = 'parent' + fn.charAt(0).toUpperCase() + fn.slice(1);
					this[parentFn] = this[fn];
				}
				this[fn] = $vof.Field[this.type][fn];
			}
		}

		this.$row.data('vof_field', this);

		if (noInit !== undefined && noInit){
			// Init on the first show
			var initEvent = function(){
				this.init();
				this.trigger('afterInit');
				this.inited = true;
				this.off('beforeShow', initEvent);
			}.bind(this);
			this.on('beforeShow', initEvent);
		} else {
			this.init();
			this.trigger('afterInit');
			this.inited = true;
		}
	};
	$.extend($vof.Field.prototype, $vof.mixins.Events, {
		init: function(){
			this.$input.on('change', function(){
				this.trigger('change', [this.getValue()]);
			}.bind(this));
		},
		deinit: function(){
		},
		getValue: function(){
			return this.$input.val();
		},
		setValue: function(value, quiet){
			quiet = quiet || false;
			this.$input.val(value);
			this.render();
			if (!quiet)
				this.trigger('change', [value]);
		},
		render: function($elm){
		},
		clearError: function(){
			this.$row.removeClass('check_wrong');
			this.$row.find('> .vof-form-row-field > .vof-form-row-state').html('');
		},
		showError: function(message){
			this.$row.addClass('check_wrong');
			this.$row.find('> .vof-form-row-field > .vof-form-row-state').html(message);
		},
		affect: function(influenceValue){
		}
	});

	/**
	 * $vof.Field type: alert
	 */
	$vof.Field['alert'] = {
		init: function(){
			new CFAlert(this.$row);
		},
		getValue: function(){
			return null;
		},
		setValue: function(){
			return null;
		}
	};

	/**
	 * $vof.Field type: radio
	 */
	$vof.Field['radio'] = {
		init: function() {
			if (this.$row.hasClass('style_inline')) {
				$.each(this.$row.find('.vof-radio'), function() {
					var $this = $(this);
					$this
						.attr('title', $this.find('.vof-radio-text')
						.text());
				});
			}
			this.$input
				.filter('[type=radio]')
				.on('change', function() {
					var value = this.getValue();
					this._activateRadio(value);
					this.trigger('change', [value]);
				}.bind(this));
		},
		getValue: function(){
			return this.$input.filter('[type=radio]:checked').val();
		},
		setValue: function(value, quite){
			this._activateRadio(value);
			if (!quite)
				this.trigger('change', [value]);
		},
		_activateRadio: function(value) {
			this.$input
				.removeAttr('checked')
				.filter('[value="' + value + '"]')
				.prop('checked', true)
				.attr('checked', true)
				.parent()
				.addClass('active')
				.siblings()
				.removeClass('active');
		},
		visibleOptions: function (options, all = false) {
			$.each(this.$row.find('.vof-radio'), function() {
				var $this = $(this),
					value = $this.find('input').val();

				if (options.indexOf(value) === -1) {
					if (!$this.hasClass('is-hidden'))
						$this.addClass('is-hidden');
				} else {
					$this.removeClass('is-hidden');
				}
			});

			this.setValue(options[0])
		}
	};

	/**
	 * $vof.Field type: select
	 */
	$vof.Field['select'] = {
		init: function(){
			this.$input.on('change keyup', function(){
				this.trigger('change', [this.getValue()]);
			}.bind(this));
		},
		affect: function(influenceValue){
			if (influenceValue.indexOf(this.getValue()) < 0 && influenceValue.length === 0) {
				this.setValue('',true);
				this.$input.closest('form').find('button')
					.addClass('is-disabled')
					.attr('disabled', true);
			}
			else if (influenceValue.length > 0 && this.getValue() === null){
				this.setValue(influenceValue[0]);
			}

			var groups = this.$input.find('optgroup');
			if(groups.length)
			{
				// Group support
				var current_group = groups
					.hide()
					.filter('[data-id="' + influenceValue + '"]')
					.show();

				if( ! current_group.find('option:selected').length)
				{
					current_group
						.find('option:first-child')
						.prop('selected', true);
				}
			}
			else if($.isArray(influenceValue))
			{
				// Standard list options
				var options = this.$input.find('option')
					.hide();
				$.each(influenceValue, function(_, value){
					options
						.filter('[value='+value+']')
						.show();
				});

				if(this.$input.find('option:selected').css('display') === 'none'){
					$('option', this.$input).each(function (i, item) {
						var $this = $(item);
						if ($this.css('display') !== 'none') {
							$this.prop('selected', true);
							$this.closest('form').find('button')
								.removeClass('is-disabled')
								.removeAttr('disabled');
							return false;
						}
					}.bind(this));
				}
			}
		}
	};

	/**
	 * $vof.Field type: select2
	 */
	$vof.Field['select2'] = {
		init: function(){
			var dropdownCssClass = (this.$row.hasClass('style_inline')) ? 'vof-select2-dropdown vof-select2-dropdown_inline' : 'vof-select2-dropdown';
			var dropdownParent = (this.$row.hasClass('parent_popup')) ? this.$row.closest('.b-popup-box') : this.$row.find('.vof-field-hider');

			if (this.$input.hasClass('i-tokenize')){
				this.select2 = this.$input.select2({
					tags: true,
					placeholder: this.$input.data('placeholder'),
					tokenSeparators: [',', ' '],
					dropdownParent: dropdownParent,
					dropdownCssClass: dropdownCssClass
				});
			} else {
				this.select2 = this.$input.select2({
					placeholder: this.$input.data('placeholder'),
					dropdownCssClass: dropdownCssClass
				});
			}
			this.previous = this.getValue();
			$('select').on('select2:selecting', function(){
				this.previous = this.getValue();
			}.bind(this));
			this.select2.on('select2:select', function(){
				this.trigger('change', [this.getValue(), this.previous]);
			}.bind(this));
			this.select2.on('select2:unselect', function(){
				this.trigger('change', [this.getValue(), this.previous]);
			}.bind(this));
			window.select2 = this;
		},
		getValue: function(){
			return this.select2 ? this.select2.val() : null;
		},
		setValue: function(value, quite){
			if (this.select2)
				this.select2.val(value).trigger('change');
			if (!quite)
				this.trigger('change', [value]);
		}
	};

	/**
	 * $vof.Field type: checkbox
	 */
	$vof.Field['checkbox'] = {
		init: function(){
			this.parentInit();
			this.$checkbox = this.$row.find('input[type="checkbox"]');
		},
		getValue: function(){
			return this.$checkbox.is(':checked') ? 1 : 0;
		},
		setValue: function(value, quite){
			this.$checkbox.prop('checked', +value);
			if (!quite)
				this.trigger('change', [value]);
		}
	};

	/**
	 * $vof.Field type: checkboxes
	 */
	$vof.Field['checkboxes'] = {
		init: function(){
			this.$checkboxes = this.$row.find('input[type="checkbox"]');
			this.$checkboxes.on('change', function(e){
				var values = this.getValue();
				this._activateCheckboxButtons(values);
				this._maybeChangeWithNested($(e.target), $.inArray($(e.target).val(), values) !== -1);
				this.trigger('change', [values]);
			}.bind(this));
			this.$filterBlock = this.$row.find('.vof-checkboxes-filter');

			if (this.$filterBlock.length) {
                this._filterInit();

                // Show all checkboxes
                this.$row.find('.vof-checkboxes-filter-show-all').on('click', function (e) {
                    var max_width = parseInt(this.$filterBlock.data('max-width')),
                        elm_height = $(this.$checkboxes[0]).closest('.vof-checkbox').outerHeight(),
                        elms_height = parseInt(this.$checkboxes.length*elm_height);

                    if (elms_height <= max_width)
                        this.$row.find('.vof-checkbox').removeClass('is-hidden');
                    else {
                        this.$checkboxes.each(function (i, item) {
                            var height = (i+1)*elm_height;
                            if (height <= max_width)
                                $(item).closest('.vof-checkbox').removeClass('is-hidden');
                        });
                    }
                }.bind(this));

                // Search by input
                this.$row.find('.vof-checkboxes-filter input').on('keyup', function (e) {
                    var value = $(e.target).val(),
                        min_search_length = parseInt(this.$filterBlock.data('min-search-length')) || 2;
                    clearTimeout(this.searchTimeout);
                    if (!value.length || value.length >= min_search_length)
                    {
                        this.searchTimeout = setTimeout(function() {
                            if (!value.length)
                                this._filterInit();
                            else
                                this._searchCheckboxes(value);
                        }.bind(this), 500);
                    }
                }.bind(this));
            }
		},
		getValue: function() {
			var value = [];
			$.each(this.$checkboxes.filter(':checked'), function (_, $el) {
				value.push($($el).val());
			}.bind(this));
			return value;
		},
		setValue: function(value, quite) {
			this._activateCheckboxButtons(value);
			if (!quite)
				this.trigger('change', [value]);
		},
		_activateCheckboxButtons: function (value) {
			$.each(this.$checkboxes, function (_, $el) {
				$el = $($el);
				$el.prop('checked', $.inArray($el.val(), value) !== -1);
				//this._maybeChangeWithNested($el, checked);
			}.bind(this));
		},
        _filterInit: function () {
		    var max_count = parseInt(this.$filterBlock.data('max-count')) -1,
		        show_all_btn = false;

            this.$checkboxes.each(function (i, item) {
                $(item).closest('.vof-checkbox').removeClass('is-hidden');
                if (i > max_count) {
                    $(item).closest('.vof-checkbox').addClass('is-hidden');
                    show_all_btn = true;
                }
            });

            if (show_all_btn)
                this.$row.find('.vof-checkboxes-filter-show-all').removeClass('is-hidden')
        },
        _searchCheckboxes: function (value) {
		    value = value.toLowerCase();
            var regex = new RegExp(value, 'g');
            this.$checkboxes.each(function (i, item) {
                var text = $(item).closest('.vof-checkbox').find('.vof-checkbox-text').text().toLowerCase();
                if ( ! text.match(regex))
                    $(item).closest('.vof-checkbox').addClass('is-hidden');
                else
                    $(item).closest('.vof-checkbox').removeClass('is-hidden');
            });
        },
		_maybeChangeWithNested: function ($el, checked) {
			var value = $el.val(),
				parent = $el.attr('data-parent'),
				$parentElm, $checked_children, $children;
			if (parent.length > 0) {
				$parentElm = this.$row.find('[value="'+parent+'"]');
				$checked_children = this.$row.find('[data-parent="'+parent+'"]:checked');
				//console.log($checked_children.length);
				if (checked || (!checked && $checked_children.length === 0))
					$parentElm.prop('checked', checked);
			} else {
				$children = this.$row.find('[data-parent="'+value+'"]');
				if ($children.length) {
					$.each($children, function (_, $child) {
						$child = $($child);
						$child.prop('checked', checked);
						//this._maybeChangeWithNested($el, checked);
					}.bind(this));
				}
			}
		},
	};

	/**
	 * $vof.Field type: switch
	 */
	$vof.Field['switcher'] = {
		init: function(){
			this.parentInit();
			this.$checkbox = this.$row.find('input[type="checkbox"]');
			this._confirm = {
				off: this.$input.data('offconfirm'),
				on: this.$input.data('onconfirm')
			};
			if (this._confirm.off || this._confirm.on) {
				this.$input.off('change');
				this.$input.on('change', function(){
					var value = this.getValue(),
						confirmChange;
					if (value === false && this._confirm.off) {
						confirmChange = confirm(this._confirm.off);
						if (confirmChange) {
							this.trigger('change', [this.getValue()]);
						} else {
							this.setValue(!value, true)
						}
					} else if (value === true && this._confirm.on) {
						confirmChange = confirm(this._confirm.on);
						if (confirmChange) {
							this.trigger('change', [this.getValue()]);
						} else {
							this.setValue(!value, true)
						}
					} else {
						this.trigger('change', [this.getValue()]);
					}
				}.bind(this));
			}
		},
		getValue: function(){
			return this.$checkbox.is(':checked');
		},
		setValue: function(value, quite){
			this.$checkbox.prop('checked', ! window.empty(value));
			if (!quite)
				this.trigger('change', [value]);
		}
	};

	/**
	 * $vof.Field type: submit
	 */
	$vof.Field['submit'] = {
		init: function(){
			this.$row.find('.g-btn').on('click', function(){
				this.$row.closest('.i-form').trigger('submit');
			}.bind(this));
		},
		getValue: null,
		setValue: null
	};

	/**
	 * vof Field: Text
	 */
	$vof.Field['text'] = {
		init: function(){
			this.parentInit();
			this.$input.on('keyup', function(){
				this.trigger('change', [this.getValue()]);
			}.bind(this));
			this.$input.on('keydown', function(e){
				if (e.which === 13){
					var $relevantForm = this.$input.closest('.i-form');
					if ($relevantForm.length){
						// Submitting form on enter key
						e.preventDefault();
						e.stopPropagation();
						$relevantForm.submit();
					}
				}
			}.bind(this));
		}
	};

	/**
	 * Class mutator, adding fieldset behaviour to a class
	 * @type {{}}
	 */
	$vof.mixins.Fieldset = $.extend({}, $vof.mixins.Events, {
		/**
		 * Attach fields to Field set, set callbacks, show_if, etc...
		 * @param fields string or jquery selector
		 * @param noFieldsInit
		 * @param fallbackValues
		 */
		attachFields: function(fields, noFieldsInit, fallbackValues){
			// Dependencies rules and the list of dependent fields for all the affecting fields
			if (!this.showIf) this.showIf = {};
			if (!this.influence) this.influence = {};
			if (!this.affects) this.affects = {};
			if (!this.$fields) this.$fields = $();
			if (!this.fields) this.fields = {};
			if (!this._events) this._events = {};
			if (!this.related) this.related = {};
			if (!this._events.changeField) this._events.changeField = {};

			var $fields = $(fields);
			this.$fields = this.$fields.add($fields);

			$fields.each(function(index, row){
				var $row = $(row),
					type = $row.cfMod('type'),
					name = $row.data('name');

				this.fields[name] = new $vof.Field($row, noFieldsInit);
				var $showIf = $row.find((type === 'wrapper_start') ? '> .vof-form-wrapper-cont > .vof-form-wrapper-showif' : '> .vof-form-row-showif');

				if ($showIf.length){
					this.showIf[name] = ($showIf[0].onclick() || {});
					this.getDependencies(this.showIf[name]).forEach(function(dep){
						// Also can depend on dot-separated path
						dep = dep.split('.')[0];
						//if (this.affects[this.showIf[name][0]] === undefined) this.affects[dep] = [];
						if (this.affects[dep] === undefined) this.affects[dep] = [];
						this.affects[dep].push(name);
					}.bind(this));
				}

				var $influence = $row.find('> .vof-form-row-influence');
				if ($influence.length){
					this.influence[name] = ($influence[0].onclick() || {});
					this.getDependencies(this.influence[name]).forEach(function(dep){
						//if (this.affects[this.showIf[name][0]] === undefined) this.affects[dep] = [];
						if (this.affects[dep] === undefined) this.affects[dep] = [];
						this.affects[dep].push(name);
					}.bind(this));
				}

				var $related = $row.find('> .vof-form-row-related');
				if ($related.length){
					this.related[name] = ($related[0].onclick() || {});
				}

				// Attaching already bound changes events to newly added fields
				if (this._singleChangeEvents && this._singleChangeEvents[name]){
					this._singleChangeEvents[name].forEach(function(fn){
						this.fields[name].on('change', fn);
					}.bind(this));
				}
				// Attaching global changes event to newly added fields
				if (this.changeWasBound){
					this.fields[name].on('change', function(val){
						this.trigger('change', [name, val]);
					}.bind(this));
				}
			}.bind(this));
			// Fallback values that will be used for missing fields if used by show_if statements
			if (this.fallbackValues === undefined) this.fallbackValues = {};
			if (fallbackValues) $.extend(this.fallbackValues, fallbackValues);
			$.each(this.affects, function(name, affectedList){
				this._events.changeField[name] = function(){
					for (var index = 0; index < affectedList.length; index++){
						var affectedName = affectedList[index];
						if (this.showIf[affectedName] === undefined || this.checkRules(this.showIf[affectedName], this.getValue.bind(this))){
							this.fields[affectedName].trigger('beforeShow');
							this.fields[affectedName].$row.show();
							this.fields[affectedName].trigger('afterShow');
						} else {
							this.fields[affectedName].trigger('beforeHide');
							this.fields[affectedName].$row.hide();
							this.fields[affectedName].trigger('afterHide');
						}
						if (this.influence[affectedName] !== undefined){
							this.fields[affectedName].affect(this.getValue(this.influence[affectedName]));
						}
					}
				}.bind(this, affectedList);
				if (this.fields[name] === undefined && this.fallbackValues[name] === undefined){
					console.error('Field ' + name + ' not found');
				} else if (name[0] === '_'){
					// Custom events for private\virtual fields (_state)
					this.on('change' + name, this._events.changeField[name]);
				} else if (this.fields[name] !== undefined){
					this.fields[name].on('change', this._events.changeField[name]);
				}
				this._events.changeField[name]();
			}.bind(this));
			// Passing visibility-related events to visible fields
			['beforeShow', 'afterShow', 'beforeHide', 'afterHide'].forEach(function(event){
				this.on(event, function(){
					$.map(this.fields, function(field){
						if (field.$row.css('display') !== 'none') field.trigger(event);
					});
				}.bind(this));
			}.bind(this));
			return this;
		},
		/**
		 * Detach fields from Field set
		 * @param fields string | jquery selector
		 */
		detachFields: function(fields){
			var $fields = $(fields);

			$fields.each(function(index, row){
				var $row = $(row),
					name = $row.data('name'),
					field = this.fields[name];

				if (this.showIf[name]){
					delete this.showIf[name];
				}

				Object.keys(this.affects).map(function(key){
					// Try to find deleted key in other affects
					var fieldIndex = this.affects[key].indexOf(name);
					if (fieldIndex !== -1){
						this.affects[key].splice(fieldIndex, 1);
					}
				}.bind(this));

				// clear affects and fallback if removing last field
				delete this.affects[name];
				delete this.fallbackValues[name];

				// Remove events from fields
				for (var fieldProp in field){
					// TODO Move event handlers removal to deinit of each separate field
					if (field.hasOwnProperty(fieldProp) && fieldProp[0] === '$' && field[fieldProp] instanceof jQuery){
						field[fieldProp].off();
					}
				}

				field.deinit();
				field.off('change', this._events.changeField[name]);
				field.$row.removeData('vof_field');

				delete this.fields[name];
			}.bind(this));

			this.$fields = this.$fields.not($fields);
			return this;
		},
		/**
		 * Get a particular field value
		 * @param name string
		 * @returns {*}
		 */
		getValue: function(name){
			if (this.fields === undefined || this.fields[name] === undefined || ! (this.fields[name].getValue instanceof Function)){
				return (this.fallbackValues || {})[name] || null;
			}
			return this.fields[name].getValue();
		},
		setValue: function(name, value, quiet){
			if (this.fields[name] !== undefined && this.fields[name].setValue instanceof Function){
				this.fields[name].setValue(value, quiet);
			}
		},
		getValues: function(){
			var values = {};
			for (var name in this.fields){
				if (!this.fields.hasOwnProperty(name) || !(this.fields[name].getValue instanceof Function)) continue;
				if (this.fields[name].inited !== false){
					values[name] = this.fields[name].getValue();
				}
			}
			return values;
		},
		setValues: function(values, quiet){
			$.each(values, function(name, value){
				if (this.fields[name] !== undefined && this.fields[name].setValue instanceof Function){
					this.fields[name].setValue(value, quiet);
					// As events are suppressed, triggering events required to update fields visibility only
					if (quiet && this._events.changeField[name]) this._events.changeField[name]();
				}
			}.bind(this));

			return this;
		},
		/**
		 *
		 * @param showIf
		 * @returns {Array}
		 */
		getDependencies: function(showIf){
			var deps = [];
			if (showIf[0] instanceof Array){
				// Complex statement with and / or request
				for (var i = 0; i < showIf.length; i += 2) deps = deps.concat(this.getDependencies(showIf[i]));
			}
			else {
				// Simple statement
				deps.push(showIf[0]);
			}
			return deps;
		},

		fieldIsVisible: function(field){
			if (this.showIf[field] === undefined)
				return true;

			return this.checkRules(this.showIf[field], this.getValue.bind(this));
		},

		/**
		 * Check showIf Rules
		 *
		 * @param showIf
		 * @param getValue function
		 * @returns {boolean}
		 */
		checkRules: function(showIf, getValue){
			var result = true;
			if (!$.isArray(showIf) || showIf.length < 3){
				return result;
			} else if ($.inArray(showIf[1].toLowerCase(), ['and', 'or']) !== -1){
				// Complex or / and statement
				result = this.checkRules(showIf[0], getValue);
				var index = 2;
				while (showIf[index] !== undefined) {
					showIf[index - 1] = showIf[index - 1].toLowerCase();
					if (showIf[index - 1] === 'and'){
						result = (result && this.checkRules(showIf[index], getValue));
					} else if (showIf[index - 1] === 'or'){
						result = (result || this.checkRules(showIf[index], getValue));
					}
					index = index + 2;
				}
			} else {
				// Also can use dot-separated paths
				var fieldPath = showIf[0].split('.'),
					fieldName = fieldPath.shift(),
					value = getValue(fieldName);
				if (fieldPath.length) value = arrayPath(value, fieldPath, undefined);
				if (value === undefined) return true;
				if (showIf[1] === '='){
					result = ( value == showIf[2] );
				} else if (showIf[1] === '!=' || showIf[1] === '<>'){
					result = ( value != showIf[2] );
				} else if (showIf[1] === 'in'){
					result = ( !showIf[2].indexOf || showIf[2].indexOf(value) !== -1);
				} else if (showIf[1] === 'not in'){
					result = ( !showIf[2].indexOf || showIf[2].indexOf(value) === -1);
				} else if (showIf[1] === 'has'){
					result = ( !value.indexOf || value.indexOf(showIf[2]) !== -1);
				} else if (showIf[1] === '<='){
					result = ( value <= showIf[2] );
				} else if (showIf[1] === '<'){
					result = ( value < showIf[2] );
				} else if (showIf[1] === '>'){
					result = ( value > showIf[2] );
				} else if (showIf[1] === '>='){
					result = ( value >= showIf[2] );
				} else {
					result = true;
				}
			}
			return result;
		},
		parentOn: $vof.mixins.Events.on,
		on: function(handle, fn){
			if (handle === 'change' && !this.changeWasBound){
				// Dev note: quite a heavy thing. Try to avoid it when possible ...
				// TODO: fields after attachFields not handle (trigger) events
				// TODO: example this.attachFields => 3 fields attached => this.on => 3 events add to Fieldset => again this.attachFields => no new events to Fieldset
				$.each(this.fields, function(name, field){
					field.on('change', function(val){
						Array.prototype.unshift.call(arguments, name); // add name as first argument
						this.trigger('change', arguments);
					}.bind(this));
				}.bind(this));
				this.changeWasBound = true;
			}
			if (handle.substr(0, 7) === 'change:'){
				// Binding a particular field
				var fieldName = handle.substr(7);
				if (this.fields[fieldName] === undefined) return;
				this.fields[fieldName].on('change', fn);
				// Storing single-bound functions to bind them to newly added fields as well
				this._singleChangeEvents = this._singleChangeEvents || {};
				this._singleChangeEvents[fieldName] = this._singleChangeEvents[fieldName] || [];
				this._singleChangeEvents[fieldName].push(fn);
			} else {
				this.parentOn(handle, fn);
			}

		},
		clearErrors: function(){
			$.each(this.fields, function(fieldId, field){
				if (field.clearError instanceof Function) field.clearError();
			}.bind(this));
		},
		showErrors: function(errors, clearFirst){
			if (clearFirst) this.clearErrors();
			for (var key in errors){
				if (!errors.hasOwnProperty(key)) continue;
				var message = errors[key],
					field = key.split('.', 2)[0];
				if (this.fields[field] !== undefined){
					this.fields[field].showError(message, key.substr(key.indexOf('.') + 1));
				} else {
					console.error(errors[key]);
				}
			}
		},
		triggerFieldsIn: function($container, eventType, params){
			$($container).find('.vof-form-row, .conv-form-row').each(function(_, row){
				var $row = $(row),
					field = $row.data('vof_field') || (this.fields && this.fields[$row.data('name')]);
				if (field && field.trigger instanceof Function) field.trigger(eventType, params);
			});
		},
		clearValues: function () {
			var values = this.getValues();
			Object.keys(values).forEach(function (key) {
				this.setValue(key, '', true);
			}.bind(this))
		}
	});

	/**
	 * $vof.Fieldset class
	 * Boundable events: change, change:field
	 * @param container
	 * @param noFieldsInit bool Don't init fields on load. Instead the field will be inited on beforeShow event
	 * @param values
	 * @constructor
	 */
	$vof.Fieldset = function(container, noFieldsInit, values){
		values = values ? values : {};
		var $container = $(container),
			$fallbackValues = $container.children('.vof-form-values'),
			fallbackValues = ($fallbackValues.length ? ($fallbackValues[0].onclick() || {}) : {});
		this.fallbackValues = {};
		this.attachFields(
			$container
				.find('> .vof-form-row,'
					+' > .vof-form-wrapper,'
					+' > .vof-form-wrapper > .vof-form-wrapper-cont > .vof-form-row'
				),
				//.not('.blocked'), // do not init blocked controls (free plan)
			noFieldsInit,
			$.extend(fallbackValues, values)
		);
	};

	$.extend($vof.Fieldset.prototype, $vof.mixins.Fieldset);
}(jQuery);
