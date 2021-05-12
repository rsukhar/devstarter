<?php
/**
 * Output a COF fieldset (group of fields)
 *
 * @var array $fields Fields settings name => field
 * @var array $values Fieldset values
 * @var array $errors Fieldset errors to output on load
 * @var string $id_prefix Prefix to add to names when creating IDs from them
 * @var string $layout Layout (optional): 'hor' / 'ver'
 * @var boolean $pass_values_to_js
 */
if ( ! isset($values) OR ! is_array($values))
{
	// Используем значения по умолчанию, чтобы правильно обрабатывать show_if правила
	$values = VOF::get_defaults($fields);
}
if ( ! isset($errors) OR ! is_array($errors))
{
	$errors = [];
}
if ( ! isset($id_prefix))
{
	global $vof_fieldset_index;
	$vof_fieldset_index = isset($vof_fieldset_index) ? ($vof_fieldset_index + 1) : 1;
	$id_prefix = 'vof'.$vof_fieldset_index.'_';
}
if ( ! isset($pass_values_to_js))
{
	$pass_values_to_js = TRUE;
}

foreach ($fields as $field_name => $field)
{
	if (isset($layout) AND $layout !== 'ver')
	{
		// Adding horizontal layout class to all the fields
		$field['classes'] = (isset($field['classes']) ? ($field['classes'].' ') : '').'layout_'.$layout;
	}
	if (is_string($field))
	{
		echo '<span>'.HTML::chars($field).'</span>';
		continue;
	}
	echo View::factory('admin/vof/field', array(
		'name' => $field_name,
		'id' => $id_prefix.$field_name,
		'field' => $field,
		'values' => $values,
		'errors' => $errors,
		'owner' => $owner ?? NULL,
	));
}

if ($pass_values_to_js)
{
	echo '<div class="vof-form-values"'.HTML::pass_to_js($values, FALSE).'></div>';
}
