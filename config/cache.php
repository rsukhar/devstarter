<?php defined('SYSPATH') or die('No direct script access.');
return
	[
	'file'    =>
		[
		'driver'             => 'file',
		'cache_dir'          => BASEPATH.'cache',
		'default_expire'     => 86400,
		'ignore_on_delete'   => [],
		]
	];