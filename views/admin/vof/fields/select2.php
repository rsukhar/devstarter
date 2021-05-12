<?php
/**
 * Convertful Options Field: Select2
 *
 * Advanced select2-driven dropdown.
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['options'] array List of value => title
 * @param $field ['placeholder']
 * @param $field ['multiple'] bool Can multiple values be selected?
 * @param $field ['tokenize'] bool Automatic tokenization behavior https://select2.github.io/examples.html#tokenizer
 * @param $field ['tokenize_single'] select only single value with tokenize param
 * @param $field ['nonce'] string nonce string
 *
 * @var $value string|array Current value (or set of values for multiple selector
 */
$multiple = ! ! Arr::get($field, 'multiple');
$tokenize = ! ! Arr::get($field, 'tokenize');
$placeholder = Arr::get($field, 'placeholder');
$tokenize_single = ! ! Arr::get($field, 'tokenize_single');
$field['options'] = isset($field['options']) ? $field['options'] : array();
$nonce = Arr::get($field, 'nonce', NULL);
if ($tokenize AND ! $tokenize_single)
{
	$multiple = TRUE;
}
$output = '<select name="'.HTML::chars($name).'" id="'.HTML::chars($id).'" autocomplete="off"';
if ($multiple)
{
	$value = (empty($value) OR ! is_array($value)) ? array() : $value;
	$output .= ' multiple="multiple"';
}
if ($tokenize)
{
	$output .= ' class="i-tokenize"';
	// add values to options
	$field['options'] = Arr::merge($field['options'], array_combine($value, $value));
}
if (! empty($placeholder))
{
	$output .= ' data-placeholder="'.HTML::chars($placeholder).'"';
}
$output .= '>';

if (! empty($placeholder))
{
	$output .= '<option></option>';
}
foreach ($field['options'] as $option_value => $option_title)
{
	if (is_array($option_title))
	{
		// Option Group
		if (isset($option_title['title']) AND isset($option_title['options']))
		{
			// Format 1: index => array( title => ... , options => ... )
			$optgroup_title = &$field['options'][$option_value]['title'];
			$optgroup_options = &$field['options'][$option_value]['options'];
		}
		else
		{
			// Format 2: title => options
			$optgroup_title = $option_value;
			$optgroup_options = $option_title;
		}
		$output .= '<optgroup label="'.HTML::chars($optgroup_title).'">';
		foreach ($optgroup_options as $sub_option_value => $sub_option_title)
		{
			$nonce = ($nonce) ? 'data-nonce='.HTML::chars(Security::nonce($nonce.$sub_option_value)).'' : '';
			$is_selected = ($multiple ? in_array($sub_option_value, $value) : ($sub_option_value == $value));
			$output .= '<option value="'.HTML::chars($sub_option_value).'" ' .($is_selected ? ' selected' : '').' '.$nonce.'>'.HTML::chars($sub_option_title).'</option>';
		}
		$output .= '</optgroup>';
	}
	else
	{
		// Just an option
		$nonce = ($nonce) ? 'data-nonce='.HTML::chars(Security::nonce($nonce.$option_value)).'' : '';
		$is_selected = ($multiple ? in_array($option_value, $value) : ($option_value == $value));
		$output .= '<option value="'.HTML::chars($option_value).'" '.($is_selected ? ' selected' : '').' '.$nonce.'>'.HTML::chars($option_title).'</option>';
	}
}

$output .= '</select>';
if ($tokenize)
{
	$output .= '<div class="vof-field-hider"></div>';
}
echo $output;
