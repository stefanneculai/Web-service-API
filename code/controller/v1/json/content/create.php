<?php
/**
 * @package     WebService.Application
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * WebService 'content' Create method.
 *
 * @package     WebService.Application
 * @subpackage  Controller
 * @since       1.0
 */
class WebServiceControllerV1JsonContentCreate extends JControllerBase
{
	/**
	 * @var    array  Required data
	 * @since  1.0
	 */
	protected $mandatoryData = array(
			'field1' => '',
			'field2' => '',
			'field3' => ''
			);

	/**
	 * @var    array  Mandatory data
	 * @since  1.0
	*/
	protected $optionalData = array(
			'field4' => '',
			'field5' => ''
			);

	/**
	 * @var    integer  Supress response codes. 401 for Unauthorized; 200 for OK
	 * @since  1.0
	 */
	protected $responseCode = 401;

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
	 * Init parameters
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function init()
	{
		$this->checkSupressResponseCodes();

		$this->getMandatoryData();
		$this->getOptionalData();
	}

	/**
	 * Get mandatory fields from input
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function getMandatoryData()
	{
		foreach ($this->mandatoryData as $key => $value )
		{
			$field = $this->input->get->getString($key);
			if ( isset($field) )
			{
				$this->mandatoryData[$key] = $field;
			}
			else
			{
				throw new InvalidArgumentException($key . ' field is mandatory.', $this->responseCode);
			}
		}
	}

	/**
	 * Get optional fields from input
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function getOptionalData()
	{
		foreach ($this->optionalData as $key => $value )
		{
			$field = $this->input->get->getString($key);
			if ( isset($field) )
			{
				$this->optionalData[$key] = $field;
			}
		}
	}

	/**
	 * Controller logic
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function execute()
	{
		$this->init();
		$this->app->setBody(json_encode('Content Create'));
	}

}
