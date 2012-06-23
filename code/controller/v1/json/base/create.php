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
class WebServiceControllerV1JsonBaseCreate extends WebServiceControllerV1Base
{
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

		// Init mandatory fields
		$this->getMandatoryFields();

		// Init optional fields
		$this->getOptionalFields();
	}

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
	 * Get optional fields from input
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function getOptionalFields()
	{
		// Search for optional fields in input query
		foreach ($this->optionalFields as $key => $value )
		{
			$field = $this->input->get->getString($key);
			if ( isset($field) )
			{
				$this->optionalFields[$key] = $field;
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
		// Init
		$this->init();

		if ($this->app->errors->errorsExist() == true)
		{
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		$data = $this->createContent();

		$this->parseData($data);
	}

	/**
	 * Create content
	 *
	 * @return  JContent
	 *
	 * @since   1.0
	 */
	protected function createContent()
	{

		$fields = implode(',', $this->mapFieldsIn(array_keys($this->mandatoryFields)));
		$fields = $fields . ',' . implode(',', $this->mapFieldsIn(array_keys($this->optionalFields)));

		// New content model
		$model = new WebServiceModelBase;

		// Get content state
		$modelState = $model->getState();

		// Set content type that we need
		$modelState->set('content.type', $this->type);

		// Set field list
		$modelState->set('content.fields', $fields);

		// Set each mandatory field
		foreach ($this->mandatoryFields as $fieldName => $fieldContent)
		{
			$modelState->set('fields.' . $this->mapIn($fieldName), $fieldContent);
		}

		// Set each optional field
		foreach ($this->optionalFields as $fieldName => $fieldContent)
		{
			$modelState->set('fields.' . $this->mapIn($fieldName), $fieldContent);
		}

		// Create item
		$item = $model->createItem();

		return $item;
	}

	/**
	 * Parse the returned data from database
	 *
	 * @param   mixed  $data  Fields may be JContent, array of JContent or false
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function parseData($data)
	{
		$this->app->setBody(json_encode($this->pruneFields($data, array('content_id'))));
	}

}
