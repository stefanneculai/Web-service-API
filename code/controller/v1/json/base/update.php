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
class WebServiceControllerV1JsonBaseUpdate extends WebServiceControllerV1Base
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
	protected $dataFields = array();

	/**
	 * @var    string  The action to be done
	 * @since  1.0
	 */
	protected $action = null;

	/**
	 * Get the before date limitation from input or the default one
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function getAction()
	{
		$action = $this->input->get->getString('action');
		if (isset($action))
		{
			return $action;
		}

		return $this->action;
	}

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
			$this->app->errors->addError("301");
			return;
		}

		// All content is refered
		if ( count($routeParts) == 0 || strlen($routeParts[0]) === 0 )
		{
			$this->app->errors->addError("309");
			return;
		}

		// Specific content id
		return $routeParts[0];
	}

	/**
	 * Get fields from input
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
	 * Build data fields as a combination of mandatory and optional fields
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function buildFields()
	{
		foreach ($this->mandatoryFields as $key => $value)
		{
			$this->dataFields[$key] = null;
		}

		foreach ($this->optionalFields as $key => $value)
		{
			$this->dataFields[$key] = null;
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

		// Create the array with the fields that could be updated
		$this->buildFields();

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

		// Check for errors
		if ($this->app->errors->errorsExist() == true)
		{
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		// Returned data
		$data = $this->updateContent();

		// Parse the returned data
		$this->parseData($data);
	}

	/**
	 * Update content
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 * @throws  Exception
	 */
	protected function updateContent()
	{
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
			$this->app->errors->addError("101");
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		$fields = implode(',', $this->mapFieldsIn(array_keys($this->dataFields)));

		// New content model
		$model = new WebServiceModelBase;

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
			$modelState->set('fields.' . $this->mapIn($fieldName), $fieldContent);
		}

		try
		{
			$item = $model->updateItem();
		}
		catch (Exception $e)
		{
			throw $e;
		}

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
			$this->app->errors->addError("100");
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}
		else
		{
			$data = new stdClass;
			$data->id = $this->id;
		}

		$this->app->setBody(json_encode($data));
	}

}
