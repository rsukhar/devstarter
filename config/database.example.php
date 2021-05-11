<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
	'default' =>
		[
		'type' => 'MySQLi',
		'connection' => [
			'hostname' => 'localhost',
			'database' => '',
			'username' => '',
			'password' => '',
			'persistent' => FALSE,
			'variables' => [
				'group_concat_max_len' => 1000000,
			],
		],
		'table_prefix' => '',
		'charset' => 'utf8mb4',
		'caching' => FALSE,
		],
);
