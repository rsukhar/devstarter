<?php defined('SYSPATH') OR die('No direct script access.');

class Settings {

	/**
	 * @var array Array of settings (getting all settings on instance creation)
	 */
	protected static $data = NULL;

	protected static function maybe_load_data()
	{
		if (self::$data !== NULL)
		{
			return;
		}

		self::$data = DB::select('name', 'value')->from('settings')->execute()->as_array('name', 'value');

		foreach (self::$data as $name => $value)
		{
			if (strlen($value) > 0 AND ($value[0] == '{' OR $value[0] == '['))
			{
				$decoded_value = json_decode($value, TRUE);
				if (json_last_error() == JSON_ERROR_NONE)
				{
					self::$data[$name] = $decoded_value;
				}
			}
		}
	}

	/**
	 * Set value for a given setting
	 *
	 * @param string|array $path
	 * @param mixed $value
	 * @throws Kohana_Exception
	 */
	public static function set($path, $value)
	{
		self::maybe_load_data();
		if ( ! is_array($path))
		{
			$path = explode('.', $path);
		}
		// Create new or update existing?
		$setting_exists = isset(self::$data[$path[0]]);

		if (count($path) == 1 AND $value === NULL)
		{
			// Should remove the setting
			return DB::delete('settings')
				->where('name', '=', $path[0])
				->execute();
		}

		Arr::set_path(self::$data, $path, $value);

		// Should store
		if ( ! $setting_exists)
		{
			DB::insert('settings', ['name', 'value'])
				->values([
					'name' => $path[0],
					'value' => is_array(self::$data[$path[0]]) ? json_encode(self::$data[$path[0]]) : self::$data[$path[0]],
				])
				->execute();
		}
		else
		{
			DB::update('settings')
				->set(['value' => is_array(self::$data[$path[0]]) ? json_encode(self::$data[$path[0]]) : self::$data[$path[0]]])
				->where('name', '=', $path[0])
				->execute();
		}
	}

	/**
	 * @param string $path
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public static function get($path, $default = NULL)
	{
		self::maybe_load_data();

		return Arr::path(self::$data, $path, $default);
	}

	/**
	 * @param string $name
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public static function path($path, $default = NULL)
	{
		return self::get($path, $default);
	}

	static function get_combined_array(string $path, $default = [])
	{
		$result = self::path($path, $default);

		if ( ! is_array($result))
		{
			return $default;
		}

		return array_combine($result, $result);
	}
}
