<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Controller extends Kohana_Controller {
	/**
	 * @var array errors container
	 */
	protected $_errors = array();

	/**
	 * @var Auth_ORM Auth instance
	 */
	protected $_auth = NULL;

	/**
	 * @var Model_User user object
	 */
	protected $_user = NULL;

	/**
	 * Adds Error to _errors array
	 *
	 * @param string|array $message Error message or code to obtain error message from messages file
	 * @param string $field Relevant field name (to properly put it to frontend later)
	 *
	 * @return bool
	 */
	protected function add_error($message, $field = NULL)
	{
		if (is_string($message) AND preg_match('~^validation\.([a-z0-9\_]+)~u', $message, $matches))
		{
			$field = isset($field) ? $field : $matches[1];
			$message = Kohana::message('errors', $message, $message);
		}
		$this->_errors[$field] = $message;

		return TRUE;
	}

	protected function has_errors()
	{
		return (count($this->_errors) != 0);
	}

	protected function has_no_errors()
	{
		return (count($this->_errors) == 0);
	}

	public function before()
	{
		parent::before();

		// Getting the auth and filling the related template settings
		$this->_auth = Auth::instance();
		if ($this->_auth->logged_in())
		{
			$this->_user = $this->_auth->get_user();
			if (in_array($this->_user->status, array('blocked')))
			{
				$this->_auth->logout();
			}
		}
	}

	/**
	 * Executes the given action and calls the [Controller::before] and [Controller::after] methods.
	 *
	 * Can also be used to catch exceptions from actions in a single place.
	 *
	 * 1. Before the controller action is called, the [Controller::before] method
	 * will be called.
	 * 2. Next the controller action will be called.
	 * 3. After the controller action is called, the [Controller::after] method
	 * will be called.
	 * @return Response
	 * @throws HTTP_Exception
	 */
	public function execute()
	{
		// Execute the "before action" method
		$error_code = $this->before();

		if ($error_code == 0) // Only if error code is NULL
		{
			// Determine the action to use
			$action = 'action_'.$this->request->action();

			// If the action doesn't exist, it's a 404
			if ( ! method_exists($this, $action))
			{
				throw HTTP_Exception::factory(404,
					'The requested URL :uri was not found on this server.',
					array(':uri' => $this->request->uri())
				)->request($this->request);
			}

			// Execute the action itself
			$this->{$action}();
		}

		// Execute the "after action" method
		$this->after();

		// Return the response
		return $this->response;
	}

}
