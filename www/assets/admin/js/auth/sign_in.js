jQuery(function($){
	var SignIn = function(container){
		this.$container = $(container);
		this.$login = this.$container.find('input[name="login"]').focus();
		this.$password = this.$container.find('input[name="password"]');
		this.$form = this.$container.find('form');

		this.$container.find('.g-form-row.check_wrong input').each((i, input) => {
			if (i === 0) {
				var val = $(input).val();
				$(input).focus().val('').val(val);
			}

		});

		this.$container.find('input').keydown(function(e){
			if (e.which === 13) {
				e.preventDefault();
				var values = {login: this.$login.val(), password: this.$password.val()},
					field = $(e.target).attr('name');
				if (values[field].length) {
					if (field === 'login' && ! values.password.length)
						$('input[name="password"]').focus();
					else if (field === 'password' && ! values.login.length)
						$('input[name="login"]').focus();
					else
						this.login();
				}
			}
		}.bind(this));
		this.$container.find('.g-btn').on('click', function(e){
			e.preventDefault();
			this.login();
		}.bind(this));

		this.$container.find('input').on('keydown', () => {
			this.$container.find('.g-form-row.check_wrong').removeClass('check_wrong').find('.g-form-row-state').html('')
		})
	};
	SignIn.prototype = {
		login: function(){
			this.$form.submit();
		},
	};

	new SignIn('.b-body.auth-sign_in');
});