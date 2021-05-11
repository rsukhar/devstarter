<?php
/**
 * Created by PhpStorm.
 * User: soulwest
 * Date: 11.10.17
 * Time: 16:38
 */

class Kohana_Exception extends Kohana_Kohana_Exception {
	public static function _handler($e)
	{
		if (Kohana::$environment !== Kohana::PRODUCTION)
		{
			Kohana_Exception::$error_view = 'admin/errors/kohana_error';
			return parent::_handler($e);
		}
		$response = Response::factory();

		// Формируем url вместе с параметрами на котором произошла ошибка
		$url = URL::current_url();
		$query_params = Request::initial()->query();
		if ( ! empty($query_params))
		{
			$url = $url.'?'.HTTP::build_query($query_params);
		}

		$view = View::factory('admin/errors/default');
		Mailer::send('error_report', Settings::get('general.admin_email', ''), 'Cообщение об ошибке',
			[
				'code' => $e->getCode(),
				'err_message' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'url' => $url,
			]
		);

		$response->status(500);
		$response->body($view->render());

		return $response;
	}
}