<?php
/**
 * @var string $title Current page title
 *
 * @var array $menu_items [{url} => {title}, ...]
 *
 * @var bool $hide_header ?
 *
 * @var null|string $active_menu_item
 * @var null|string $submenu_active_item
 * @var string $body_classes
 * @var string $footer_exists
 * @var View $content Page-related content
 * @var Model_User $user Current user (if logged in)
 * @var Model_User $owner Current user (if logged in)
 */
$active_menu_item = isset($active_menu_item) ? $active_menu_item : NULL;
$submenu_active_item = isset($submenu_active_item) ? $submenu_active_item : NULL;
$body_classes = (isset($body_classes) AND ! empty($body_classes)) ? (' '.$body_classes) : '';
$menu_items = $menu_items ?? [];
?>
<!DOCTYPE html>
<html xml:lang="ru" lang="ru">
<head>
	<meta charset="UTF-8">
	<title><?php echo isset($title) ? ($title.' — ') : '' ?>DevStarter</title>

	<?php echo Assets::instance()->output(2, TRUE, FALSE) ?>

	<meta name="referrer" content="never">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>
<body class="b-body<?php echo $body_classes ?>">
<div class="b-canvas">

	<?php if ( ! isset($hide_header) OR ! $hide_header): ?>
		<header class="b-header">
			<div class="b-header-h">
				<a href="/" class="b-logo">DevStarter</a>

				<div class="b-menu">
					<?php if (isset($menu_items) AND ! empty($menu_items)): ?>
						<?php echo View::factory('admin/helpers/dropdown_menu', [
							'items' => $menu_items,
						]) ?>
					<?php endif; ?>
				</div>

				<?php if (isset($user)): ?>
					<div class="b-menu pos_right">
						<div class="b-menu-item has_dropdown">
							<a href="/admin/users/<?php echo HTML::chars($user->username) ?>/">
								<span><?php echo ($user->role === 'customer') ? HTML::chars($user->username) : HTML::chars($user->full_name) ?></span>
							</a>
							<div class="b-menu-list">
								<a href="/sign_out/"><span>Выход</span></a>
							</div>
						</div>
					</div>
				<?php endif; ?>

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

<div class="b-footer">
	<div class="b-footer-h">
		<span>&copy; DevStarter, <?php echo date('Y'); ?></span>
	</div>
</div>


<?php echo Assets::instance()->output(0, FALSE, TRUE) ?>

<?php if (Kohana::$profiling): ?>
	<?php echo View::factory('profiler/stats') ?>
<?php endif; ?>

</body>
</html>