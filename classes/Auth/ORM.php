<?php defined('SYSPATH') or die('No direct access allowed.');

class Auth_ORM extends Kohana_Auth_ORM {

	/**
	 * Logs a user in.
	 *
	 * @param string $username
	 * @param string $password
	 * @param boolean $remember enable autologin
	 * @return boolean|string
	 */
	protected function _login($user, $password, $remember)
	{
		if ( ! is_object($user))
		{
			$username = $user;

			// Load the user
			$user = ORM::factory('User');
			$user->where($user->unique_key($username), '=', $username)->find();
		}

		// If the passwords match, perform a login
		if ($this->hash($password) === $user->password)
		{
			return $this->force_login($user, $remember);
		}

		// Login failed
		return FALSE;
	}

	/**
	 * Forces a user to be logged in, without specifying a password.
	 *
	 * @param Model_User $user User ORM object
	 * @param boolean $remember Enable autologin
	 * @param boolean $disable_update disable update user info
	 * @return boolean
	 */
	public function force_login($user, $remember = FALSE, $disable_statistic = FALSE)
	{
		if ($remember === TRUE)
		{
			// Token data
			$data = array(
				'user_id' => $user->pk(),
				'expires' => time() + $this->_config['lifetime'],
				'user_agent' => sha1(Request::$user_agent),
			);

			// Create a new autologin token
			$token = ORM::factory('User_Token')
				->values($data)
				->create();

			// Set the autologinM cookie
			Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
		}

		// Finish the login
		if ($disable_statistic)
		{
			parent::complete_login($user);
		}
		else
		{
			$this->complete_login($user);
		}

		if ($remember === TRUE)
		{
			return $token->token;
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * Logs a user in, based on the token
	 *
	 * @param string $token
	 * @param bool $check_user_agent
	 * @return  mixed
	 * @throws Kohana_Exception
	 */
	public function login_by_token($token, $check_user_agent = TRUE)
	{
		$token = ORM::factory('User_Token', array('token' => $token));

		if ($token->loaded() AND $token->user->loaded())
		{
			if (! $check_user_agent OR $token->user_agent === sha1(Request::$user_agent))
			{
				// Complete the login with the found data
				$this->complete_login($token->user);

				// Automatic login was successful
				return $token->user;
			}

			// Token is invalid
			$token->delete();
		}

		return NULL;
	}

	public function logout_by_token($token)
	{
		$token = ORM::factory('User_Token', array('token' => $token));

		if ($token->loaded() AND $token->user->loaded())
		{
			$token->delete();
		}

		parent::logout();
	}
}
