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
		if (isset($action))
		{
			return $action;
		}

		return $this->action;
	}

	/** Get the user_id from input and check if it exists
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	protected function checkUserId()
	{
		$user_id = $this->input->get->getString('user_id');
		if (isset($user_id))
		{
			$user = new JUser;
			return $user->load($user_id);
		}

		return false;
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
		// Check if the passed user id is correct
		if ($this->checkUserId() == true)
		{
			// Load user in session
			$user_id = $this->input->get->getString('user_id');
			$session = $this->app->getSession();
			$session->set('user', new JUser($user_id));
		}
		else
		{
			// Bad user id
			$this->app->errors->addError("201", array($this->input->get->getString('user_id')));
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

		// Get action
		$this->action = $this->getAction();

		// User
		if ($this->action != null && (strcmp($this->action, 'like') == 0 || strcmp($this->action, 'unlike') == 0))
		{
			$this->loadUser();
		}

		// Get media and save it
		if (isset($_FILES['media']))
		{
			$this->dataFields['media'] = $this->getMedia();
		}
	}

	/**
	 * Save media fields to the upload folder
	 *
	 * @return  string  A string with the names of the uploaded files
	 *
	 * @since   1.0
	 */
	protected function saveMedia()
	{
		$media = $_FILES['media'];

		$files = array();

		foreach ($media['name'] as $key => $value)
		{
			$ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $value);
			$newName = uniqid("", true) . '.' . $ext;

			// If a file with the same name exists create a new name
			while (file_exists(JPATH_BASE . "/../www/uploads/" . $newName))
			{
				$newName = uniqid("", true) . '.' . $ext;
			}

			array_push($files, UPLOADS . $newName);

			move_uploaded_file(
					$media['tmp_name'][$key],
					JPATH_BASE . "/../www/uploads/" . $newName
					);
		}

		return implode('|', $files);
	}

	/**
	 * Get media fields from input
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	protected function getMedia()
	{
		if (isset($_FILES['media']))
		{
			try
			{
				return $this->saveMedia();
			}
			catch (Exception $e)
			{
				$this->app->errors->addError("301", $e->getMessage());
				return;
			}
		}

		return null;
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

		if (count($this->dataFields) == 0 && $this->action == null)
		{
			$this->app->errors->addError("101");
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
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
			if (strcmp($this->action, 'hit') == 0)
			{
				$item = $this->model->hitItem();
			}
			elseif (strcmp($this->action, 'like') == 0)
			{
				$item = $this->model->likeItem();
			}
			elseif (strcmp($this->action, 'unlike') == 0)
			{
				$item = $this->model->likeItem(true);
			}
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
		// Check if the update was successful
		$this->app->setBody(json_encode($data));
	}

}
