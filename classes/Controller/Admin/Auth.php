<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Auth extends Controller_Admin {

	public function before()
	{
		parent::before();
	}

	/**
	 * @throws Kohana_Exception
	 */
	public function action_sign_in()
	{
		$this->template->title = 'Авторизация — DevStarter';
		$this->template->header_exists = FALSE;
		$this->template->content->set_filename('admin/auth/sign_in');
		$this->assets->add('auth.sign_in');
		$this->template->body_classes .= 'auth-sign_in style_alternate';

		if (count($this->request->post()) == 0)
		{
			return NULL;
		}

		$this->template->content->values = $this->request->post();

		$login = $this->request->post('login');
		$password = $this->request->post('password');

		if ( ! isset($login) OR empty($login))
		{
			return $this->add_error('Поле не может быть пустым', 'login');
		}

		if ( ! isset($password) OR empty($password))
		{
			return $this->add_error('Поле не может быть пустым', 'password');
		}

		$field = (Valid::email($login)) ? 'email' : 'username';

		/**
		 * @var Model_User $user
		 */
		$user = ORM::factory('User')
			->where($field, '=', $login)
			->find();

		if ( ! $user->loaded())
		{
			return $this->add_error('Пользователя с таким логином не существует', 'login');
		}

		if ( ! $this->_auth->login($user, $password, TRUE))
		{
			return $this->add_error('Неверный пароль', 'password');
		}

		$this->redirect('/admin/students/');
	}

	/**
	 * GET /sign_out/
	 */
	public function action_sign_out()
	{
		if ($this->_auth->logged_in())
		{
			$this->_auth->logout();
		}
		$this->redirect('/sign_in/');
	}
}
