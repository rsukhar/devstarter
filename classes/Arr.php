<?php

class Arr extends Kohana_Arr{

	/**
	 * Retrieves muliple single-key values from a list of arrays.
	 *
	 *     // Get all of the "id" values from a result
	 *     $ids = Arr::pluck($result, 'id');
	 *
	 * [!!] A list of arrays is an array that contains arrays, eg: array(array $a, array $b, array $c, ...)
	 *
	 * @param array $array list of arrays to check
	 * @param string $key key to pluck
	 * @param bool $preserve_keys Preserve keys of the given array?
	 * @return array
	 */
	public static function pluck($array, $key, $preserve_keys = FALSE)
	{
		$values = array();

		foreach ($array as $k => $row)
		{
			if (isset($row[$key]))
			{
				// Found a value in this row
				if ($preserve_keys)
				{
					$values[$k] = $row[$key];
				}
				else
				{
					$values[] = $row[$key];
				}
			}
		}

		return $values;
	}

	/**
	 * Tests if it's an associative array or not
	 *
	 *     // Returns TRUE
	 *     Arr::is_assoc(array('username' => 'john.doe'));
	 *
	 *     // Returns FALSE
	 *     Arr::is_assoc('foo', 'bar');
	 *
	 * @param  array $array  to check
	 * @return  boolean
	 */
	public static function is_assoc(array $array)
	{
		return self::is_array($array) AND parent::is_assoc($array);
	}

	public static function path($array, $path, $default = NULL, $delimiter = NULL)
	{
		if ( ! Arr::is_array($array))
		{
			// This is not an array!
			return $default;
		}

		if (is_array($path))
		{
			// The path has already been separated into keys
			$keys = $path;
		}
		else
		{
			if (isset($array[$path]))
			{
				// No need to do extra processing
				return $array[$path];
			}

			if ($delimiter === NULL)
			{
				// Use the default delimiter
				$delimiter = Arr::$delimiter;
			}

			// Remove starting delimiters and spaces
			$path = ltrim($path, "{$delimiter} ");

			// Remove ending delimiters, spaces, and wildcards
			$path = rtrim($path, "{$delimiter} *");

			// Split the keys by delimiter
			$keys = explode($delimiter, $path);
		}

		do
		{
			$key = array_shift($keys);

			if (ctype_digit($key))
			{
				// Make the key an integer
				$key = (int) $key;
			}

			if (isset($array[$key]))
			{
				if ($keys)
				{
					if (Arr::is_array($array[$key]))
					{
						// Dig down into the next part of the path
						$array = $array[$key];
					}
					else
					{
						// Unable to dig deeper
						break;
					}
				}
				else
				{
					// Found the path requested
					return $array[$key];
				}
			}
			elseif ($key === '*')
			{
				// Handle wildcards

				$values = [];
				foreach ($array as $arr)
				{
					if ($value = Arr::path($arr, implode('.', $keys)))
					{
						$values[] = $value;
					}
				}

				if ($values)
				{
					// Found the values requested
					return $values;
				}
				else
				{
					// Unable to dig deeper
					break;
				}
			}
			else
			{
				// Unable to dig deeper
				break;
			}
		}
		while ($keys);

		// Unable to find the value requested
		return $default;
	}

}