<?php
/**
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * The class for GET requests
 *
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @since       1.0
 */
class WebServiceControllerV1JsonBaseGet extends WebServiceControllerV1Base
{
	/**
	 * @var    string  The limit of the results
	 * @since  1.0
	 */
	protected $limit = 20;

	/**
	 * @var    string  The maximum number of results per page
	 * @since  1.0
	 */
	protected $maxResults = 100;

	/**
	 * @var    string  The offset of the results
	 * @since  1.0
	 */
	protected $offset = 0;

	/**
	 * @var    array  The fields of the results
	 * @since  1.0
	 */
	protected $fields;

	/**
	 * @var    string  The content id. It may be numeric id or '*' if all content is refeered
	 * @since  1.0
	 */
	protected $id = '*';

	/**
	 * @var    string  The user id associated with the content
	 * @since  1.0
	 */
	protected $user_id = null;

	/**
	 * @var    string  The user id associated with the content
	 * @since  1.0
	 */
	protected $application_id = null;

	/**
	 * @var    string  The minimum created date of the results
	 * @since  1.0
	 */
	protected $since = '01-01-1970';

	/**
	 * @var    string  The maximum created date of the results
	 * @since  1.0
	 */
	protected $before = 'now';

	/**
	 * @var    string  Action
	 * @since  1.0
	 */
	protected $action = null;

	/**
	 * Get the content ID from the input. It may also return '*' refeering all the content
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function getContentId()
	{
		// Get content id from input and convert it to integer
		$id = $this->input->get->getInteger('content_id');

		// Check if content_id is set
		if (isset($id))
		{
			// Check if ID is positive
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
			return $this->id;
		}
	}

	/**
	 * Get the offset from input or the default one
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function getOffset()
	{
		$offset = $this->input->get->getInteger('offset');

		// Check if offset is set in input
		if (isset($offset))
		{
			// Check if offset is positive
			if ($offset >= 0)
			{
				return $offset;
			}
			else
			{
				$this->app->errors->addError("302");
				return;
			}
		}
		else
		{
			return $this->offset;
		}
	}

	/**
	 * Get the limit from input or the default one
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function getLimit()
	{
		$limit = $this->input->get->getInteger('limit');

		// Check if limit is passed in input
		if (isset($limit))
		{
			// Check if limit is positive
			if ($limit > 0)
			{
				$limit = min($this->maxResults, $limit);
				return $limit;
			}

			$this->app->errors->addError("303");
			return;
		}
		else
		{
			return $this->limit;
		}
	}

	/**
	 * Get the fields from input or the default one
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 */
	protected function getFields()
	{
		$fields = $this->input->get->getString('fields');

		// CHeck if a list of fields in passed in input
		if (isset($fields))
		{
			// Split the list of fields in words
			$fields = preg_split('#[\s,]+#', $fields, null, PREG_SPLIT_NO_EMPTY);

			// Check if the fields exist in the fields' map. Otherwise, remove it
			foreach ($fields as $key => $field)
			{
				if (!array_key_exists($field, $this->fieldsMap))
				{
					unset($fields[$key]);
				}
			}

			// If filter fields are passed return them
			if (count($fields) > 0)
			{
				return $fields;
			}

			// All available fields are returned in an array
			return array_keys($this->fieldsMap);
		}
		else
		{
			// All available fields are returned in an array
			return array_keys($this->fieldsMap);
		}
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

		// Check if since is set
		if (isset($since))
		{
			// Convert since to date
			$date = new JDate($since);

			// Test if date is valid
			if (!empty($since) && checkdate($date->__get('month'), $date->__get('day'), $date->__get('year')))
			{
				return $date->toSql();
			}

			$this->app->errors->addError("304");
			return;
		}
		else
		{
			// Return the default date
			$date = new JDate($this->since);
			return $date->toSql();
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

		// Check if before is set
		if (isset($before))
		{
			// Convert before to date
			$date = new JDate($before);

			// Test if date is valid
			if (!empty($before) && checkdate($date->__get('month'), $date->__get('day'), $date->__get('year')))
			{
				return $date->toSql();
			}

			$this->app->errors->addError("305");
			return;
		}
		else
		{
			// Return the default date
			$date = new JDate($this->before);
			return $date->toSql();
		}
	}

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
			// The only available action for GET method is count
			if (strcmp($action, 'count') == 0)
			{
				return $action;
			}
			else
			{
				$this->app->errors->addError("502", array($action));
			}
		}

		return $this->action;
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
		// Read fields from config file
		$this->readFields();

		// Content id
		$this->id = $this->getContentId();

		// Results offset
		$this->offset = $this->getOffset();

		// Results limits
		$this->limit = $this->getLimit();

		// Returned fields
		$this->fields = $this->getFields();

		// Map fields according to the application database
		if ($this->fields != null)
		{
			$this->fields = $this->mapFieldsIn($this->fields);
		}

		// Since
		$this->since = $this->getSince();

		// Before
		$this->before = $this->getBefore();

		// Action
		$this->action = $this->getAction();

		// The user id for the current content
		$this->user_id = $this->getUserId();
	}

	/**
	 * Do the mapping with tags, comments and categories
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function doMap()
	{
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

		// Map other data
		$this->doMap();

		// Check if there are errors
		if ($this->app->errors->errorsExist() == true)
		{
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		// Get data from model
		$data = $this->getContent();

		// Format the results properly
		$this->parseData($data);
	}

	/**
	 * Get content by id or all content
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 * @throws  Exception if something went wrong during the database request
	 */
	protected function getContent()
	{
		// Get content state
		$modelState = $this->model->getState();

		// Set content type that we need
		$modelState->set('content.type', $this->type);
		$modelState->set('content.id', $this->id);

		// Set date limitations
		$modelState->set('filter.since', $this->since);
		$modelState->set('filter.before', $this->before);

		// Return the number of items
		if (strcmp($this->action, 'count') === 0)
		{
			return $this->model->countItems();
		}

		// A specific content is requested
		if (strcmp($this->id, '*') !== 0)
		{
			// Get the requested data
			try
			{
				$item = $this->model->getItem();
			}
			catch (Exception $e)
			{
				throw $e;
			}

			return $item;
		}

		// All content is requested
		else
		{
			if ($this->user_id != null)
			{
				$modelState->set('content.user_id', $this->user_id);
			}

			// Set offset and results limit
			$modelState->set('list.offset', $this->offset);
			$modelState->set('list.limit', $this->limit);

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
		// There is no content for the request
		if ($data == null)
		{
			if (strcmp($this->id, '*') !== 0)
			{
				$this->app->errors->addError("204", array($this->type . '_id', $this->id));
				$this->app->setBody(json_encode($this->app->errors->getErrors()));
				$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
				return;
			}
			else
			{
				$data = array();
			}
		}

		// Get only requested fields
		if ( is_integer($data) == false )
		{
			$data = $this->pruneFields($data, $this->fields);

			$dataValues = array_values($data);

			if (is_array($data) && count($dataValues) > 0 && is_array($dataValues[0]))
			{
				$data = array_values($data);
			}
		}
		else
		{
			$newData = new stdClass;
			$newData->count = $data;
			$data = $newData;
		}

		$this->app->setBody(json_encode($data));
	}
}
