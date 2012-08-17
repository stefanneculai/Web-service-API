<?php
/**
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * The class for Screenshot GET requests
 *
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @since       1.0
 */
class WebServiceControllerV1JsonScreenshotsGet extends WebServiceControllerV1JsonBaseGet
{
	/**
	 * Get mandatory fields from input
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function getMandatoryFields()
	{
		// Search for mandatory fields in input query
		foreach ($this->mandatoryFields as $key => $value )
		{
			// Check if mandatory field is set
			$field = $this->input->get->getString($key);
			if ( isset($field) )
			{
				$this->mandatoryFields[$key] = $field;
			}
			else
			{
				$this->app->errors->addError("308", array($key));
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
		// Set the fields
		$this->readFields();

		// Content id
		$this->id = $this->getContentId();

		// Returned fields
		$this->fields = $this->getFields();

		// Get action
		$this->action = $this->getAction();

		// Get the mandatory fields
		$this->getMandatoryFields();

		// Map fields according to the application database
		if ($this->fields != null)
		{
			$this->fields = $this->mapFieldsIn($this->fields);
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
		// Init request
		$this->init();

		if ($this->app->errors->errorsExist() == true)
		{
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		if ($this->itemExists($this->mandatoryFields['application_id'], 'application'))
		{
			// Get content state
			$modelState = $this->model->getState();

			// Set content type that we need
			$modelState->set('content.type', 'application');
			$modelState->set('content.id', $this->mandatoryFields['application_id']);

			// Load application
			$application = $this->model->getItem();

			// Check if application was successfully loaded
			if ($application == false)
			{
				$this->app->setBody(json_encode(false));
			}
			else
			{
				$this->parseData($application);
			}
		}
		else
		{
			$this->app->errors->addError('202');
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}
	}

	/**
	 * Parse the returned data from database
	 *
	 * @param   mixed  $data  A JContent object, an array of JContent or a boolean.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function parseData($data)
	{
		// Get only requested fields
		if ( is_integer($data) == false )
		{
			// There is no content for the request
			if ($data == false)
			{
				$data = null;
			}

			$data = $this->pruneFields($data, $this->fields);
		}

		// Check if screenshots are set
		if (is_array($data) && isset($data['screenshots']))
		{
			$data = $data['screenshots'];

			if ($data == null)
			{
				$data = new stdClass;
			}
		}

		// Check if a specific screenshot should be returned
		if (strcmp($this->id, '*') !== 0)
		{
			$newData = new stdClass;

			foreach ($data->data as $key => $screenshot)
			{
				if ($screenshot->id == $this->id)
				{
					$newData = $screenshot;
					break;
				}
			}

			$data = $newData;
		}

		// Check if we should count screenshots
		if (strcmp($this->action, 'count') === 0)
		{
			$newData = new stdClass;
			$newData->count = count((array) $data);
			$data = $newData;
		}

		$this->app->setBody(json_encode($data));
	}
}
