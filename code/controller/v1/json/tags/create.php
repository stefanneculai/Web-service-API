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
class WebServiceControllerV1JsonTagsCreate extends WebServiceControllerV1JsonBaseCreate
{
	/**
	 * Controller logic
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function execute()
	{
		//TODO set a root user
		$this->app->input->get->set('user_id', '32');

		// Init
		$this->init();

		// Check for errors
		if ($this->app->errors->errorsExist() == true)
		{
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		// Set where clause
		$this->setWhere(array($this->mapIn('name') => $this->mandatoryFields['name']));

		// Check if the tag already exist
		if ($this->model->countItems() > 0)
		{
			// Get the tag
			$data = $this->getContent();
			$data = array_values($data);
			$data = $data[0];
		}
		// Create the tag if it does not exist
		else
		{
			$data = $this->createContent();
		}

		// Check if there is an application id specified
		if (array_key_exists('application_id', $this->optionalFields))
		{
			$tag_id = $this->pruneFields($data, array('content_id'));

			// If content was just created
			if (array_key_exists('id', $tag_id))
			{
				$tag_id = $tag_id['id'];
			}

			// Check if application exists in database
			if ($this->applicationExists($this->optionalFields['application_id']))
			{
				// Get content state
				$modelState = $this->model->getState();

				// Set content type that we need
				$modelState->set('content.type', $this->type);

				$result = $this->model->map($this->optionalFields['application_id'], $tag_id);

				$this->app->setBody(json_encode($result));

				return;
			}
			// Raise error
			else
			{
				$this->app->errors->addError('202');
				$this->app->setBody(json_encode($this->app->errors->getErrors()));
				$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
				return;
			}
		}

		$this->parseData($data);
	}

	/**
	 * Check if an application exists in DB
	 *
	 * @param   string  $id  The id of the application
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	protected function applicationExists($id)
	{
		$modelState = $this->model->getState();
		$modelState->set('content.type', 'application');

		return $this->model->existsItem($id);
	}

	/**
	 * Get content by id or all content
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 * @throws  Exception
	 */
	protected function getContent()
	{
		// Get content state
		$modelState = $this->model->getState();

		// Set content type that we need
		$modelState->set('content.type', $this->type);

		$modelState->set('list.offset', '0');
		$modelState->set('list.limit', '1');

		// Get content from Database
		try
		{
			$items = $this->model->getList();
		}
		catch (Exception $e)
		{
			throw $e;
		}

		return $items;
	}
}
