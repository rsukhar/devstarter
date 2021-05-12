<?php defined('SYSPATH') or die('No direct access allowed.');

return [

	'driver' => 'ORM',
	'hash_method' => 'sha256',

	// This is the important line
	'hash_key' => 'devstarter-1234567',
	'lifetime' => 1209600,
	'session_type' => Session::$default,
	'session_key' => 'auth_user',

	// Username/password combinations for the Auth File driver
	'users' => []

];
