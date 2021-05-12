<?php defined('SYSPATH') or die('No direct script access.');

class Security extends Kohana_Security {

	/**
	 * Get the time-dependent variable for nonce creation.
	 *
	 * @return float
	 */
	protected static function nonce_tick()
	{
		$nonce_life = Date::DAY;

		return ceil(time() / ($nonce_life / 2));
	}

	/**
	 * Creates a cryptographic token tied to a specific action, user, user session,
	 * and window of time.
	 *
	 * @param string|int $action Scalar value to add context to the nonce.
	 * @param null $user_id
	 * @return string The token
	 * @throws Kohana_Exception
	 */
	public static function nonce($action = -1, $user_id = NULL)
	{
		$auth = Auth::instance();
		if (is_null($user_id))
		{
			$user_id = $auth->logged_in() ? $auth->get_user()->id : 0;
		}

		$nonce_tick = self::nonce_tick();
		$token = Cookie::$salt;

		return substr($auth->hash($nonce_tick.'|'.$action.'|'.$user_id.'|'.$token), -12, 10);
	}

	/**
	 * Verify that correct nonce was used with time limit.
	 *
	 * The user is given an amount of time to use the token, so therefore, since the
	 * UID and $action remain the same, the independent variable is the time.
	 *
	 * @param string $nonce Nonce that was used in the form to verify
	 * @param string|int $action Should give context to what is taking place and be the same when nonce was created.
	 * @param null $user_id
	 * @return bool Is the nonce valid?
	 * @throws Kohana_Exception
	 */
	public static function check_nonce($nonce, $action = -1, $user_id = NULL)
	{
		$nonce = (string) $nonce;
		$auth = Auth::instance();
		if (is_null($user_id))
		{
			$user_id = $auth->logged_in() ? $auth->get_user()->id : 0;
		}

		$nonce_tick = self::nonce_tick();
		$token = Cookie::$salt;

		// Nonce generated 0-12 hours ago
		$expected = substr($auth->hash($nonce_tick.'|'.$action.'|'.$user_id.'|'.$token), -12, 10);
		if (Security::slow_equals($expected, $nonce))
		{
			return TRUE;
		}

		// Nonce generated 12-24 hours ago
		$expected = substr($auth->hash(($nonce_tick - 1).'|'.$action.'|'.$user_id.'|'.$token), -12, 10);
		if (Security::slow_equals($expected, $nonce))
		{
			return TRUE;
		}

		return FALSE;
	}

}