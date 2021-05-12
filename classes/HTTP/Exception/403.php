<?php defined('SYSPATH') OR die('No direct script access.');

class HTTP_Exception_403 extends Kohana_HTTP_Exception_403 {

	public function get_response()
	{
		$response = Response::factory();
		$response
			->status($this->getCode())
			->body(Request::factory('error/403')->execute());
		return $response;
	}

}
