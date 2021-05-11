<?php defined('SYSPATH') or die('No direct script access.');

/**
 * API-запросы
 */
Route::set('api', 'api/<controller>/<action>(/<param1>(/<param2>))', [
	'controller' => '[a-z_]+',
	'action' => '[a-zA-Z0-9_-]+',
	'param1' => '[a-zA-Z0-9_-]+',
	'param2' => '[a-zA-Z0-9_-]+',
])->filter(function ($route, $params, $request) {
	$params['directory'] = 'API';
	// Student
	if ($params['controller'] === 'Student')
	{
		// api/content/<action>
		if (in_array($params['action'], ['create']))
		{
			return $params;
		}
		// api/content/<action>/<id>
		elseif (in_array($params['action'], ['update', 'delete']) AND isset($params['param1']))
		{
			return array_merge($params, ['id' => (int) $params['param1']]);
		}
	}

	return FALSE;
});

// Native Auth
Route::set('auth', '<action>', [
	'action' => 'sign_in|sign_out|sign_up|verify_email|request_password_reset|reset_password|complete_sign_up',
])->defaults([
	'directory' => 'Native',
	'controller' => 'Auth',
]);

// Errors' routes (required for HMVC-requests from Error Exception)
Route::set('errors', 'error/<action>', array(
	'action' => '401|403|404',
))->defaults(array(
	'controller' => 'Native',
));
// Modules assets
Route::set('modules-assets', 'assets/<file>', array(
	'file' => '.+\.(js|png|jpg|css|otf|svg|ttf|woff|woff2)',
))->defaults(array(
	'controller' => 'Assets',
	'action' => 'handle',
));

Route::set('admin', 'admin(/<controller>(/<id>)(/<action>(/<param1>)(/<param2>)))', [
	'controller' => 'student',
	'action' => '[a-z_]+',
	'id' => '[0-9]+',
	'param1' => '[0-9a-zA-Z\d\.\_\-]+',
	'param2' => '[0-9a-zA-Z\d\.\_\-]+',
])->filter(function ($route, $params, $request) {
	if ($params['controller'] === 'Student')
	{
		if (isset($params['id']) AND ! in_array($params['action'], ['index']))
		{
			$params = array_merge($params, ['tab' => $params['action']]);
		}
		$params['action'] = 'index';
	}

	return array_merge($params, [
		'directory' => 'Admin',
	]);
})->defaults([
	'directory' => 'Admin',
	'controller' => 'Content',
	'action' => 'index',
]);


// Для всех несуществующих страниц в тестовых целях выводим 404 ошибку
Route::set('node', '<url>', [
	'url' => '.*?',
])->defaults([
	'directory' => 'Native',
	'action' => '404',
]);


