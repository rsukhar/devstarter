<?php
/**
 * @var string $title
 * @var View $sidebar
 * @var array $fields
 * @var string $button_title
 * @var array $values
 * @var array $errors
 * @var string $message
 * @var array $groups_params
 */
// При необходимости группируем поля по названию группы
$button_title = (isset($button_title) AND ! empty($button_title)) ? $button_title : 'Отправить';
$groups_params = (isset($groups_params) AND is_array($groups_params)) ? $groups_params : [];

if ( ! count(Arr::pluck($fields, 'group')))
{
	// Одна большая группа настроек
	$groups_fields = ['' => &$fields];
}
else
{
	// Несколько групп настроек
	$groups_fields = [];
	foreach ($fields as $field_name => $field)
	{
		$group_name = Arr::get($field, 'group', '');
		// Чтобы не было бесконечной рекурсии
		unset($field['group']);
		Arr::set_path($groups_fields, [$group_name, $field_name], $field);
	}
}
?>
<?php if (isset($title) AND ! empty($title)): ?>
	<div class="b-titlebar">
		<div class="b-titlebar-h"><h1><?php echo $title ?></h1></div>
	</div>
<?php endif ?>
<div class="b-main">
	<section class="b-section">
		<div class="b-section-h">
			<?php if (isset($sidebar) AND $sidebar): ?>
				<aside class="b-sidebar position-left">
					<?php echo $sidebar ?>
				</aside>
			<?php endif; ?>
			<div class="b-content">

				<?php if (isset($message) AND ! empty($message)): ?>
					<div class="g-alert type_success">
						<div class="g-alert-closer"></div>
						<div class="g-alert-body"><?php echo $message ?></div>
					</div>
				<?php endif; ?>

				<?php foreach ($groups_fields as $group_name => $single_group_fields): ?>
						<?php if ( ! empty($group_name)): ?>
						<h1><?php echo $group_name ?></h1>
						<?php endif; ?>
						<?php if (Arr::path($groups_params, [$group_name, 'description'])):?>
							<p>
								<?php echo Arr::path($groups_params, [$group_name, 'description'])?>
							</p>
						<?php endif;?>
						<form method="post" class="g-form width_half i-form i-autoinit" enctype="multipart/form-data">
							<?php echo View::factory('admin/vof/fieldset', [
								'fields' => $single_group_fields,
								'values' => isset($values) ? $values : [],
								'errors' => isset($errors) ? $errors : [],
							])->render() ?>
							<?php if (Arr::path($groups_params, [$group_name, 'group_submit'], TRUE)):?>
								<button class="g-btn width_full style_solid color_green"><?php echo $button_title ?></button>
							<?php endif;?>
						</form>
				<br><br><br><br>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
</div>