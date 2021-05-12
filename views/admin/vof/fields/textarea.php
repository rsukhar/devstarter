<?php
/**
 * Convertful Options Field: Textarea
 *
 * Multiline text area
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['cols'] string Field title
 * @param $field ['rows'] string Field title
 *
 * @var $value string Current value
 */
$cols = Arr::get($field, 'cols', 30);
$rows = Arr::get($field, 'rows', 10);
$editor = Arr::get($field, 'editor');
$placeholder = Arr::get($field, 'placeholder', '');
?>
<textarea name="<?php echo HTML::chars($name) ?>" id="<?php echo HTML::chars($id) ?>" cols="<?php echo $cols;?>" rows="<?php echo $rows;?>" class="<?php echo $editor;?>" placeholder="<?php echo $placeholder;?>"><?php echo HTML::chars($value) ?></textarea>