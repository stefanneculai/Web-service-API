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
class WebServiceErrors
{
	/**
	 * @var    integer  Response code. 200 for OK and 401 for Unauthorized
	 * @since  1.0
	 */
	protected $responseCode = 200;

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
	 * Boolean showing if there is any error so far
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

		$this->errorsMap = $this->fetchErrorsData();
	}

	/**
	 * Add error to the error list
	 *
	 * @param   string  $code  The code of the error
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function addError($code)
	{
		$this->errors = true;
		$this->responseCode = 401;

		if (property_exists($this->errorsMap, $code))
		{
			array_push($this->errorsArray, get_object_vars($this->errorsMap->{$code}));
		}
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
	 * A boolean that tells if there are errors or not
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
		$error['response_code'] = '401';

		return $error;
	}

	/**
	 * Check the input for supress_response_codes = true in order to supress the error codes.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function checkSupressResponseCodes()
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
				$this->responseCode = 401;
				return;
			}

			throw new InvalidArgumentException('suppress_response_codes should be set to true or false', $this->responseCode);
		}
	}

	/**
	 * Fetch the errros data for the application.
	 *
	 * @return  object  An object to be loaded into the application configuration.
	 *
	 * @since   1.0
	 * @throws  RuntimeException if file cannot be read.
	 */
	protected function fetchErrorsData()
	{
		// Initialise variables.
		$config = array();

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

		// Set the configuration file path for the application.
		if (file_exists(JPATH_CONFIGURATION . '/errors.json'))
		{
			$file = JPATH_CONFIGURATION . '/errors.json';
		}
		else
		{
			// Default to the distribution configuration.
			$file = JPATH_CONFIGURATION . '/errors.dist.json';
		}

		if (!is_readable($file))
		{
			throw new RuntimeException('Errors file does not exist or is unreadable.');
		}

		// Load the configuration file into an object.
		$errors = json_decode(file_get_contents($file));

		if ($errors == null)
		{
			throw new RuntimeException('Configuration file cannot be decoded.');
		}

		return $errors;
	}
}
