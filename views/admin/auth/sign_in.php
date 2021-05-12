<?php
/**
 * @var array $errors Set of errors: field_name => error_text
 * @var array $values Values that should be put to the form
 */
?>
<div class="b-main">
	<section class="b-section">
		<div class="b-section-h">

			<div class="b-content">

				<a href="/" class="b-logo">
					<picture>
						<source media="(max-width: 599px)" srcset="/assets/img/logo_mobile.svg">
						<img src="/assets/img/logo.svg" width="170" alt="DevStarter" />
					</picture>
				</a>
				<br><br>

				<form class="g-form style_small" method="post">

					<h3>Авторизация</h3>

					<div class="g-form-row type_text <?php echo (isset($errors['login']) ? 'check_wrong' : '') ?>">
						<label for="sign_in_login" class="g-form-row-label">Email или Логин</label>
						<input type="text" name="login" id="sign_in_login" value="<?php echo HTML::chars(Arr::get($values, 'login', '')) ?>"/>
						<div class="g-form-row-state"><?php echo Arr::get($errors, 'login', '') ?></div>
					</div>
					<div class="g-form-row type_password <?php echo (isset($errors['password']) ? 'check_wrong' : '') ?>">
						<label for="sign_in_password" class="g-form-row-label">Пароль</label>
						<input type="password" name="password" id="sign_in_password" value="<?php echo HTML::chars(Arr::get($values, 'password', '')) ?>"/>
						<div class="g-form-row-state"><?php echo Arr::get($errors, 'password', '') ?></div>
					</div>
					<div class="g-form-row type_submit">
						<div class="g-btn">Войти</div>
					</div>

				</form>

			</div>

		</div>
	</section>
</div>
