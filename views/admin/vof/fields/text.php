<?php
/**
 * Convertful Options Field: Text
 *
 * Simple text line.
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['placeholder'] string Field placeholder
 * @param $field ['disabled'] string Field placeholder
 * @param $field ['search_closer'] bool
 *
 * @var $value string Current value
 */

$search_control = Arr::get($field, 'search_closer', FALSE);

$output = '';
if ($search_control)
{
    $output .= '<div class="for_search_closer"></div>';
}
$output .= '<input type="text" name="'.HTML::chars($name).'" id="'.HTML::chars($id).'" value="'.HTML::chars($value).'"';
if (isset($field['placeholder']) AND ! empty($field['placeholder']))
{
	$output .= ' placeholder="'.HTML::chars($field['placeholder']).'"';
}
if (isset($field['disabled']) AND $field['disabled'])
{
	$output .= ' disabled="disabled" ';
}
if (isset($field['suggestions']) AND ! empty($field['suggestions']) AND is_array($field['suggestions']))
{
	Assets::instance()->add('jquery.autocomplete');
	$output .= HTML::pass_to_js($field['suggestions'], FALSE, ' data-suggestions="%s"');
	$output .= ' autocomplete="off"><div class="vof-autocomplete-show-all"></div>';
}
else
{
	$output .= ' autocomplete="off">';
}

echo (Arr::get($field, 'slug')) ? '<span class="for_slug_before">/</span>&nbsp;'.$output.'<span class="for_slug_after">&nbsp;/</span>': $output;