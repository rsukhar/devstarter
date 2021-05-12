!function($){
	/**
	 * Retrieve/set/erase dom modificator class <mod>_<value> for the CSS Framework
	 * @param {String} mod Modificator namespace
	 * @param {String} [value] Value
	 * @returns {string|jQuery|boolean}
	 */
	$.fn.cfMod = function(mod, value){
		if (this.length === 0) return this;
		// Remove class modificator
		if (value === false){
			return this.each(function(){
				this.className = this.className.replace(new RegExp('(^| )' + mod + '\_[a-zA-Z0-9\_\-]+((?= )|$)', 'g'), '$2');
			});
		}
		var pcre = new RegExp('^.*?' + mod + '\_([a-zA-Z0-9\_\-]+).*?$'),
			arr;
		// Retrieve modificator
		if (value === undefined){
			return (arr = pcre.exec(this.get(0).className)) ? arr[1] : false;
		}
		// Set modificator
		else {
			var regexp = new RegExp('(^| )' + mod + '\_[a-zA-Z0-9\_\-]+( |$)');
			return this.each(function(){
				if (this.className.match(regexp)){
					this.className = this.className.replace(regexp, '$1' + mod + '_' + value + '$2');
				}
				else {
					this.className += ' ' + mod + '_' + value;
				}
			});
		}
	};

	$.fn.slideUpRow = function(callback){
		setTimeout(function(){
			this.children('td').animate({paddingTop: 0, paddingBottom: 0, borderTopWidth: 0, borderBottomWidth: 0}, 500)
				.wrapInner('<div />')
				.children()
				.slideUp(500);
			// Not attaching to slideUp, as we need it to be fired just once
			if (callback instanceof Function) setTimeout(callback, 500)
		}.bind(this), 350);
		return this;
	};
	$.fn.slideDownRow = function(callback){
		var $tds = this.children('td').wrapInner('<div />'),
			$divs = $tds.children().hide(),
			atts = $tds.css(['paddingTop', 'paddingBottom', 'borderTopWidth', 'borderBottomWidth']);
		$tds.css({paddingTop: 0, paddingBottom: 0, borderTopWidth: 0, borderBottomWidth: 0});
		this.show();
		$divs.slideDown(500);
		$tds.animate(atts);
		// Not attaching to slideUp, as we need it to be fired just once
		setTimeout(function(){
			$tds.css({paddingTop: '', paddingBottom: '', borderTopWidth: '', borderBottomWidth: ''});
			// Unwrapping
			$divs.each(function(){
				this.parentNode.innerHTML = this.innerHTML;
			});
			if (callback instanceof Function) callback();
		}.bind(this), 500);
		return this;
	};

	$.fn.bindFirst = function(name, fn){
		var elem, handlers, i, _len;
		this.bind(name, fn);
		for (i = 0, _len = this.length; i < _len; i++){
			elem = this[i];
			handlers = jQuery._data(elem).events[name.split('.')[0]];
			handlers.unshift(handlers.pop());
		}
	};

	/**
	 * Show errors from API request in some particular form
	 * @param errors
	 */
	$.fn.showErrors = function(errors){
		// Cleaning previous errors at first
		this.find('.g-form-row.check_wrong .g-form-row-state').html('');
		this.find('.g-form-row.check_wrong').removeClass('check_wrong');
		for (var key in errors){
			if (!errors.hasOwnProperty(key)) continue;
			var $input = this.find('[name="' + key + '"]');
			if ($input.length === 0) continue;
			$input.parents('.g-form-row').addClass('check_wrong').find('.g-form-row-state').html(errors[key]);
		}
	};

}(jQuery);


// Determine whether a variable is empty
function empty(mixed_var){
	return (mixed_var === "" || mixed_var === 0 || mixed_var === "0" || mixed_var === null || mixed_var === false || (typeof mixed_var === 'object' && mixed_var.length === 0));
}


/**
 * Globally available Convertful helpers
 */
