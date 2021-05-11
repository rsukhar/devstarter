<?php

// Set the PHP error reporting level. If you set this in php.ini, you remove this.
if (isset($_SERVER['HTTP_HOST']))
{
	if (preg_match('/(.*?)devstarter\.local/', $_SERVER['HTTP_HOST']))
	{
		$_SERVER['KOHANA_ENV'] = 'development';
	}
}
if (isset($_SERVER['KOHANA_ENV']) AND ($_SERVER['KOHANA_ENV'] === 'development' OR $_SERVER['KOHANA_ENV'] === 'staging'))
{
	error_reporting(E_ALL | E_STRICT);
}
else
{
	error_reporting(E_ALL ^ E_NOTICE);
}

// Блокируем все запросы, которые обращаются по IP в обход хоста
if (PHP_SAPI != 'cli' AND preg_match('~^[\d\.]+$~', (string) $_SERVER['HTTP_HOST']))
{
	header('HTTP/1.0 403 Forbidden');
	die('<html><body><h1>403 Forbidden</h1><p>The server has refused to fulfill your request.</p></body></html>');
}

// Preserving cases when index.php is included from somewhere else
chdir(__DIR__);

// The default extension of resource files
define('EXT', '.php');

// Set the base path
define('BASEPATH', realpath(__DIR__.DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR);
// SYSPATH: Koseven system directory
define('SYSPATH', realpath(BASEPATH.'vendor/koseven/system').DIRECTORY_SEPARATOR);

// MODPATH: Kohana modules directory
define('MODPATH', realpath(BASEPATH.'vendor/koseven/modules').DIRECTORY_SEPARATOR);

// APPPATH
define('APPPATH', BASEPATH);
define('DOCROOT',  APPPATH.'www'.DIRECTORY_SEPARATOR);
define('DOCROOT_WITHOUT_SLASH',  APPPATH.'www');

// Define the start time of the application, used for profiling.
if ( ! defined('KOHANA_START_TIME'))
{
	define('KOHANA_START_TIME', microtime(TRUE));
}

// Define the memory usage at the start of the application, used for profiling.
if ( ! defined('KOHANA_START_MEMORY'))
{
	define('KOHANA_START_MEMORY', memory_get_usage());
}

// Load the core Kohana class
require SYSPATH.'classes/Kohana/Core'.EXT;
require SYSPATH.'classes/Kohana'.EXT;

date_default_timezone_set('Europe/Moscow');
setlocale(LC_ALL | LC_NUMERIC, 'ru_RU.utf8');
spl_autoload_register(['Kohana', 'auto_load']);
ini_set('unserialize_callback_func', 'spl_autoload_call');
mb_substitute_character('none');

// -- Configuration and initialization -----------------------------------------

I18n::lang('ru-ru');

if (isset($_SERVER['SERVER_PROTOCOL']))
{
	// Replace the default protocol.
	HTTP::$protocol = $_SERVER['SERVER_PROTOCOL'];
}
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}
else
{
	Kohana::$environment = Kohana::PRODUCTION;
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
Kohana::init([
	'base_url' => '/',
	'index_file' => '',
	'profile' => Kohana::$environment === Kohana::DEVELOPMENT,
	'cache_dir' => BASEPATH.'cache',
	'cache_life' => 60,
	'caching' => (getenv('KOHANA_DISABLE_CACHE') != TRUE AND Kohana::$environment === Kohana::PRODUCTION),
	'errors' => Kohana::$environment !== Kohana::PRODUCTION, // SHOW notice on dev and stagging ENV //TODO: problem with empty vars in render_element
]);

// Attach the file write to logging
Kohana::$log->attach(new Log_File(BASEPATH.'logs'));

// Attach a file reader to config
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
$kohana_modules = [];

if (Kohana::$environment >= Kohana::STAGING AND is_dir(BASEPATH.'.dev'))
{
	// Development tools
	$kohana_modules['dev'] = BASEPATH.'.dev';
}

$kohana_modules += [
	'database' => MODPATH.'database',
	'auth' => MODPATH.'auth',
	'image' => MODPATH.'image',
];
if (PHP_SAPI == 'cli')
{
	$kohana_modules += [
		'minion' => MODPATH.'minion', // CLI Tasks
	];
}
$kohana_modules += [
	'orm' => MODPATH.'orm', // Object Relationship Mapping
	'cache' => MODPATH.'cache', // Caching with multiple backends
];

Kohana::modules($kohana_modules);
unset($kohana_modules);

// Setting Cookie Salt
Cookie::$salt = 'devstarter-salt-894132';
Cookie::$domain = '.'.URL::primary_domain(FALSE);
Cache::$default = 'file';

// Loading routes
if (file_exists(APPPATH.'routing.php'))
{
	require_once APPPATH.'routing.php';
}

if (PHP_SAPI == 'cli')
{
	// Handling command-line task
	class_exists('Minion_Task') OR die('Please enable the Minion module for CLI support.');
	set_exception_handler(['Minion_Exception', 'handler']);
	Minion_Task::factory(Minion_CLI::options())->execute();
}
else
{
	if (file_exists( __DIR__ . '/maintenance.html' ))
	{
		include __DIR__ . '/maintenance.html';
		return;
	}

	// Handling web request
	echo Request::factory(TRUE, [], FALSE)
		->execute()
		->send_headers(TRUE)
		->body();

}