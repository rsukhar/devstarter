<?php defined('SYSPATH') or die('No direct script access.');

class Controller_API extends Controller {

	/**
	 * Output formats supported by this controller
	 */
	protected $_supported_formats = array(
		'json',
	);

	/**
	 * @var array Output data that will be displayed through AJAX
	 */
	public $output = array();

	/**
	 * @var array success message title and text container
	 */
	protected $_success_message = array();

	/**
	 * Returns formatted error response containing errors occured
	 *
	 * @return array formatted error response
	 */
	protected function get_error_response()
	{
		return array(
			'success' => 0,
			'errors' => $this->_errors,
		);
	}

	/**
	 * Returns formatted success response containing output data
	 *
	 * @param mixed $data output data
	 * @return array formatted success response
	 */
	protected function get_success_response($data)
	{
		return array(
			'success' => 1,
			'message' => $this->_success_message,
			'data' => $data,
		);
	}

	public function set_success_message($message)
	{
		$this->_success_message = $message;
	}


	/**
	 * Checks if requested format is supported by API
	 * Sets parameter $this->_output_data_only from request to determine if API was called for testing purposes
	 */
	public function before()
	{
		parent::before();

		$this->_auth = Auth::instance();

		if ($this->_auth->logged_in())
		{
			$this->_user = $this->_auth->get_user();
		}

		if ($this->_user AND in_array($this->_user->status, array('blocked')))
		{
			$this->_auth->logout();
		}

		// Test to ensure the format requested is supported
		$format = $this->request->param('format', 'json');
		if ( ! empty($format) AND ! in_array($format, $this->_supported_formats))
		{
			throw new Kohana_Exception('controller_api:wrong_format', array(
				':format' => $this->request->param('format'),
			));
		}

		// If we get request with content-type JSON, trying to get some json encoded data from request body
		if ($this->request->method() === 'POST')
		{
			$body = $this->request->body();
			$content_type_is_json =  stripos($this->request->headers('Content-Type'), 'application/json') !== FALSE;
			if ($content_type_is_json AND ! empty($body) AND Valid::json($body))
			{
				// Parse json from request body to post data
				$body = json_decode($body, TRUE);
				$post = $this->request->post();
				foreach (Arr::kohana_flatten($body, TRUE) as $key => $value)
				{
					Arr::set_path($post, $key, $value);
				}
				$this->request->post($post);
			}
		}
	}

	public function after()
	{
		$this->response->headers('Cache-Control', 'private');

		if (count($this->_errors) > 0)
		{
			// If errors are present - echo them
			$output = $this->get_error_response();
		}
		else
		{
			$output = $this->get_success_response($this->output);
		}

		$format = $this->request->param('format', 'json');

		if ($format == 'json')
		{
			$this->response->headers('Content-Type', 'application/json');
			$this->response->body(json_encode($output));
		}
		else
		{
			throw new Kohana_Exception('controller_api:wrong_format', array(':format' => $this->request->param('format')));
		}

		parent::after();
		$this->check_cache();
	}

	public function add_error($message, $field = NULL)
	{
		parent::add_error($message, $field);

		// In case of access restrictions showing the relevant screen
		if (is_string($message) AND preg_match('/access\.(?<code>\d+)/i', $message, $message_arr))
		{
			throw HTTP_Exception::factory($message_arr['code']);
		}

		return TRUE;
	}

	public function execute()
	{
		try
		{
			parent::execute();
		}
		catch (HTTP_Exception $e)
		{
			$this->after();
		}

		return $this->response;
	}

}
