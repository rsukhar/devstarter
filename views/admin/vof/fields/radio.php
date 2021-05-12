<?php
/**
 * Convertful Options Field: Radio
 *
 * Simple radio buttons selector.
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['options'] array List of value => title
 * @param $field ['html_options']
 *
 * @var $value array Current value
 */
$html_options = Arr::get($field, 'html_options', FALSE);
?>
<div class="vof-radio-list">
	<?php foreach ($field['options'] AS $option_value => $option_title): ?>
		<div class="vof-radio<?php echo ($option_value == $value) ? ' active' : '' ?>">
			<input autocomplete="off" type="radio" name="<?php echo $name ?>" value="<?php echo HTML::chars($option_value) ?>" id="<?php echo $id.'_'.HTML::chars($option_value) ?>"<?php echo ($option_value === $value) ? ' checked="checked"' : '' ?>>
			<label for="<?php echo $id.'_'.HTML::chars($option_value) ?>">
				<span class="vof-radio-icon"></span>
				<span class="vof-radio-text"><?php echo ($html_options) ? $option_title : HTML::chars($option_title) ?></span>
			</label>
		</div>
	<?php endforeach; ?>
</div>