<?php
/**
 * @package     WebService.Application
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Web Service application response codes.
 *
 * @package     WebService.Application
 * @subpackage  Application
 * @since       1.0
 */
class WebServiceApplicationWebErrors
{
	/**
	 * @var    integer  Response code. 200 for OK and 400 for Bad Request
	 * @since  1.0
	 */
	protected $responseCode = 400;

	/**
	 * The application object.
	 *
	 * @var    JApplicationBase
	 * @since  1.0
	 */
	protected $app;

	/**
	 * The input object.
	 *
	 * @var    JInput
	 * @since  1.0
	 */
	protected $input;

	/**
	 * Error status
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $errors = false;

	/**
	 * An array of WebServiceError
	 *
	 * @var array
	 */
	protected $errorsArray = array();

	/**
	 * An associative array for response codes messages
	 *
	 * @var array
	 */
	protected $errorsMap;

	/**
	 * Instantiate the controller.
	 *
	 * @param   JApplicationBase  $app    The application object.
	 * @param   JInput            $input  The input object.
	 *
	 * @since  12.1
	 */
	public function __construct($app, $input)
	{
		// Setup dependencies.
		$this->app = isset($app) ? $app : $this->loadApplication();
		$this->input = isset($input) ? $input : $this->loadInput();

		// Read the configuration file for errors
		$this->errorsMap = $this->fetchErrorsData();
	}

	/**
	 * Add custom parameters to the error message
	 *
	 * @param   array  $errorObj  An associative array with the error message
	 * @param   array  $params    The parameters to pass to the error message
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	private function customMessage($errorObj, $params)
	{
		// Replace the parameter from message with the string from $params[$#]
		for ($i = 1;; $i++)
		{
			if (strstr($errorObj['message'], '$' . $i) !== false)
			{
				$errorObj['message'] = str_replace('$' . $i, $params[$i - 1], $errorObj['message']);
			}
			else
			{
				return $errorObj;
			}
		}
		// @codeCoverageIgnoreStart
	}
		// @codeCoverageIgnoreEnd

	/**
	 * Add error to the error list
	 *
	 * @param   string  $code    The code of the error
	 * @param   array   $params  An array with custom messages
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function addError($code, $params = array())
	{
		// Set erros status to true
		$this->errors = true;

		// Check if the error exist in configuration file
		if (property_exists($this->errorsMap, $code))
		{
			// Check if there is any custom message and set it
			if (count($params) > 0)
			{
				$message = get_object_vars($this->errorsMap->{$code});
				array_push($this->errorsArray, $this->customMessage($message, $params));
			}
			else
			{
				array_push($this->errorsArray, get_object_vars($this->errorsMap->{$code}));
			}
		}
		// The error is unknown. The unknown error code is thrown
		else
		{
			array_push($this->errorsArray, $this->unknownError($code));
		}
	}

	/**
	 * An array with the existing errros
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getErrors()
	{
		return $this->errorsArray;
	}

	/**
	 * Returns the error status
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function errorsExist()
	{
		return $this->errors;
	}

	/**
	 * An integer with the response code
	 *
	 * @return  integer
	 *
	 * @since   1.0
	 */
	public function getResponseCode()
	{
		return $this->responseCode;
	}

	/**
	 * Unknown error code
	 *
	 * @param   string  $code  The code of the error
	 *
	 * @return  array
	 *
	 * @since 1.0
	 */
	protected function unknownError($code)
	{
		$error['code'] = $code;
		$error['message'] = 'This error is not known';
		$error['more_info'] = 'A link where to find more info about an unknown error';
		$error['response_code'] = '400';

		return $error;
	}

	/**
	 * Check the input for supress_response_codes = true in order to supress the error codes.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function checkSupressResponseCodes()
	{
		$errCode = $this->input->get->getString('suppress_response_codes');

		if (isset($errCode))
		{
			$errCode = strtolower($errCode);

			if (strcmp($errCode, 'true') === 0)
			{
				$this->responseCode = 200;
				return;
			}

			if (strcmp($errCode, 'false') === 0)
			{
				$this->responseCode = 400;
				return;
			}

			$this->addError("306");
			return;
		}
	}

	/**
	 * Fetch the errros data for the application.
	 *
	 * @return  object  An object to be loaded into the application configuration.
	 *
	 * @since   1.0
	 *
	 */
	protected function fetchErrorsData()
	{
		// Load the configuration file into an object.
		return $this->app->readConfig('errors');
	}
}
