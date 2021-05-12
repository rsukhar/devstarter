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
		// api/content/<action>/<id>
		if (in_array($params['action'], ['delete']) AND isset($params['param1']))
		{
			return array_merge($params, ['id' => (int) $params['param1']]);
		}
	}

	return FALSE;
});

// Native Auth
Route::set('auth', '<action>', [
	'action' => 'sign_in|sign_out',
])->defaults([
	'directory' => 'Admin',
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

Route::set('admin-models', 'admin/<controller>s(/<id>)(/<action>)', [
	'controller' => 'student',
	'id' => '[0-9]+',
	'action' => 'create|update',
])->defaults([
	'directory' => 'Admin',
	'controller' => 'Student',
	'action' => 'list',
]);


// Для всех несуществующих страниц в тестовых целях выводим 404 ошибку
Route::set('index', '<url>', [
	'url' => '.*?',
])->defaults([
	'controller' => 'Native',
	'action' => 'index',
]);


