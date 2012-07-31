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
class WebServiceControllerV1JsonBaseDelete extends WebServiceControllerV1Base
{
	/**
	 * @var    string  The content id. It may be numeric id or '*' if all content is needed
	 * @since  1.0
	 */
	protected $id = '*';

	/**
	 * @var    string  The minimum created date of the results
	 * @since  1.0
	 */
	protected $since = '1970-01-01';

	/**
	 * @var    string  The user id associated with the content
	 * @since  1.0
	 */
	protected $user_id = null;

	/**
	 * @var    string  The maximum created date of the results
	 * @since  1.0
	 */
	protected $before = 'now';

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

		// Contet is not refered by a number id
		if ( count($routeParts) > 0 && (!is_numeric($routeParts[0]) || $routeParts[0] < 0) && !empty($routeParts[0]))
		{
			$this->app->errors->addError("301");
			return;
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
			$date = new JDate($since);
			if (!empty($since) && checkdate($date->__get('month'), $date->__get('day'), $date->__get('year')))
			{
				return $date->toSql();
			}

			$this->app->errors->addError("304");
			return;
		}
		else
		{
			$date = new JDate($this->since);
			return $date->toSql();
		}
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
	 * Get the user ID associated with the content
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function getUserId()
	{
		$user_id = $this->input->get->getString('user_id');
		if (isset($user_id))
		{
			if ($this->checkUserId() == true)
			{
				return $user_id;
			}
			else
			{
				$this->app->errors->addError("201", array($this->input->get->getString('user_id')));
			}
		}

		return $this->user_id;
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
			$date = new JDate($before);
			if (!empty($before) && checkdate($date->__get('month'), $date->__get('day'), $date->__get('year')))
			{
				return $date->toSql();
			}

			$this->app->errors->addError("305");
			return;
		}
		else
		{
			$date = new JDate($this->before);
			return $date->toSql();
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
		$this->readFields();

		// Content id
		$this->id = $this->getContentId();

		// Since
		$this->since = $this->getSince();

		// Before
		$this->before = $this->getBefore();

		// Get the user id
		$this->user_id = $this->getUserId();
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

		// Check for errors
		if ($this->app->errors->errorsExist() == true)
		{
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		// Delete content from Database
		$data = $this->deleteContent();

		// Parse the returned code
		$this->parseData($data);
	}

	/**
	 * Delete item or all data using before and since
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function deleteContent()
	{
		// Get content state
		$modelState = $this->model->getState();

		// Set content type that we need
		$modelState->set('content.type', $this->type);
		$modelState->set('content.id', $this->id);

		$modelState->set('filter.since', $this->since);
		$modelState->set('filter.before', $this->before);

		if (strcmp($this->id, '*') !== 0)
		{
			// Get the result
			try
			{
				$result = $this->model->deleteItem();
			}
			catch (Exception $e)
			{
				throw $e;
			}

			return $result;
		}
		else
		{
			if ($this->user_id != null)
			{
				$modelState->set('content.user_id', $this->user_id);
			}

			try
			{
				$result = $this->model->deleteList();
			}
			catch (Exception $e)
			{
				throw $e;
			}

			return $result;
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
