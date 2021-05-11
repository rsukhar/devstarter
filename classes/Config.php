<?php

class Config extends Kohana_Config {

	protected static $owner_id = NULL;

	/**
	 * Get specific value from one of the config files based on path that includes the filename:
	 *
	 * Config::get('plans.free.price')
	 *
	 * @param string|array $path
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get($path, $default = NULL)
	{
		if ( ! is_array($path))
		{
			$path = explode('.', $path);
		}
		$config_group = array_shift($path);
		try
		{
			$config = Kohana::$config->load($config_group)->as_array();
		}
		catch (Kohana_Exception $e)
		{
			$config = [];
		}
		if ( ! empty($path))
		{
			$config = Arr::path($config, $path, $default);
		}

		return $config;
	}

	/**
	 * Get array of values from config
	 *
	 * @param string|array $path
	 * @param string $key_param Column for associative keys
	 * @param string $value_param Column for values
	 * @return array
	 */
	public static function get_array($path, $key_param = NULL, $value_param = NULL)
	{
		$array = Config::get($path, []);
		$result = [];
		foreach ($array as $key => $value)
		{
			if ($key_param !== NULL)
			{
				// Must use specific param as a key of the result array
				if ( ! isset($value[$key_param]))
				{
					continue;
				}
				$key = $value[$key_param];
			}
			if ($value_param !== NULL)
			{
				// Must use specific param as a key of the result array
				if ( ! isset($value[$value_param]))
				{
					continue;
				}
				$value = $value[$value_param];
			}
			$result[$key] = $value;
		}

		return $result;
	}
}
