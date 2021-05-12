<?php
/**
 * Convertful Options Field: Submit
 *
 * Simple submit button.
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['color'] string primary / secondary
 * @param $field ['action'] string
 *
 * @var $value string Current value
 */

$field['color'] = isset($field['color']) ? $field['color'] : 'primary';
$classes = $field['classes'] ?? '';
$classes .= ' color_'.$field['color'];
if (isset($field['action']))
{
	$classes .= ' action_'.$field['action'];
}
if (strpos($classes, 'style_') === FALSE)
{
	$classes = trim('style_solid '.$classes);
}
?>
<a class="g-btn <?php echo $classes ?>" href="javascript:void(0)"><?php echo $field['title'] ?></a>
