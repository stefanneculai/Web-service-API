<?php
/**
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * The class for UPDATE requests
 *
 * @package     WebService.Controller
 * @subpackage  Controller
 *
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
	 * @var    string  The user that liked or unliked
	 * @since  1.0
	 */
	protected $user = null;

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

		// Check if action is set
		if (isset($action))
		{
			// Available actions are like, unlike and hit
			if (in_array($action, $this->availableActions) && strcmp($action, 'count') != 0)
			{
				return $action;
			}
			else
			{
				$this->app->errors->addError("502", array($action));
			}
		}

		// NULL is returned
		return $this->action;
	}

	/**
	 * Load user
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	protected function loadUser()
	{
		$user_id = $this->input->get->getString('user_id');

		if (!isset($user_id))
		{
			$this->app->errors->addError("308", array('user_id'));
		}
		else
		{
			// Check if the passed user id is correct
			if ($this->checkUserId() == true)
			{
				// Load user in session
				$session = $this->app->getSession();
				$session->set('user', new JUser($user_id));
			}
			else
			{
				// Bad user id
				$this->app->errors->addError("201", array($this->input->get->getString('user_id')));
			}
		}
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
		$id = $this->input->get->getInteger('content_id');

		// Content is not refered by a number id
		if (isset($id))
		{
			if ($id <= 0)
			{
				$this->app->errors->addError("301");
				return;
			}
			else
			{
				return $id;
			}
		}
		else
		{
			$this->app->errors->addError("309");
			return;
		}
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

		// Get action
		$this->action = $this->getAction();

		// If we have an action we need a user to do that
		if ($this->action != null && (strcmp($this->action, 'like') == 0 || strcmp($this->action, 'unlike') == 0))
		{
			$this->loadUser();
		}
		elseif (!empty($this->dataFields['user_id']) && $this->checkUserId() == false)
		{
			$this->app->errors->addError("201", array($this->input->get->getString('user_id')));
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

		// Check for errors
		if ($this->app->errors->errorsExist() == true)
		{
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		// Returned data
		$data = $this->updateContent();

		// Check for errors
		if ($this->app->errors->errorsExist() == true)
		{
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		// Parse the returned data
		$this->parseData($data);
	}

	/**
	 * Update content
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 * @throws  Exception - if something went wrong during the update
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

		if (count($this->dataFields) == 0 && $this->action == null)
		{
			return true;
		}

		$fields = implode(',', $this->mapFieldsIn(array_keys($this->dataFields)));

		// Get content state
		$modelState = $this->model->getState();

		// Set content id
		$modelState->set('content.id', $this->id);

		// Set content type that we need
		$modelState->set('content.type', $this->type);

		// Set field list
		$modelState->set('content.fields', $fields);

		// Set each field
		foreach ($this->dataFields as $fieldName => $fieldContent)
		{
			$modelState->set('fields.' . $this->mapIn($fieldName), $fieldContent);
		}

		try
		{
			// Check if item exists
			if ($this->model->existsItem($this->id) == false)
			{
				$this->app->errors->addError("204", array($this->type . '_id', $this->id));
				return;
			}

			// Hit item
			if (strcmp($this->action, 'hit') == 0)
			{
				$item = $this->model->hitItem();
			}

			// Like item
			elseif (strcmp($this->action, 'like') == 0)
			{
				$item = $this->model->likeItem();
			}

			// Unlike item
			elseif (strcmp($this->action, 'unlike') == 0)
			{
				$item = $this->model->likeItem(true);
			}

			// Update item fields
			else
			{
				$item = $this->model->updateItem();
			}

			return $item;
		}
		catch (Exception $e)
		{
			throw $e;
		}
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
		$this->app->setBody(json_encode($data));
	}

}
