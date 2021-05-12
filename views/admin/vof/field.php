<?php
/**
 * Output single VOF field
 *
 * @var string $name Field name
 * @var string $id Field ID
 * @var array $field Field settings
 * @var array $values Current fieldset values (including value for the current field)
 * @var array $errors Errors to display on load
 * @var Model $owner
 */

if (isset($field['place_if']) AND ! $field['place_if'])
{
	return;
}
if ( ! isset($field['type']))
{
	throw new Exception($name.' field has no type defined');
}

$show_field = ( ! isset($field['show_if']) OR VOF::is_show_if_statement_true($field['show_if'], $values));
if ($field['type'] == 'wrapper_start')
{
	//$row_classes = $name;
	$row_classes = ' type_'.$field['type'];
	if (isset($field['active']))
	{
		$row_classes .= ' '.($field['active'] ? 'active' : '');
	}
	if (isset($field['classes']) AND ! empty($field['classes']))
	{
		$row_classes .= ' '.$field['classes'];
	}
	echo '<div class="vof-form-wrapper '.$row_classes.'" data-name="'.$name.'" data-id="'.$id.'"';
	if ( ! $show_field)
	{
		echo ' style="display: none"';
	}
	echo '>';
	if (isset($field['title']) AND ! empty($field['title']))
	{
		echo '<div class="vof-form-wrapper-title">'.$field['title'].'</div>';
		echo '<div class="vof-form-wrapper-icon"></div>';
		echo '<div class="vof-form-wrapper-icon type_close"></div>';
	}
	echo '<div class="vof-form-wrapper-cont"';
	if (empty($field['active']))
	{
		echo ' style="display: none"';
	}
	echo '>';
	if (isset($field['show_if']) AND is_array($field['show_if']) AND ! empty($field['show_if']))
	{
		echo '<div class="vof-form-wrapper-showif"'.HTML::pass_to_js($field['show_if'], FALSE).'></div>';
	}

	return;
}
elseif ($field['type'] == 'wrapper_end')
{
	echo '</div></div>';

	return;
}

if ( ! isset($field['std']))
{
	$field['std'] = ((isset($field['options']) AND is_array($field['options']) AND ! Arr::get($field, 'placeholder')) ? key($field['options']) : NULL);
}
$value = isset($values[$name]) ? $values[$name] : $field['std'];

$field['description'] = Arr::get($field, 'description', '');
$field['classes'] = Arr::get($field, 'classes', '');

if (isset($field['description_type']) AND ! empty($field['description_type']))
{
	$field['classes'] .= ' desctype_'.$field['description_type'];
}
if (isset($field['description_place']) AND ! empty($field['description_place']))
{
	$field['classes'] .= ' descplace_'.$field['description_place'];
}

$row_classes = ' type_'.$field['type'];
if ($field['description'])
{
	// Adding default desctype_* class modificator when it doesn't exist
	$default_desctype = ($field['type'] === 'switcher') ? 'tooltip' : 'text';
	if ( ! preg_match('~desctype_([^ ]+)~', $field['classes'], $matches))
	{
		$field['classes'] .= ' desctype_'.$default_desctype;
	}
	$desctype = $matches ? $matches[1] : $default_desctype;
	// Adding default descplace_* class modificator when it doesn't exist
	if ( ! preg_match('~descplace_([^ ]+)~', $field['classes'], $matches))
	{
		$field['classes'] .= ' descplace_field';
	}
	$descplace = $matches ? $matches[1] : 'field';

	if ($field['description'] AND $desctype === 'tooltip' AND ($descplace === 'field' OR $descplace === 'title'))
	{
		$tooltip_place = 'place_'.$field['tooltip_place'] ?? 'place_bottom';
	}
}
if (isset($field['classes']) AND ! empty($field['classes']))
{
	$row_classes .= ' '.trim($field['classes']);
}
if (isset($errors) AND isset($errors[$name]))
{
	$row_classes .= ' check_wrong';
}

