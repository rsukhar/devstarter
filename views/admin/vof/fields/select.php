<?php
/**
 * Convertful Options Field: Select
 *
 * Simple select dropdown.
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *  ['title'] string Field title
 *  ['description'] string Field title
 *  ['options'] array List of value => title
 *  ['nonce'] string nonce string
 *
 * @var $value string Current value
 */
$placeholder = Arr::get($field, 'placeholder', NULL);
$nonce = Arr::get($field, 'nonce', NULL);
?>
<div class="vof-select">
	<select name="<?php echo HTML::chars($name) ?>" id="<?php echo HTML::chars($id) ?>" autocomplete="off">
		<?php if ($placeholder): ?>
			<option value="" disabled="disabled"<?php echo ($value === NULL) ? ' selected="selected"' : '' ?>>
				<?php echo HTML::chars($placeholder) ?>
			</option>
		<?php endif; ?>
		<?php foreach (Arr::get($field, 'options', []) AS $option_value => $option_title): ?>
			<?php if (is_array($option_title)): ?>
				<?php
				$option_label = Arr::path($field, 'options_labels.'.$option_value, $option_value); ?>
				<optgroup label="<?php echo $option_label ?>" data-id="<?php echo $option_value ?>">
					<?php foreach ($option_title AS $sub_option_value => $sub_option_title): ?>
						<option value="<?php echo HTML::chars($sub_option_value) ?>"<?php echo(($option_value == $value AND ( ! $placeholder OR $value !== NULL)) ? ' selected' : '') ?> <?php if ($nonce) echo 'data-nonce="'.HTML::chars(Security::nonce($nonce.$option_value)).'"' ?>>
							<?php echo HTML::chars($sub_option_title) ?>
						</option>
					<?php endforeach; ?>
				</optgroup>
			<?php else: ?>
				<option value="<?php echo HTML::chars($option_value) ?>"<?php echo(($option_value == $value AND ( ! $placeholder OR $value !== NULL)) ? ' selected' : '') ?> <?php if ($nonce) echo 'data-nonce="'.HTML::chars(Security::nonce($nonce.$option_value)).'"' ?>>
					<?php echo HTML::chars($option_title) ?>
				</option>
			<?php endif; ?>
		<?php endforeach; ?>
	</select>
</div>
