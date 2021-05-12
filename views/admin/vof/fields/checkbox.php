<?php
/**
 * Convertful Options Field: Checkbox
 *
 * Checkbox
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['options'] array Array of two key => title pairs
 * @param $field ['text'] array Additional text to show right near the switcher
 *
 * @var $value string Current value
 */
if ( ! isset($field['options']) OR empty($field['options']))
{
	$field['options'] = array(
		TRUE => 'On',
		FALSE => 'Off',
	);
}
$field_keys = array_keys($field['options']);
if (count($field_keys) < 2)
{
	return;
}
?>
<div class="vof-checkbox">
	<input type="hidden" name="<?php echo HTML::chars($name) ?>" value="<?php echo HTML::chars($field_keys[1]) ?>" autocomplete="off">
	<input type="checkbox" value="<?php echo HTML::chars($field_keys[0]) ?>" name="<?php echo HTML::chars($name) ?>" id="<?php echo HTML::chars($id) ?>"<?php echo(($value == $field_keys[0]) ? ' checked' : '') ?> autocomplete="off">
	<label for="<?php echo HTML::chars($id) ?>">
		<span class="vof-checkbox-icon"></span>
		<?php if (isset($field['text']) AND ! empty($field['text'])): ?>
			<span class="vof-checkbox-text"><?php echo $field['text'] ?></span>
		<?php endif; ?>
	</label>
</div>