echo '<div class="vof-form-row'.$row_classes.'" data-name="'.$name.'" data-id="'.$id.'"';
if ( ! $show_field)
{
	echo ' style="display: none"';
}
echo '>';
if (isset($field['title']) AND ! empty($field['title']) AND ( ! in_array($field['type'], ['button', 'submit']) OR (isset($field['with_field_title']) AND $field['with_field_title'])))
{
	echo '<div class="vof-form-row-title">';
	echo '<span>'.$field['title'].'</span>';
	if ($field['description'] AND $descplace === 'title')
	{
		echo '<div class="vof-form-row-desc">';
		echo '<div class="vof-form-row-desc-icon"></div>';
		if ($desctype === 'tooltip')
		{
			echo '<div class="g-tooltip place_bottom">'.$field['description'].'</div>';
		}
		else
		{
			echo '<div class="vof-form-row-desc-text">'.$field['description'].'</div>';
		}
		echo '</div>';
	}
	if (isset($field['classes']) AND preg_match('~( |^)refreshpos_title( |$)~', $field['classes']))
	{
		echo '<div class="vof-form-row-control-refresh" title="Refresh"></div>';
	}
	echo '</div>';
}
if ($field['type'] === 'text' AND Arr::get($field, 'slug'))
{
	echo '<div class="vof-form-row-field"><div class="vof-form-row-control for_slug">';
}
else
{
	echo '<div class="vof-form-row-field"><div class="vof-form-row-control">';
}
// Including the field control itself
echo View::factory('admin/vof/fields/'.$field['type'], array(
	'name' => $name,
	'id' => $id,
	'field' => $field,
	'value' => $value,
	'errors' => (isset($errors)) ? $errors : [],
));
// Refreshable behavior
if (isset($field['classes']) AND preg_match('~( |^)i-refreshable( |$)~', $field['classes']))
{
	echo '<div class="vof-form-row-control-refresh" title="Refresh"></div>';
}
echo '</div><!-- .vof-form-row-control -->';
if ($field['description'] AND $descplace === 'field')
{
	echo '<div class="vof-form-row-desc">';
	echo '<div class="vof-form-row-desc-icon"></div>';
	if ($desctype === 'tooltip')
	{
		echo '<div class="g-tooltip '.$tooltip_place.'">'.$field['description'].'</div>';
	}
	else
	{
		echo '<div class="vof-form-row-desc-text">'.$field['description'].'</div>';
	}
	echo '</div>';
}
echo '<div class="vof-form-row-state">'.((isset($errors) AND isset($errors[$name])) ? $errors[$name] : '').'</div>';
echo '</div>'; // .vof-form-row-field

if (strpos(Arr::get($field, 'classes', ''), 'vof-disabled') !== FALSE)
{
	if ($owner)
	{
		$link = ($owner->plan === 'free') ? 'https://convertful.com/pricing' : '/users/'.$owner->username.'/billing';
		echo '<a href="'.$link.'" class="vof-disabled-text" target="_blank">Try Premium Features for Free</a>';
	}
}

if (isset($field['show_if']) AND is_array($field['show_if']) AND ! empty($field['show_if']))
{
	// Showing conditions
	echo '<div class="vof-form-row-showif"'.HTML::pass_to_js($field['show_if'], FALSE).'></div>';
}
if (isset($field['builder_preview']) AND ! empty($field['builder_preview']))
{
	// Showing conditions
	echo '<div class="vof-form-row-builderpreview"'.HTML::pass_to_js($field['builder_preview'], FALSE).'></div>';
}
if (isset($field['influence']) AND is_array($field['influence']) AND ! empty($field['influence']))
{
	// Showing conditions
	echo '<div class="vof-form-row-influence"'.HTML::pass_to_js($field['influence'], FALSE).'></div>';
}
if (isset($field['related']) AND is_array($field['related']) AND ! empty($field['related']))
{
	// Showing conditions
	echo '<div class="vof-form-row-related"'.HTML::pass_to_js($field['related'], FALSE).'></div>';
}
echo '</div><!-- .vof-form-row -->';
