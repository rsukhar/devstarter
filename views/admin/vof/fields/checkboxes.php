<?php
/**
 * Convertful Options Field: Checkboxes
 *
 * Checkboxes
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 * @param $field ['options'] array List of value => title
 *
 * @var $value array Current value
 */
if ( ! is_array($value))
{
	$value = [];
}
$has_filter = $field ['has_filter'] ?? FALSE;
$filter_max_count = $field ['filter_max_count'] ?? 10;
$filter_max_width = $field ['filter_max_width'] ?? 500;
$filter_min_search_length = $field ['filter_min_search_length'] ?? 2;
$nested_checkboxes = Arr::get($field, 'nested', []);
$tooltips = Arr::get($field, 'tooltips', []);
?>
<div class="vof-checkboxes" id="<?php echo HTML::chars($id) ?>">
	<?php if ($has_filter):?>
        <div class="vof-checkboxes-filter" data-max-count="<?php echo $filter_max_count;?>" data-max-width="<?php echo $filter_max_width;?>" data-min-search-length="<?php echo $filter_min_search_length?>">
            <input type="text" name="query" id="query" value="" placeholder="" autocomplete="off">
        </div>
	<?php endif;?>
	<?php $render_checkbox = function ($option_value, $option_title, $nested = FALSE, $parent = '') use ($name, $id, $value, $tooltips){
		$checkbox_name = ($nested) ? HTML::chars($name).'['.$parent.'][]' : HTML::chars($name).'[]';
		$checkbox_id = ($nested) ? $id.'_'.$parent.'_'.HTML::chars($option_value) : $id.'_'.HTML::chars($option_value);
		$checked = ($nested) ? in_array($option_value, Arr::get($value, $parent, [])) : in_array($option_value, $value);
		$html = '<div class="vof-checkbox '.(($nested) ? 'is-nested' : '').'">
                    <input type="checkbox" name="'.$checkbox_name.'" value="'.HTML::chars($option_value).'" id="'.$checkbox_id.'" '.(($checked) ? ' checked' : '').' autocomplete="off" data-parent="'.$parent.'">
                    <label for="'.$checkbox_id.'">
                        <span class="vof-checkbox-icon"></span>
                        <span class="vof-checkbox-text">'.$option_title.'</span>
                     </label>';
		if (Arr::get($tooltips, $option_value))
        {
            $html .= '<div class="vof-form-row-desc">
	            <div class="vof-form-row-desc-icon"></div>
	            <div class="g-tooltip place_bottom">'.Arr::get($tooltips, $option_value).'</div>
            </div>';
        }
		$html .= '</div>';
		echo $html;
	}?>
	<?php foreach (Arr::get($field, 'options', array()) AS $option_value => $option_title): ?>
		<?php $render_checkbox($option_value, $option_title);?>
		<?php foreach (Arr::get($nested_checkboxes, $option_value, []) as $nested_value => $nested_title):?>
			<?php $render_checkbox($nested_value, $nested_title, TRUE, $option_value);?>
		<?php endforeach; ?>
	<?php endforeach; ?>
	<?php if ($has_filter):?>
        <div class="vof-checkboxes-filter-show-all is-hidden">Показать все</div>
	<?php endif;?>
</div>