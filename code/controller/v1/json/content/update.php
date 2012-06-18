<?php
/**
 * @package     WebService.Application
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * WebService 'content' Update method.
 *
 * @package     WebService.Application
 * @subpackage  Controller
 * @since       1.0
 */
class WebServiceControllerV1JsonContentUpdate extends JControllerBase
{
	/**
	 * @var    string  The content id. It may be numeric id or '*' if all content is needed
	 * @since  1.0
	 */
	protected $id = '*';

	/**
	 * @var    array  Data fields
	 * @since  1.0
	 */
	protected $dataFields = array(
			'field1' => null,
			'field2' => null,
			'field3' => null,
			'field4' => null,
			'field5' => null
			);

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

		// Break route into more parts
		$routeParts = explode('/', $route);

		// Content is not refered by a number id
		if (count($routeParts) > 0 && (!is_numeric($routeParts[0]) || $routeParts[0] < 0) && !empty($routeParts[0]))
		{
			throw new InvalidArgumentException('Unknown content path.', $this->responseCode);
		}

		// All content is refered
		if ( count($routeParts) == 0 || strlen($routeParts[0]) === 0 )
		{
			throw new InvalidArgumentException('Unknown content path.', $this->responseCode);
		}

		// Specific content id
		return $routeParts[0];
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
	 * Get mandatory fields from input
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function getDataFields()
	{
		foreach ($this->dataFields as $key => $value )
		{
			$field = $this->input->get->getString($key);
			if ( isset($field) )
			{
				$this->dataFields[$key] = $field;
			}
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
		// Check error codes
		$this->checkSupressResponseCodes();

		// Content id
		$this->id = $this->getContentId();

		// Init data fields
		$this->getDataFields();
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
		// Init request
		$this->init();

		// Returned data
		$data = $this->updateContent();

		// Parse the returned data
		$this->parseData($data);
	}

/**
	 * Create content
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 */
	protected function updateContent()
	{
		// Content model
		include_once JPATH_BASE . '/model/model.php';

		// Remove null values from fields
		foreach ($this->dataFields as $key => $value)
		{
			if ($value == null)
			{
				unset($this->dataFields[$key]);
			}
		}

		if (count($this->dataFields) == 0)
		{
			throw new InvalidArgumentException('Invalid request. Nothing to update.', $this->responseCode);
		}

		$fields = implode(',', array_keys($this->dataFields));

		// New content model
		$model = new WebServiceContentModelBase;

		// Get content state
		$modelState = $model->getState();

		// Set content id
		$modelState->set('content.id', $this->id);

		// Set content type that we need
		$modelState->set('content.type', 'general');

		// Set field list
		$modelState->set('content.fields', $fields);

		// Set each field
		foreach ($this->dataFields as $fieldName => $fieldContent)
		{
			$modelState->set('fields.' . $fieldName, $fieldContent);
		}

		$item = $model->updateItem();

		return $item;
	}

	/**
	 * Parse the returned data from database
	 *
	 * @param   boolean  $data  Request was successful or not
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function parseData($data)
	{
		// Check if the update was successful
		if ($data == false)
		{
			$data = "No such content";
		}
		else
		{
			$data = "Content was updated successfully";
		}

		$this->app->setBody(json_encode($data));
	}

}
