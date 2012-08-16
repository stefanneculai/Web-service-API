<?php
/**
 * Bootstrap file for the Joomla Web Service Application.
 *
 * @package    WebService.Application
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// Set the Joomla execution flag.
define('_JEXEC', 1);

// Allow the application to run as long as is necessary.
ini_set('max_execution_time', 0);

// Note, you would not use these settings in production.
error_reporting(E_ALL);
ini_set('display_errors', true);

// Ensure that required path constants are defined.
if (!defined('JPATH_BASE'))
{
	define('JPATH_BASE', realpath(__DIR__));
}

Phar::loadPhar(realpath(JPATH_BASE . '/../libraries/joomla.phar'));

// Define the path for the Joomla Platform.
if (!defined('JPATH_PLATFORM'))
{
	$platform = getenv('JPLATFORM_HOME');
	if ($platform)
	{
		define('JPATH_PLATFORM', realpath($platform));
	}
	else
	{
		// Platform directory instead of phar: define('JPATH_PLATFORM', JPATH_BASE . '/../../joomla-platform/libraries');
		define('JPATH_PLATFORM', 'phar://joomla.phar/libraries');
	}
}

// Ensure that required path constants are defined.
if (!defined('JPATH_CONFIGURATION'))
{
	$path = getenv('WEBSERVICE_CONFIG');
	if ($path)
	{
		define('JPATH_CONFIGURATION', realpath($path));
	}
	else
	{
		define('JPATH_CONFIGURATION', realpath(dirname(JPATH_BASE) . '/config'));
	}
}

define('JPATH_SITE', realpath(__DIR__) . '/code');
define('JPATH_CACHE', JPATH_SITE . '/cache');

// Import the platform(s).
require_once JPATH_PLATFORM . '/import.php';

// Make sure that the Joomla Platform has been successfully loaded.
if (!class_exists('JLoader'))
{
	exit('Joomla Platform not loaded.');
}

define('UPLOADS', 'http://ws.localhost/uploads/');

// Setup the autoloader for the WebService application classes.
JLoader::registerPrefix('J', __DIR__ . '/joomla', true);
JLoader::registerPrefix('J', JPATH_PLATFORM . '/joomla');
JLoader::registerPrefix('WebService', __DIR__);