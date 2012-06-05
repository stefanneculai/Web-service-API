<?php
/**
 * @package     WebService.Application
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * WebService 'content' Delete method.
 *
 * @package     WebService.Application
 * @subpackage  Controller
 * @since       1.0
 */
class WebServiceControllerV1JsonContentDelete extends JControllerBase
{

	/**
	 * @var    string  The content id. It may be numeric id or '*' if all content is needed
	 * @since  1.0
	 */
	protected $id = '*';

	/**
	 * @var    string  Max results per page
	 * @since  1.0
	 */
	protected $since = '1970-01-01';

	/**
	 * @var    string  Max results per page
	 * @since  1.0
	 */
	protected $before = 'now';

	/**
	 * @var    integer  Supress response codes. 401 for Unauthorized; 200 for OK
	 * @since  1.0
	 */
	protected $responseCode = 401;

	/**
	 * Get route parts from the input or the default one
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function getContentId()
	{
		// Get route from the input
		$route = $this->input->get->getString('@route');

		if (preg_match("/json$/", $route) >= 0)
		{
			$route = str_replace('.json', '', $route);
		}

		// Break route into more parts
		$routeParts = explode('/', $route);

		// Contet is not refered by a number id
		if ( count($routeParts) > 0 && (!is_numeric($routeParts[0]) || $routeParts[0] < 0) && !empty($routeParts[0]))
		{
			throw new InvalidArgumentException('Unknown content path.', $this->responseCode);
		}

		// All content is refered
		if ( count($routeParts) == 0 || strlen($routeParts[0]) === 0 )
		{
			return $this->id;
		}

		// Specific content id
		return $routeParts[0];
	}

	/**
	 * Get the since date limitation from input or the default one
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function getSince()
	{
		$since = $this->input->get->getString('since');

		if (isset($since))
		{
			if (strtotime($since) != false)
			{
				return strptime(strtotime($since), '%d/%m/%Y');
			}

			throw new InvalidArgumentException('Since should be a valid date. By default all the results are returned.', $this->responseCode);
		}
		else
		{
			return strptime(strtotime($this->since), '%d/%m/%Y');
		}

	}

	/**
	 * Get the before date limitation from input or the default one
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function getBefore()
	{
		$before = $this->input->get->getString('before');

		if (isset($before))
		{
			// Notice: PHP 5.1 would return -1
			if (strtotime($before) != false)
			{
				return strptime(strtotime($before), '%d/%m/%Y');
			}

			throw new InvalidArgumentException(
					'Before should be a valid date. By default all the results until the current date are returned.',
					$this->responseCode
			);
		}
		else
		{
			return strptime(strtotime($this->before), '%d/%m/%Y');
		}
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
	 * Init parameters
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function init()
	{

		$this->checkSupressResponseCodes();

		// Content id
		$this->id = $this->getContentId();

		// Since
		$this->since = $this->getSince();

		// Before
		$this->before = $this->getBefore();
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

		$this->app->setBody(json_encode('Content Delete'));
	}

}
