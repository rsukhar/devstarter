<?php
/**
 * @var string $title Current page title
 *
 * @var array $menu_items [{url} => {title}, ...]
 * @var array $switcher_items [{url} => {title}, ...]
 *
 * @var array $subheader_items [{url} => {title}, ...]
 *
 * @var null|string $active_menu_item
 * @var null|string $submenu_active_item
 * @var string $body_classes
 * @var string $header_exists
 * @var string $footer_exists
 * @var View $content Page-related content
 * @var Model_User $user Current user (if logged in)
 * @var Model_User $owner Current user (if logged in)
 */
$active_menu_item = isset($active_menu_item) ? $active_menu_item : NULL;
$submenu_active_item = isset($submenu_active_item) ? $submenu_active_item : NULL;
$body_classes = (isset($body_classes) AND ! empty($body_classes)) ? (' '.$body_classes) : '';
$header_exists = (isset($header_exists) AND $header_exists);
$footer_exists = (isset($footer_exists) AND $footer_exists);
$menu_items = $menu_items ?? [];
?>
<!DOCTYPE html>
<html xml:lang="ru" lang="ru">
<head>
	<meta charset="UTF-8">
	<title><?php echo isset($title) ? ($title.' — ') : '' ?>ВсеКолёса</title>

	<?php echo Assets::instance('admin')->output(2, TRUE, FALSE) ?>

	<meta name="referrer" content="never">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>
<body class="b-body<?php echo $body_classes ?>">
<div class="b-canvas">

	<?php if ($header_exists): ?>
		<header class="b-header">
			<div class="b-header-h">
				<a href="/" class="b-logo">Все Колёса</a>

				<div class="b-menu">

					<?php if (isset($menu_items) AND ! empty($menu_items)): ?>
						<?php echo View::factory('admin/helpers/dropdown_menu', [
							'items' => $menu_items,
						]) ?>
					<?php endif; ?>
					<?php if (isset($switcher_items) AND ! empty($switcher_items)): ?>
						<?php echo View::factory('admin/helpers/dropdown_menu', [
							'items' => $switcher_items,
							'preserve_params' => TRUE,
							'style' => 'large',
						]) ?>
					<?php endif; ?>

				</div>

				<div class="b-menu pos_right">

<!--					<div class="b-menu-item">
						<a href="#"><span>Справка</span></a>
					</div>-->

					<div class="b-menu-item has_dropdown">
						<a href="/admin/users/<?php echo HTML::chars($user->username) ?>/">
							<span><?php echo ($user->role === 'customer') ? HTML::chars($user->username) : HTML::chars($user->full_name) ?></span>
						</a>
						<div class="b-menu-list">
							<a href="/admin/users/<?php echo HTML::chars($user->username) ?>"><span>Аккаунт</span></a>
							<a href="/sign_out/"><span>Выход</span></a>
						</div>
					</div>

				</div>

			</div>
		</header>
	<?php endif; ?>

	<?php echo (isset($subheader)) ? $subheader : '' ?>

	<?php
	if ( ! isset($content->title) AND isset($title))
	{
		$content->title = $title;
	}
	echo $content;
	?>

	<?php echo isset($global_content) ? $global_content : '' ?>

</div>

<?php if ($footer_exists): ?>
	<div class="b-footer">
		<div class="b-footer-h">
			<!-- Begin copyright -->
			<span>&copy; ВсеКолёса, <?php echo date('Y'); ?></span>

			<a href="/legal/terms/" rel="nofollow" target="_blank">Условия использования</a>
			<a href="/legal/privacy/" rel="nofollow" target="_blank">Приватность</a>
			<a href="/contacts/" rel="nofollow" target="_blank">Обратная связь</a>
			<!-- End copyright -->

		</div>
	</div>
<?php endif; ?>


<?php echo Assets::instance('admin')->output(0, FALSE, TRUE) ?>

<?php if (Auth::user_is_logged_in() AND Settings::get('admin_chat.secret_key', '')): ?>
	<script>
		jQuery(function($){
			var emailSignature = '<?php echo hash_hmac('sha256', $user->email, Settings::get('admin_chat.secret_key', '')) ?>';
			if (window.$crisp !== undefined && $crisp.push !== undefined){
				$crisp.push(['set', 'user:email', ['<?php echo $user->email ?>', emailSignature]]);
				$crisp.push(['set', 'user:nickname', ['<?php echo $user->full_name ?> (<?php echo $user->username ?>)']]);
				$crisp.push(['set', 'session:event', ['page_visited', { url: location.pathname }]]);
			}
		});
	</script>
<?php endif; ?>
<?php echo Settings::get('admin_chat.html', '') ?>

<?php if (Kohana::$profiling): ?>
	<?php echo View::factory('profiler/stats') ?>
<?php endif; ?>

</body>
</html>