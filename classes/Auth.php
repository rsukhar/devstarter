<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Class Auth
 *
 * User roles:
 * 1. guest
 * 2. customer
 * 3. manager
 * 4. admin
 */
abstract class Auth extends Kohana_Auth {

	public static function get_user_id()
	{
		$user = Auth::instance()->get_user();

		return ($user !== NULL) ? $user->id : NULL;
	}

	/**
	 * Проверят принадлежность пользователя к указанным ролям
	 *
	 * Использование: Auth::user_is('manager', 'admin')
	 *
	 * (!) НЕ используйте "guest" / "customer". Проверяйте только привелигерованные роли, чтобы если новые роли добавятся, это не
	 * привело к логическим ошибкам.
	 */
	public static function user_is()
	{
		$accepted_roles = func_get_args();
		// Accepted roles were passed as array
		if (count($accepted_roles) == 1 AND is_array($accepted_roles[0]))
		{
			$accepted_roles = $accepted_roles[0];
		}
		if (Kohana::$environment === Kohana::DEVELOPMENT AND count(array_intersect($accepted_roles, ['guest', 'customer'])))
		{
			throw new Kohana_Exception('Не используйте "guest" / "customer" для проверки прав доступа. Такая проверка на самом деле является проверкой на принадлежность к более привелигерованной роли, и любые новые добавленные роли тоже будут подходить под эту проверку, что может приводить к ошибкам безопасности.');
		}
		$user = Auth::instance()->get_user();
		$role = ($user === NULL) ? 'guest' : $user->role;
		return in_array($role, $accepted_roles);
	}

	public static function user_is_logged_in()
	{
		$user = Auth::instance()->get_user();

		return ($user !== NULL AND $user instanceof Model_User AND $user->loaded());
	}

}
