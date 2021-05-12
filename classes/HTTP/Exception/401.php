<?php defined('SYSPATH') OR die('No direct script access.');

class HTTP_Exception_401 extends Kohana_HTTP_Exception_401 {

	public function get_response()
	{
		$response = Response::factory();
		$response
			->status($this->getCode())
			->body(Request::factory('error/401')->execute());
		return $response;
	}

}
