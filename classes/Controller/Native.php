<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Native extends Controller_Template {

	/**
	 * @var View General template
	 */
	public $template = 'admin/template';

	/**
	 * @var Assets Instance of Assets class
	 */
	protected $assets;

	/**
	 * @var Model_User User Instance
	 */
	protected $_user;

	/**
	 * Adds a error to _errors array
	 *
	 * @param string $message Error code
	 * @param string $field Field name (if not set is taken from error code's second level) or custom text
	 *
	 * @return
	 * @throws HTTP_Exception
	 */
	protected function add_error($message, $field = NULL)
	{
		// In case of access restrictions showing the relevant screen
		if ($message == 'access.401')
		{
			// throw HTTP_Exception::factory(401, 'Authorization required');
			$request_uri = Arr::get($_SERVER, 'REQUEST_URI', '/');
			$sign_in_url = '/sign_in/'.(($request_uri === '/') ? '' : ('?'.http_build_query(array('p' => $request_uri))));
			$this->redirect($sign_in_url);
		}
		elseif ($message == 'access.403')
		{
			throw HTTP_Exception::factory(403, 'Access forbidden');
		}
		elseif ($message == 'access.404')
		{
			throw HTTP_Exception::factory(404, 'Page not found');
		}

		return parent::add_error($message, $field);
	}

	public function action_index()
	{
		HTTP::redirect('/sign_in/');
	}

	public function before()
	{
		parent::before();

		$this->assets = Assets::instance();
		$this->assets->add('base');
		$this->template->body_classes = '';

		// Internal content template
		$this->template->content = View::factory();
		$this->template->content->bind('errors', $this->_errors);
		$this->template->content->set('values', []);
	}

	public function after()
	{
		$this->template->bind('user', $this->_user);

		parent::after();
	}

} // End Controller_Native
