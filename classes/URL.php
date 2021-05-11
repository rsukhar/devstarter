<?php

class URL extends Kohana_URL {

	/**
	 * @return string Current protocol
	 */
	public static function protocol()
	{
		if (Request::$initial instanceof Request)
		{
			if (Request::$initial->secure())
			{
				return 'https:';
			}
			list($protocol) = explode('/', strtolower(Request::$initial->protocol()));

			return $protocol.':';
		}

		return 'http:';
	}

	/**
	 * Get Current domain
	 * @param bool $with_protocol
	 * @param bool $only_root
	 * @return string Current domain (with protocol included)
	 */
	public static function domain($with_protocol = TRUE, $only_root = FALSE)
	{
		$domain = isset($_SERVER['HTTP_HOST'])
			? $_SERVER['HTTP_HOST']
			// CLI mode
			: URL::primary_domain(FALSE);

		if ($only_root)
		{
			$domain = explode('.', $domain);
			$domain = implode('.', array_slice($domain, -2, 2));
		}

		return ($with_protocol ? URL::protocol().'//' : '').$domain;
	}

	/**
	 * Get main application domain
	 * @param bool $with_protocol
	 * @param bool $only_root
	 * @return string Primary Domain (with protocol included)
	 */
	public static function primary_domain($with_protocol = TRUE, $only_root = FALSE)
	{
		$domain = Settings::get('general.domain_name');

		if (Kohana::$environment === 40)
		{
			$domain = 'vsekolesa.local';
		}

		return ($with_protocol ? URL::protocol().'//' : '').$domain;
	}
}
