<?php defined('SYSPATH') or die('No direct script access.');

// Custom code tests
Route::set('devtests', 'devtests(/<action>(/<param1>(/<param2>(/<param3>))))')->defaults(array(
	'controller' => 'Devtests',
	'action' => 'index',
));

