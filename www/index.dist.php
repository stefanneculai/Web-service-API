<?php
/**
 * Web server entry point for the Vangelis Application.
 *
 * @package     Vangelis.Application
 *
 * @copyright   Copyright (C) {COPYRIGHT}. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// Bootstrap the application.
$path = getenv('VANGELIS_HOME');
if ($path)
{
	require_once $path . '/bootstrap.php';
}
else
{
	require_once realpath(__DIR__ . '/../code/bootstrap.php');
}

try
{
	// Instantiate the application.
	$application = JApplicationWeb::getInstance('VangelisApplicationWeb');

	// Set the default JInput class for the application to use JSON input.
// 	$application->input = new JInputJson;

	// Store the application.
	JFactory::$application = $application;

	// Execute the application.
	$application
// 		->loadSession()
// 		->loadDatabase()
		->loadRouter()
		->execute();
}
catch (Exception $e)
{
	// Set the server response code.
	header('Status: 500', true, 500);

	// An exception has been caught, echo the message and exit.
	echo json_encode(array('message' => $e->getMessage(), 'code' => $e->getCode(), 'type' => get_class($e)));
	exit;
}
