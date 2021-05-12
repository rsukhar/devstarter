<?php defined('SYSPATH') or die('No direct access allowed.');

// Добавляем версию к URL файлов, чтобы сбрасывать кеш браузера при обновлениях
$app_version = '1.1';

return [
	'default' => [
		'jquery' => [
			'core' => [
				'file' => 'assets/admin/vendor/jquery/jquery-3.5.1.min.js',
			],
		],

		'base' => [
			'files' => [
				'https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap&subset=cyrillic,cyrillic-ext',
				'assets/admin/css/admin.css?v='.$app_version,
				'assets/admin/css/vof.css?v='.$app_version,
				'assets/admin/js/base/main.js?v='.$app_version,
				'assets/admin/js/base/vof.js?v='.$app_version,
			],
			'requires' => ['jquery.core', 'select2'],
		],

		'select2' => [
			'files' => [
				// 'assets/vendor/select2/css/select2.min.css',
				'assets/admin/vendor/select2/js/select2.full.min.js',
			],
			'requires' => ['jquery.core'],
		],

		'auth' => [
			'sign_in' => [
				'files' => [
					'assets/admin/js/auth/sign_in.js?v='.$app_version,
				],
				'requires' => ['base', 'jquery'],
			],
		],

		'student' => [
			'list' => [
				'file' => 'assets/admin/js/student/list.js?v='.$app_version,
				'requires' => ['base'],
			],
			'get' => [
				'file' => 'assets/admin/js/student/get.js?v='.$app_version,
				'requires' => ['base'],
			],
		],

		'settings' => [
			'settings' => [
				'file' => 'assets/admin/js/settings/content.js?v='.$app_version,
				'requires' => ['base'],
			],
		],
	],
];
