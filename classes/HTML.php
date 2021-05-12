<?php defined('SYSPATH') OR die('No direct script access.');

class HTML extends Kohana_HTML {

	/**
	 * Pass some PHP variable to JS
	 *
	 * @param mixed $variable
	 * @param bool $output Should we output it instantly?
	 * @param string $format
	 *
	 * @return string
	 */
	public static function pass_to_js($variable, $output = TRUE, $format = ' onclick="return %s"')
	{
		$result = htmlspecialchars(json_encode($variable), ENT_QUOTES, 'UTF-8');
		if ($format)
		{
			$result = sprintf($format, $result);
		}
		if ($output)
		{
			echo $result;
		}

		return $result;
	}

}
