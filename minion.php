#!/usr/bin/env php
<?php

putenv('KOHANA_DISABLE_CACHE=1');

if (getenv('KOHANA_ENV'))
{
	$_SERVER['KOHANA_ENV'] = getenv('KOHANA_ENV');
}
else
{
	switch (__DIR__)
	{
		case '/srv/devstarter.local':
			$_SERVER['KOHANA_ENV'] = 'DEVELOPMENT';
			break;
		default:
			$_SERVER['KOHANA_ENV'] = 'PRODUCTION';
	}
}

if (is_dir(__DIR__.'/www'))
{
	// Handling application-specific index file
	chdir(__DIR__.'/www');
}

include 'index.php';
