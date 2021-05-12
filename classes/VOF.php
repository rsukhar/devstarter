<?php

class VOF {

	/**
	 * Checks if the showing condition is true
	 *
	 * Note: at any possible syntax error we choose to show the field so it will be functional anyway.
	 *
	 * @param array $show_if Showing condition
	 * @param array $values Current values
	 *
	 * @return bool
	 */
	public static function is_show_if_statement_true(array &$show_if, array &$values)
	{
		if ( ! is_array($show_if) OR count($show_if) < 3)
		{
			// Wrong condition
			$result = TRUE;
		}
		elseif ($show_if[1] == 'or' OR $show_if[1] == 'and')
		{
			// Complex or / and statement
			$result = VOF::is_show_if_statement_true($show_if[0], $values);
			$index = 2;
			while (isset($show_if[$index]))
			{
				$show_if[$index - 1] = strtolower($show_if[$index - 1]);
				if ($show_if[$index - 1] == 'and')
				{
					$result = ($result AND VOF::is_show_if_statement_true($show_if[$index], $values));
				}
				elseif ($show_if[$index - 1] == 'or')
				{
					$result = ($result OR VOF::is_show_if_statement_true($show_if[$index], $values));
				}
				$index = $index + 2;
			}
		}
		else
		{
			// Values can be accessed by dot-separated path
			$value = Arr::path($values, $show_if[0]);
			if ($value === NULL)
			{
				return TRUE;
			}
			if ($show_if[1] == '=')
			{
				$result = ($value == $show_if[2]);
			}
			elseif ($show_if[1] == '!=' OR $show_if[1] == '<>')
			{
				$result = ($value != $show_if[2]);
			}
			elseif ($show_if[1] == 'in')
			{
				$result = TRUE;
				// Should work both for arrays and strings
				if (is_array($show_if[2]))
				{
					$result = in_array($value, $show_if[2]);
				}
				elseif (is_string($show_if[2]))
				{
					$result = (strpos($show_if[2], $value) !== FALSE);
				}
			}
			elseif ($show_if[1] == 'not in')
			{
				$result = TRUE;
				// Should work both for arrays and strings
				if (is_array($show_if[2]))
				{
					$result = ! in_array($value, $show_if[2]);
				}
				elseif (is_string($show_if[2]))
				{
					$result = (strpos($show_if[2], $value) === FALSE);
				}
			}
			elseif ($show_if[1] == 'has')
			{
				$result = TRUE;
				// Should work both for arrays and strings
				if (is_array($show_if[2]))
				{
					$result = in_array($show_if[2], $value);
				}
				elseif (is_string($show_if[2]))
				{
					$result = (strpos($value, $show_if[2]) !== FALSE);
				}
			}
			elseif ($show_if[1] == '<=')
			{
				$result = ($value <= $show_if[2]);
			}
			elseif ($show_if[1] == '<')
			{
				$result = ($value < $show_if[2]);
			}
			elseif ($show_if[1] == '>')
			{
				$result = ($value > $show_if[2]);
			}
			elseif ($show_if[1] == '>=')
			{
				$result = ($value >= $show_if[2]);
			}
			else
			{
				$result = TRUE;
			}
		}

		return $result;
	}

