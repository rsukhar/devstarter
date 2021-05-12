<?php

class ORM extends Kohana_ORM {

	/**
	 * VOF-поля для редактирования модели, функция будет перегружаться в дочерних классах
	 * @return array
	 */
	public function describe_vof_fields()
	{
		return [];
	}

	/**
	 * Проставить значения в объект модели, фильтруя и валидируя их, используя VOF-поля
	 *
	 * @param array $values
	 * @param array $errors
	 * @return bool Успешно ли проставились значения? Или же есть ошибки валидации?
	 */
	public function set_vof_values(array $values, array &$errors)
	{
		// Создаем виртуальный объект, чтобы получить описание полей, которое может зависить от значений
		$_obj = ORM::factory(substr(get_class($this), 6));
		$fields = $_obj->values($values)->describe_vof_fields();
		$values = VOF::filter_values($values, $fields);
		if ( ! VOF::validate_values($values, $fields, $errors))
		{
			return FALSE;
		}
		$this->values($values, array_keys($values));

		return TRUE;
	}

	public function get_vof_fieldset($name, $node_type)
	{
		$fields = $this->describe_vof_fields();

		if ( ! isset($name))
		{
			return $fields;
		}

		$fieldset_config = Config::get('admin_content.'.$node_type.'.fieldsets.'.$name.'.fields', []);
		$fieldset = array_intersect_key($fields, $fieldset_config);

		return (empty($fieldset)) ? $fields : $fieldset;
	}
}