!function($){
	if (window.$cf === undefined) window.$cf = {};

	$cf.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
	$cf.isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);

	// jQuery objects of commonly used DOM-elements
	$cf.$window = $(window);
	$cf.$document = $(document);
	$cf.$html = $(document.documentElement).toggleClass('no-touch', !$cf.isMobile);
	$cf.$head = $(document.head);
	$cf.$body = $(document.body);
	// Empty image for COF
	// http://stackoverflow.com/questions/5775469/whats-the-valid-way-to-include-an-image-with-no-src
	// Use "//:0" is buggy in FF
	$cf.emptyImage = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";

	if ($cf.mixins === undefined) $cf.mixins = {};

	// Helpers
	if ($cf.helpers === undefined) $cf.helpers = {};
	$cf.helpers.escapeHtml = function(text){
		if (typeof text != 'string') return '';
		var map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
		return text.replace(/[&<>"']/g, function(m){
			return map[m];
		});
	};
	$cf.helpers.stripTags = function(text){
		return text.replace(/(<([^>]+)>)/ig, '');
	};
	$cf.helpers.urlencode = function(str){
		//       discuss at: http://locutus.io/php/urlencode/
		//      original by: Philip Peterson
		//      improved by: Kevin van Zonneveld (http://kvz.io)
		//      improved by: Kevin van Zonneveld (http://kvz.io)
		//      improved by: Brett Zamir (http://brett-zamir.me)
		//      improved by: Lars Fischer
		//         input by: AJ
		//         input by: travc
		//         input by: Brett Zamir (http://brett-zamir.me)
		//         input by: Ratheous
		//      bugfixed by: Kevin van Zonneveld (http://kvz.io)
		//      bugfixed by: Kevin van Zonneveld (http://kvz.io)
		//      bugfixed by: Joris
		// reimplemented by: Brett Zamir (http://brett-zamir.me)
		// reimplemented by: Brett Zamir (http://brett-zamir.me)
		//           note 1: This reflects PHP 5.3/6.0+ behavior
		//           note 1: Please be aware that this function
		//           note 1: expects to encode into UTF-8 encoded strings, as found on
		//           note 1: pages served as UTF-8
		//        example 1: urlencode('Kevin van Zonneveld!')
		//        returns 1: 'Kevin+van+Zonneveld%21'
		//        example 2: urlencode('http://kvz.io/')
		//        returns 2: 'http%3A%2F%2Fkvz.io%2F'
		//        example 3: urlencode('http://www.google.nl/search?q=Locutus&ie=utf-8')
		//        returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3DLocutus%26ie%3Dutf-8'

		str = (str + '');

		// Tilde should be allowed unescaped in future versions of PHP (as reflected below),
		// but if you want to reflect current
		// PHP behavior, you would need to add ".replace(/~/g, '%7E');" to the following.
		return encodeURIComponent(str)
			.replace(/!/g, '%21')
			.replace(/'/g, '%27')
			.replace(/\(/g, '%28')
			.replace(/\)/g, '%29')
			.replace(/\*/g, '%2A')
			.replace(/%20/g, '+')
	};
	$cf.helpers.urldecode = function(str){
		return decodeURIComponent((str + '')
			.replace(/%(?![\da-f]{2})/gi, function(){
				// PHP tolerates poorly formed escape sequences
				return '%25'
			})
			.replace(/\+/g, '%20'))
	};
	$cf.helpers.equals = function(obj1, obj2) {
		function _equals(obj1, obj2) {
			var clone = $.extend(true, {}, obj1),
				cloneStr = JSON.stringify(clone);
			return cloneStr === JSON.stringify($.extend(true, clone, obj2));
		}

		return _equals(obj1, obj2) && _equals(obj2, obj1);
	};

	$cf.helpers.updateHistoryState = function(url){
		if (!window.history && !history.pushState) return;
		window.history.pushState(null, null, url);
	};

	$cf.helpers.ruPluralForm = function(n, forms){
		n = parseInt(n);
		return (n%10 == 1 && n%100 != 11 ? forms[0] : (n%10 >= 2 && n%10 <= 4 && (n%100<10 || n%100>=20) ? forms[1] : forms[2]))
	};

	$cf.helpers.formatNumber = function(number, digits){
		digits = (digits === undefined) ? 2 : digits;
		return parseFloat((number+'').replace(' ', '')).toLocaleString('ru-RU', {
			minimumFractionDigits: digits, maximumFractionDigits: digits
		});
	};

	// Транслитерация
	var translitPredefined = {
			'главная': 'index', 'главная страница': 'index', 'компания': 'company', 'о компании': 'company',
			'о нас': 'about', 'контакты': 'contacts', 'контактная информация': 'contacts', 'товары': 'products',
			'услуги': 'services', 'цены': 'prices', 'статьи': 'articles', 'новости': 'news', 'команда': 'team',
			'сотрудники': 'staff', 'история': 'history', 'события': 'events'
		},
		translitRu = "абвгдеёжзийклмнопрстуфхцчшщьыъэюя",
		translitEn = ['a','b','v','g','d','e','jo','zh','z','i','j','k','l','m','n','o','p','r','s','t','u','f',
			'h','c','ch','sh','sch','','y','','e','u','ya'];
	$cf.helpers.translit = function(ruValue){
		if (typeof ruValue !== 'string') return ruValue;
		ruValue = ruValue.toLowerCase().trim();
		if (translitPredefined[ruValue] !== undefined) return translitPredefined[ruValue];

		ruValue = ruValue.replace(/[^а-яёa-z0-9\-]+/ig, '-').replace(/\-{2,}/ig, '-').replace(/\-$|^\-/ig, '');
		var enValue = [];
		for(var i = 0, l = ruValue.length; i < l; i++){
			var s = ruValue.charAt(i), n = translitRu.indexOf(s);
			enValue[enValue.length] = (n >= 0) ? translitEn[n] : s;
		}
		return enValue.join('');
	};

	/**
	 * Copying value to clipboard
	 * @param {string} value
	 */
	$cf.copyToClipboard = function (value) {
		var el = document.createElement('textarea');
		el.value = value;
		el.setAttribute('readonly', '');
		el.style.position = 'absolute';
		el.style.left = '-9999px';
		document.body.appendChild(el);
		var selected = document.getSelection().rangeCount > 0 ? document.getSelection().getRangeAt(0) : false;
		el.select();
		document.execCommand('copy');
		document.body.removeChild(el);
		if (selected) {
			document.getSelection().removeAllRanges();
			document.getSelection().addRange(selected);
		}
	};

	$cf.countDropdownPlace = function($dropdown, $button, offset){
		var offset = offset || 0,
			dropdownW = $dropdown.width(),
			dropdownH = $dropdown.height(),
			buttonW = $button.width(),
			buttonOffset = $button[0].getBoundingClientRect(),
			align = $dropdown.data('align') || '',
			valign;
		if (align === ''){
			if (buttonOffset.left + buttonW / 2 + dropdownW / 2 > $cf.$window.width()){
				align = 'right';
			}
			if (buttonOffset.left < (dropdownW / 2 - buttonW / 2)){
				align = 'left';
			}
		}
		if (buttonOffset.top > (dropdownH + offset) && (buttonOffset.bottom + offset + dropdownH) > $cf.$window.height()){
			valign = 'up';
		} else {
			valign = 'down';
		}
		return valign + ((align === 'center') ? '' : align);
	};

	/**
	 * Class mutator, allowing bind, unbind, and trigger class instance events
	 * @type {{}}
	 */
	$cf.mixins.Events = {
		/**
		 * Attach a handler to an event for the class instance
		 * @param {String} eventType A string containing event type, such as 'beforeShow' or 'change'
		 * @param {Function} handler A function to execute each time the event is triggered
		 */
		bind: function(eventType, handler){
			if (this.$$events === undefined) this.$$events = {};
			if (this.$$events[eventType] === undefined) this.$$events[eventType] = [];
			this.$$events[eventType].push(handler);
			return this;
		},
		/**
		 * Remove a previously-attached event handler from the class instance
		 * @param {String} eventType A string containing event type, such as 'beforeShow' or 'change'
		 * @param {Function} [handler] The function that is to be no longer executed.
		 * @chainable
		 */
		unbind: function(eventType, handler){
			if (this.$$events === undefined || this.$$events[eventType] === undefined) return this;
			if (handler !== undefined){
				var handlerPos = $.inArray(handler, this.$$events[eventType]);
				if (handlerPos != -1){
					this.$$events[eventType].splice(handlerPos, 1);
				}
			} else {
				this.$$events[eventType] = [];
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
			if (this.$$events === undefined || this.$$events[eventType] === undefined || this.$$events[eventType].length == 0) return this;
			for (var index = 0; index < this.$$events[eventType].length; index++){
				this.$$events[eventType][index].apply(this, extraParameters);
			}
			return this;
		}
	};
	/**
	 * Load module
	 * @param {string} file
	 * @param {function} callback
	 */
	$cf.require = function(file, callback) {
		if( ! file) return;
		var ext = file.match(/\.([js|css]{2,3})(?:[\?#]|$)/i);
			ext = ext && ext[1];
		if (ext === null) return;
		var $el = $(ext === 'js' ? '<script>' : '<link>'),
			$elms = $($el[0].tagName.toLowerCase());
		if(ext === 'css') {
			if ($elms.filter('[href^="'+file+'"]').length) return;
			$el.attr({type: 'text/css', rel: 'stylesheet', href: file});
			$('head').append($el);
		} else {
			if ($elms.filter('[src^="'+file+'"]').length) return;
			$el.attr({type: 'text/javascript', src: file});
			$('body').append($el);
		}
		$el.one('load', callback);
	};
}(jQuery);

/**
 * Events
 */
!function($){

	var disabledActions = [],
		superProps = {},
		config = {
			trackChargeEnabled: false
		};

	$cf.addPropsToAllTrackedEvents = function(props){
		$.extend(superProps, props);
	};

	$cf.setTrackingConfig = function(cfg){
		$.extend(config, cfg);
	};

	$cf.trackEvent = function(action, props, callback, once){
		// Temporary debug wrapper
		if (disabledActions.indexOf(action) !== -1){
			// Tracking disabled
			return (callback instanceof Function) ? callback() : null;
		}
		// Providing fault tolerance
		var callbackWrapper = function(){
			if (callback instanceof Function) callback();
			callback = undefined;
		};
		if (callback instanceof Function) setTimeout(callbackWrapper, 500);
		$.extend(props, superProps);
		if (action === 'Page View'){
			if (window.ga instanceof Function) ga('send', 'pageview');
			if (window.fbq instanceof Function) fbq('track', 'PageView');
		}
		if (action === 'Payment Complete' && props.$amount){
			// Facebook
			if (window.fbq instanceof Function) fbq('track', 'Purchase', {
				value: props['Original Amount'],
				currency: props['Original Currency'],
				content_ids: props['Plan']
			});
		}
		if (action === 'Sign Up'){
			if (window.fbq instanceof Function) fbq('track', 'CompleteRegistration');
		}
		if (action === 'Checkout Start'){
			if (window.fbq instanceof Function) fbq('track', 'InitiateCheckout');
		}
		if (once) disabledActions.push(action);
	};

	$cf.trackLinks = function($links, action, props){
		$links = ($links instanceof jQuery) ? $links : $($links);
		$links.each(function(_, link){
			var $link = $(link);
			$link.on('click', function(e){
				// Links, opening in new window won't interrupt event tracking
				if ($link.attr('target') === '_blank') return $cf.trackEvent(action, props);
				e.preventDefault();
				$cf.trackEvent(action, props, function(){
					window.location = $link.attr('href');
				});
			});
		});
	};

}(jQuery);

/**
 * Accordion
 */
!function($){
	"use strict";
	var CFAccordion = window.CFAccordion = function(container){
		this.$container = $(container);
		this.$titles = this.$container.find('.b-accordion-title');
		this.$content = this.$container.find('.b-accordion-content');
		this.$container.find('.b-accordion-title').on('click', function(e){
			var $el = $(e.currentTarget),
				$content = $el.next();
			if (!$el.hasClass('is-active')){
				this.$titles.removeClass('is-active');
				this.$content.slideUp();
				$el.addClass('is-active');
				$content.slideDown();
			}
		}.bind(this));
	};
}(jQuery);

/**
 * Alert
 */
!function($){
	"use strict";
	var CFAlert = window.CFAlert = function(container){
		this.$container = $(container);
		this.$container.find('.g-alert-closer').on('click', this.hide.bind(this));
	};
	$.extend(CFAlert.prototype, {
		show: function() {
			this.$container.slideDown();
		},
		hide: function() {
			this.$container.slideUp();
		}
	});
}(jQuery);

/**
 * Modal Popup
 */
!function($){
	"use strict";
	var CFPopup = window.CFPopup = function(container){
		this.$container = $(container);
		this._events = {
			resize: this.resize.bind(this),
			keypress: function(e){
				if (e.keyCode == 27) this.hide();
			}.bind(this)
		};
		this.isFixed = !$cf.isMobile;
		this.$wrap = this.$container.find('.b-popup-wrap:first')
			.cfMod('pos', this.isFixed ? 'fixed' : 'absolute')
			.on('click', function(e){
				if (!$.contains(this.$box[0], e.target) && !$(e.target).hasClass('disable_wrapper_click')) this.hide();
			}.bind(this));
		this.$box = this.$container.find('.b-popup-box:first');
		this.$overlay = this.$container.find('.b-popup-overlay:first')
			.cfMod('pos', this.isFixed ? 'fixed' : 'absolute');
		this.$box.on('click', '.action_hidepopup', this.hide.bind(this));
		this.timer = null;
	};
	$.extend(CFPopup.prototype, $cf.mixins.Events, {
		_hasScrollbar: function(){
			return document.documentElement.scrollHeight > document.documentElement.clientHeight;
		},
		_getScrollbarSize: function(){
			if ($cf.scrollbarSize === undefined){
				var scrollDiv = document.createElement('div');
				scrollDiv.style.cssText = 'width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;';
				document.body.appendChild(scrollDiv);
				$cf.scrollbarSize = scrollDiv.offsetWidth - scrollDiv.clientWidth;
				document.body.removeChild(scrollDiv);
			}
			return $cf.scrollbarSize;
		},
		show: function(){
			clearTimeout(this.timer);
			this.$overlay.appendTo($cf.$body).show();
			this.$wrap.appendTo($cf.$body).show();
			// Load iframe
			this.$wrap.find('iframe[data-src]').each(function(){
				var url = $(this).data('src');
				var owner = $('.vof-container').data('owner') || $('.b-builder').data('owner') || $('.b-main').data('owner');
				if (owner !== 0)
					url = url + '?owner_id=' + owner;
				$(this).attr('src', url);
			});
			this.resize();
			if (this.isFixed){
				$cf.$html.addClass('overlay_fixed');
				// Storing the value for the whole popup visibility session
				this.windowHasScrollbar = this._hasScrollbar();
				if (this.windowHasScrollbar && this._getScrollbarSize()) $cf.$html.css('margin-right', this._getScrollbarSize());
			} else {
				this.$overlay.css({
					height: $cf.$document.height()
				});
				this.$wrap.css('top', $cf.$window.scrollTop());
			}
			$cf.$body.on('keypress', this._events.keypress);
			this.timer = setTimeout(this.afterShow.bind(this), 25);
			this.trigger('show', []);
		},
		afterShow: function(){
			clearTimeout(this.timer);
			this.$overlay.addClass('active');
			this.$box.addClass('active');
			$cf.$window.trigger('resize');
			$cf.$window.on('resize', this._events.resize);
		},
		hide: function(){
			clearTimeout(this.timer);
			$cf.$window.off('resize', this._events.resize);
			$cf.$body.off('keypress', this._events.keypress);
			this.$overlay.removeClass('active');
			this.$box.removeClass('active');
			// Closing it anyway
			this.timer = setTimeout(this.afterHide.bind(this), 500);
			this.trigger('hide', []);
		},
		afterHide: function(){
			clearTimeout(this.timer);
			// TODO: bug with iframe here. By reason of re-appending the iframe content will be reload. For example its effect on the youtube iframe
			// and little fix for elFinder
			this.$wrap.find('iframe[data-src]').attr('src', '');
			this.$overlay.appendTo(this.$container).hide();
			this.$wrap.appendTo(this.$container).hide();
			if (this.isFixed){
				$cf.$html.removeClass('overlay_fixed');
				if (this.windowHasScrollbar) $cf.$html.css('margin-right', '');
			}
		},
		resize: function(){
			var animation = this.$box.cfMod('animation'),
				padding = parseInt(this.$box.css('padding-top')),
				winHeight = $cf.$window.height(),
				popupHeight = this.$box.height();
			if (!this.isFixed) this.$overlay.css('height', $cf.$document.height());
			this.$box.css('top', Math.max(0, (winHeight - popupHeight) / 2 - padding));
		}
	});
}(jQuery);

/**
 * Filters
 */
!function($){
	"use strict";
	var CFFilters = window.CFFilters = function(container, callback){
		this.$container = $(container);
		if (!this.$container.length) return;
		this.$filters = this.$container.find('.b-filter-h');
		this.searchTimeout = '';

		this._events = {
			toggleFilters: function(e){
				var $target = $(e.target);
				$target.toggleClass('active');
				this.$filters.toggleClass('active')
			}.bind(this)
		};

		this.searchPlaceCaretAtEnd();

		this.$container.on('click', '.b-filter-trigger', function(e){
			this._events.toggleFilters(e);
		}.bind(this));

		// Filters field
		this.$container.find('.b-filter-item .vof-form-row').each(function(_, item){
			var field = new $vof.Field($(item));
			field.on('change', function(value){
				value = value.join('|');
				callback(field.name, value);
			})
		});

		// Search field
		this.$container.find('.b-filter-search .vof-form-row').each(function(_, item){
			var field = new $vof.Field($(item));
			field.$input.on('keyup', function(e){
				var value = $(e.target).val();
				clearTimeout(this.searchTimeout);
				if (!value.length || value.length > 2)
				{
					this.searchTimeout = setTimeout(function() {
						callback(field.name, value);
					}.bind(this), 500);
				}
			}.bind(this))
		});
	};
	CFFilters.prototype = {
		searchPlaceCaretAtEnd : function(){
			var $searchField = this.$container.find('.b-filter-search input'),
				value;
			if ( ! $searchField.length) return;
			value = $searchField.val();
			if (value.length) {
				$searchField.focus();
				$searchField.val('');
				$searchField.val(value);
			}
		},
	};
}(jQuery);

!function($){
	"use strict";
	var Assignee = window.Assignee = function(container, callback){
		this.$container = $(container);
		if (!this.$container.length) return;
		this.$window = $(window);
		this.container = container;
		this.callback = callback;
		this._events = {
			togglePopup: function(e){
				if ($(e.target).hasClass('b-assignee-popup-item')) return;
				var $popup = $(e.target).parent().find('.b-assignee-popup');
				this.togglePopup($popup);
			}.bind(this),
			popupItemClick: function(e){
				var $popupItem = $(e.target),
					$title = $popupItem.closest('.b-assignee-trigger').find('span'),
					$parent = $popupItem.parent(),
					assignedId = $popupItem.data('value'),
					$activeItem = $parent.find('.is-active');

				if ($activeItem.length > 0) {
					$activeItem.removeClass('is-active');
				}
				$popupItem.addClass('is-active');
				$title.html($popupItem.html());
				callback(assignedId, $parent, this)
			}.bind(this)
		};

		this.$container.find('.b-assignee-trigger').each(function(_, trigger){
			var $trigger = $(trigger);
			$trigger.on('click', this._events.togglePopup);
		}.bind(this));

		this.$container.find('.b-assignee-popup-item').each(function(_, popupItem){
			var $popupItem = $(popupItem);
			$popupItem.on('click', this._events.popupItemClick);
		}.bind(this));
	};
	Assignee.prototype = {
		togglePopup: function(target){
			(target.hasClass('is-active')) ? this.hidePopup(target) : this.showPopup(target);
		},
		hidePopup: function(target){
			target.hide();
			target.removeClass('is-active');
			this.$window.off('mouseup touchstart mousewheel DOMMouseScroll touchstart', this._events.hidePopup);
		},
		showPopup: function(target){
			target.addClass('is-active');
			target.show();
			this._events.hidePopup = function(e){
				if (target.parent().has(e.target).length !== 0) return;
				e.stopPropagation();
				e.preventDefault();
				this.hidePopup(target);
			}.bind(this);
			this.$window.on('mouseup touchstart mousewheel DOMMouseScroll touchstart', this._events.hidePopup);
		},
	}
}(jQuery);

jQuery(function($){
	/**
	 * Title editor
	 * @param container mixed
	 * @constructor
	 */
	$cf.TitleEditor = function(container){

		this.$container = $(container);
		this.$container_class = this.$container.attr('class');
		this.$text = this.$container.find('.'+this.$container_class+'-text');
		this.$form = this.$container.find('.'+this.$container_class+'-input');
		this.$error = this.$container.find('.'+this.$container_class+'-error');
		this.$errorH = this.$error.find('.'+this.$container_class+'-error-h');

		this.fixWidth = function () {
			this.$input.css('width', this.$text.outerWidth());
		};

		this._events = {
			submit: function(e){
				e.stopPropagation();
				e.preventDefault();
				var oldValue = this.$input.data('oldvalue') || '',
					newValue = this.$input.val().trim();
				if (oldValue == newValue) return;
				this.$input
					.data('oldvalue', newValue)
					.off('blur', this._events.submit)
					.trigger('blur');
				this.submit();
			}.bind(this)
		};
		this.$input = this.$form.find('input[type="text"]')
			.on('focus', function () {
				this.$container.addClass('is-active');
				this.fixWidth();
				if ( ! $cf.isSafari) {
					this.$input[0].select();
				}
			}.bind(this))
			.on('blur', function () {
				if ( ! $cf.isSafari && this.$input[0].setSelectionRange) {
					this.$input[0].setSelectionRange(0, 0);
				}
				this.fixWidth();
				this.$container.removeClass('is-active');
			}.bind(this))
			.on('change keyup paste', function(e){
				this.$input
					.off('blur', this._events.submit)
					.one('blur', this._events.submit);
				if (e.type === 'keyup') {
					// Esc key
					if (e.which === 27) {
						this.$input
							.val(this.$input.data('oldvalue'))
							.trigger('blur');
					}
					else return;
				}
				this.$text.html(this.$input.val().replace(/ /g, '&nbsp;'));
				this.fixWidth();
			}.bind(this));
		this.$text.html(this.$input.val().replace(/ /g, '&nbsp;'));
		this.fixWidth();
		this.$form.on('submit', this._events.submit);

		this.showError = function(message){
			// this.$container.addClass('check_wrong');
			this.$errorH.html(message);
		};

		this.$container.addClass('is-editable');
	};
});


jQuery(function($){
	// Fixed titlebar

	var $titlebar = $('.b-titlebar'),
		$titlebarFixed = $titlebar.find('.b-titlebar-fixed');
	if ($titlebarFixed.length){
		var _titlebarOffset = $titlebarFixed.offset().top + ($titlebarFixed.data('offset') ? $titlebarFixed.data('offset') : 0);
		$(window).on('scroll', function(){
			$titlebarFixed.closest('.b-titlebar').toggleClass('is-fixed', (window.pageYOffset > _titlebarOffset));
		});
	}

	// :hover  menu fix for mobile phones
	$('.b-menu-item.has_dropdown, .b-switcher-h').on('click touch', function(e){
		var $this = $(this);
		if (!$this.hasClass('is_active') && $cf.isMobile){
			e.preventDefault();
			$this.addClass('is_active');
			setTimeout(function(){
				$('body').one('click touch', function(e){
					$this.removeClass('is_active');
				});
			}, 10);
		}
	});
});

// Tabs
!function($){
	"use strict";
	var VOFTabs = window.VOFTabs = function(container, callback){
		this.$container = $(container);
		if (!this.$container.length) return;

		this.$container.on('click', '.g-tabs-item', function (e) {
			var id = $(e.target).data('tabId') || $(e.target).closest('.g-tabs-item').data('tabId'),
				$tabs = $(e.target).closest('.g-tabs'),
				$tabsH = $(e.target).closest('.g-tabs-h');
			if (typeof id !== 'undefined') {
				$($tabs).children('.g-tabs-section').removeClass('is-active');
				$tabsH.find('.g-tabs-item').removeClass('is-active');
				$tabs.find('.g-tabs-item[data-tab-id="'+id+'"]').addClass('is-active');
				$tabs.find('.g-tabs-section[data-tab-id="'+id+'"]').addClass('is-active');
			}
			callback(id, $tabs);
		}.bind(this));
	};
	VOFTabs.prototype = {};
}(jQuery);

/**
 * CSS-analog of jQuery slideDown/slideUp/fadeIn/fadeOut functions (for better rendering)
 */
!function($){
	/**
	 * Remove the passed inline CSS attributes.
	 *
	 * Usage: $elm.resetInlineCSS('height', 'width');
	 */
	$.fn.resetInlineCSS = function(){
		for (var index = 0; index < arguments.length; index++){
			this.css(arguments[index], '');
		}
		return this;
	};

	$.fn.clearPreviousTransitions = function(){
		// Stopping previous events, if there were any
		var prevTimers = (this.data('animation-timers') || '').split(',');
		if (prevTimers.length >= 2){
			this.resetInlineCSS('transition', '-webkit-transition');
			prevTimers.map(clearTimeout);
			this.removeData('animation-timers');
		}
		return this;
	};
	/**
	 *
	 * @param {Object} css key-value pairs of animated css
	 * @param {Number} duration in milliseconds
	 * @param {Function} onFinish
	 * @param {String} easing CSS easing name
	 * @param {Number} delay in milliseconds
	 */
	$.fn.performCSSTransition = function(css, duration, onFinish, easing, delay){
		duration = duration || 250;
		delay = delay || 25;
		easing = easing || 'ease-in-out';
		var $this = this,
			transition = [];

		this.clearPreviousTransitions();

		for (var attr in css){
			if (!css.hasOwnProperty(attr)) continue;
			transition.push(attr + ' ' + (duration / 1000) + 's ' + easing);
		}
		transition = transition.join(', ');
		$this.css({
			transition: transition,
			'-webkit-transition': transition
		});

		// Starting the transition with a slight delay for the proper application of CSS transition properties
		var timer1 = setTimeout(function(){
			$this.css(css);
		}, delay);

		var timer2 = setTimeout(function(){
			$this.resetInlineCSS('transition', '-webkit-transition');
			if (typeof onFinish == 'function') onFinish();
		}, duration + delay);

		this.data('animation-timers', timer1 + ',' + timer2);
	};
	// Height animations
	$.fn.slideDownCSS = function(duration, onFinish, easing, delay){
		if (this.length === 0) return;
		var $this = this;
		this.clearPreviousTransitions();
		// Grabbing paddings
		this.resetInlineCSS('padding-top', 'padding-bottom');
		var timer1 = setTimeout(function(){
			var paddingTop = parseInt($this.css('padding-top')),
				paddingBottom = parseInt($this.css('padding-bottom'));
			// Grabbing the "auto" height in px
			$this.css({
				visibility: 'hidden',
				position: 'absolute',
				height: 'auto',
				'padding-top': 0,
				'padding-bottom': 0,
				display: 'block'
			});
			var height = $this.height();
			$this.css({
				overflow: 'hidden',
				height: '0px',
				visibility: '',
				position: '',
				opacity: 0
			});
			$this.performCSSTransition({
				height: height + paddingTop + paddingBottom,
				opacity: 1,
				'padding-top': paddingTop,
				'padding-bottom': paddingBottom
			}, duration, function(){
				$this.resetInlineCSS('overflow').css('height', 'auto');
				if (typeof onFinish == 'function') onFinish();
			}, easing, delay);
		}, 25);
		this.data('animation-timers', timer1 + ',null');
	};
	$.fn.slideUpCSS = function(duration, onFinish, easing, delay){
		if (this.length === 0) return;
		this.clearPreviousTransitions();
		this.css({
			height: this.outerHeight(),
			overflow: 'hidden',
			'padding-top': this.css('padding-top'),
			'padding-bottom': this.css('padding-bottom'),
			opacity: 1
		});
		var $this = this;
		this.performCSSTransition({
			height: 0,
			'padding-top': 0,
			'padding-bottom': 0,
			opacity: 0
		}, duration, function(){
			$this.resetInlineCSS('overflow', 'padding-top', 'padding-bottom', 'opacity').css({
				display: 'none'
			});
			if (typeof onFinish == 'function') onFinish();
		}, easing, delay);
	};
	$.fn.reverse = function() {
		return this.pushStack(this.get().reverse(), arguments);
	};
}(jQuery);

/**
 * g-form accordion
 */
(function($){
	$('.g-form-field-accordion-title').on('click', function(e){
		$(e.target).parent().toggleClass('is-active');
	});
})(jQuery);

/**
 * Convert a multi-dimensional object|array into a single-dimensional array.
 * @param obj array|object
 * @return array
 */
function arrayFlatten(obj){
	var arr = (obj instanceof Array) ? obj : Object.values(obj);
	return arr.reduce(function(result, value){
		return result.concat((typeof value === 'object') ? arrayFlatten(value) : value);
	}, []);
}

/**
 * Set a value on array by path
 * @param obj Object
 * @param path string|Array
 * @param value mixed|undefined
 */
function arraySetPath(obj, path, value){
	var keys = (path instanceof Array) ? path : path.split('.');
	// Providing the path
	for (var i = 0; i < keys.length - 1; i++){
		var key = keys[i];
		if (typeof obj[key] !== 'object' || obj[key] === null) obj[key] = {};
		obj = obj[key];
	}
	// Setting the value
	if (value === undefined){
		delete obj[keys[keys.length - 1]];
	} else {
		obj[keys[keys.length - 1]] = value;
	}
}

/**
 * Gets a value from an array using a dot separated path.
 * @param obj Object
 * @param path string|Array
 * @param [def] mixed Default value
 * @return mixed
 */
function arrayPath(obj, path, def){
	var keys = (path instanceof Array) ? path : path.split('.'),
		value = obj;
	for (var i = 0; i < keys.length; i++){
		var key = keys[i];
		if (value[key] === undefined) return def;
		value = value[key];
	}
	return value;
}

function arrayPathKeys(obj){
	var keys = [];
	for (var key in obj){
		if (typeof obj[key] === "object"){
			var subkeys = arrayPathKeys(obj[key]);
			keys = keys.concat(subkeys.map(function(subkey){
				return key + "." + subkey;
			}));
		} else {
			keys.push(key);
		}
	}
	return keys;
}

// eslint-disable-line camelcase
//  discuss at: http://locutus.io/php/array_combine/
// original by: Kevin van Zonneveld (http://kvz.io)
// improved by: Brett Zamir (http://brett-zamir.me)
//   example 1: array_combine([0,1,2], ['kevin','van','zonneveld'])
//   returns 1: {0: 'kevin', 1: 'van', 2: 'zonneveld'}
function arrayCombine (keys, values) {
	var newArray = {};
	var i;

	// input sanitation
	// Only accept arrays or array-like objects
	// Require arrays to have a count
	if (typeof keys !== 'object') {
		return false
	}
	if (typeof values !== 'object') {
		return false
	}
	if (typeof keys.length !== 'number') {
		return false
	}
	if (typeof values.length !== 'number') {
		return false
	}
	if (!keys.length) {
		return false
	}

	// number of elements does not match
	if (keys.length !== values.length) {
		return false
	}

	for (i = 0; i < keys.length; i++) {
		newArray[keys[i]] = values[i]
	}

	return newArray
}

// Storage driver: Browser HTML5 Storage
!function(window, undefined){
	if (window.localStorage === undefined) return;
	var localStore = {
		/**
		 * Set value by key
		 * @param string key
		 * @param mixed value
		 * @return boolean
		 */
		set: function(key, value){
			if(value === undefined || typeof value === "function") {
				value += '';
			} else {
				try {
					value = JSON.stringify(value);
				} catch(e) {}
			}
			try {
				localStorage[(value !== null && value !== undefined) ? 'setItem' : 'removeItem'](key, value);
				return true;
			} catch (e) {
				return false;
			}
		},
		/**
		 * Get value by key
		 * @param string key
		 * @param mixed defaultValue
		 * @return mixed
		 */
		get: function(key, defaultValue) {
			var value = localStorage.getItem(key);
			if(value === null || value === undefined) {
				return defaultValue || '';
			}
			try {
				return JSON.parse(value);
			} catch(e) {
				return value;
			}
		}
	};
	try {
		// Checking first if localStorage is available
		localStore.set('', 1);
		localStore.set('', null);
		localStore.set('', {});
		window.localStore = localStore;
	} catch (e) {}
}(window);

jQuery(function($){
	// Setting focus to the target element
	$('.i-setfocus input[type="text"], .i-setfocus input[type="password"], .i-setfocus textarea').focus();

	// Инициализируем все VOF-формы, которые расчитаны на это
	if (window.$vof) $('.i-form.i-autoinit').each(function(_, container){
		new $vof.Fieldset(container);
	});

});