	/**
	 * Filter user defined values based on fieldset config
	 *
	 * @param array $values
	 * @param array $fields VOF fieldset config
	 * @return array
	 */
	public static function filter_values(array $values, array $fields)
	{
		$result = [];
		foreach ($fields as $key => $field)
		{
			$type = Arr::get($field, 'type', 'text');
			// Types that have no value at all
			if ($type == 'submit' OR $type == 'alert' OR substr($type, 0, 8) == 'wrapper_')
			{
				continue;
			}
			// Поля, наличие которых опраделяется внешним условием, которое не выполнено
			if (Arr::get($field, 'place_if') === FALSE)
			{
				continue;
			}
			$value = Arr::get($values, $key, Arr::get($field, 'std'));
			if (isset($field['options']) AND is_array($field['options']) AND ! Arr::get($field, 'tokenize', FALSE))
			{
				if (in_array($type, ['checkboxes', 'badges']) OR Arr::get($field, 'multiple', FALSE) === TRUE)
				{
					$value = is_array($value) ? $value : [];
					$value = array_intersect(array_values($value), array_keys($field['options']));
				}
				elseif ($value === '' AND Arr::get($field, 'placeholder') !== NULL)
				{
					// Field has a placeholder and the value hasn't been ever selected -- it's ok, keeping the empty value
				}
				else
				{
					if ( ! array_key_exists($value, $field['options']))
					{
						$value_verified = FALSE;
						foreach ($field['options'] as $opt_value => $opt_label)
						{
							if (is_array($opt_label) AND isset($opt_label[$value]))
							{
								$value_verified = TRUE;
								break;
							}
						}
						$value = $value_verified ? $value : NULL;
					}
				}
			}
			// Types where value is supposed to be boolean
			elseif ($type == 'switcher')
			{
				$value = (bool) $value;
			}
			// Types with string values
			elseif ($type == 'text' OR $type == 'password')
			{
				$value = trim($value);
				if ($key === 'phone')
				{
					$value = preg_replace('~[^0-9]~', '', $value);
				}
				if ($key === 'source_published' AND empty($value))
				{
					continue;
				}
			}
			elseif ($type === 'textarea')
			{
				if ($key === 'video_links')
				{
					$data_values = explode("\n", $value);
					foreach ($data_values as $index => $_value)
					{
						if ( ! preg_match('/(http:\/\/|https:\/\/|)(player.|www.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/',$_value))
						{
							unset($data_values[$index]);
						}
					}
					$value = implode("\n", $data_values);
				}
			}
			if (Arr::get($field, 'empty_is_null') AND empty($value))
			{
				$value = NULL;
			}
			$result[$key] = $value;
		}
		return $result;
	}

	/**
	 * Get defaults for fields
	 * @param array $fields
	 * @return array
	 */
	public static function get_defaults(array $fields)
	{
		$result = [];
		foreach ($fields as $key => $field)
		{
			if (isset($field['std']))
			{
				$result[$key] = $field['std'];
			}
			elseif (isset($field['options']) AND ! empty($field['options']))
			{
				$first_option = array_keys($field['options'])[0];
				if (is_array($field['options'][$first_option]))
				{
					$first_option = array_keys($field['options'][$first_option])[0];
				}
				$result[$key] = $first_option;
			}
			else
			{
				$result[$key] = '';
			}
		}

		return $result;
	}

	public static function validate_values(array $values, array $fields, array &$errors = [])
	{
		$validation = Validation::factory($values);
		foreach ($fields as $f_name => $field)
		{
			// Поля, наличие которых опраделяется внешним условием, которое не выполнено
			if (Arr::get($field, 'place_if') === FALSE)
			{
				continue;
			}
			if (isset($field['rules']) AND is_array($field['rules']))
			{
				$validation
					->rules($f_name, $field['rules'])
					->label($f_name, Arr::get($field, 'title'));
			}
		}
		if (($is_valid = $validation->check()) === FALSE)
		{
			$errors = $validation->errors('validation', TRUE);

		}

		return $is_valid;
	}

	/**
	 * Validate values using the describe_data_rules
	 * @param array $values
	 * @param array $rules
	 * @param array $errors
	 * @param null $filter
	 * @return bool
	 */
	public static function validate_data_rules(array $values, array $rules, array &$errors = [], $filter = NULL)
	{
		$validation = Validation::factory($values);

		foreach ($values as $f_name => $field)
		{
			$validation
				->rules($f_name, $rules)
				->label($f_name, NULL)
				->filter_rules($f_name, $filter);
		}

		if (($is_valid = $validation->check()) === FALSE)
		{
			$errors = $validation->errors('validation', FALSE, $validation->get_custom_messages($rules));
		}

		return $is_valid;
	}

	/**
	 * Get human readable value
	 * @param mixed $value field value
	 * @param array $field
	 *
	 * @return mixed
	 */
	public static function get_humanized_value($value, array $field)
	{
		$options = Arr::get($field, 'options');
		if ($options !== NULL)
		{
			if (is_array($value))
			{
				$value = Arr::extract($field, array_values($value));
				$value = implode(', ', $value);
			}
			else
			{
				$value = Arr::path($field, ['options', $value]);
			}
		}

		return $value;
	}

	public static function get_fieldsets_titles($fieldsets)
	{
		$titles = [];
		foreach ($fieldsets as $key => $value)
		{
			foreach (Arr::get($value, 'fields', $value) as $field_key => $field)
			{
				if (Arr::get($field, 'title'))
				{
					$titles[$field_key] = Arr::get($field, 'title');
				}
				if ($field_key === 'has_studs')
				{
					$titles[$field_key] = Arr::get($field, 'text');
				}
				if (Arr::get($field, 'options'))
				{
					$titles = Arr::merge($titles, Arr::get($field, 'options', []));
				}
			}
		}

		return $titles;
	}
}
