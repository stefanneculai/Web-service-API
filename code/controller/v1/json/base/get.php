<?php
/**
 * @package     WebService.Application
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * WebService GET content class
 *
 * @package     WebService.Application
 * @subpackage  Controller
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
	 * @var    string  The order of the results
	 * @since  1.0
	 */
	protected $order = null;

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
			return $this->id;
		}

		// Specific content id
		return $routeParts[0];
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
		$offset = $this->input->get->getString('offset');

		if (isset($offset))
		{
			if ( is_numeric($offset) && $offset >= 0)
			{
				return $offset;
			}

			$this->app->errors->addError("302");
			return;
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
		$limit = $this->input->get->getString('limit');

		if (isset($limit))
		{
			$limit = min($this->maxResults, $limit);
			if (is_numeric($limit) && $limit > 0)
			{
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

		if (isset($fields))
		{
			$fields = preg_split('#[\s,]+#', $fields, null, PREG_SPLIT_NO_EMPTY);

			foreach ($fields as $key => $field)
			{
				if (!array_key_exists($field, $this->fieldsMap))
				{
					unset($fields[$key]);
				}
			}

			if (count($fields) > 0)
			{
				return $fields;
			}

			return array_keys($this->fieldsMap);
		}
		else
		{
			return array_keys($this->fieldsMap);
		}
	}

	/**
	 * Get the order from input or the default one
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 */
	protected function getOrder()
	{

		$order = $this->input->get->getString('order');

		if (isset($order))
		{
			$order = preg_split('#[\s,]+#', $order, null, PREG_SPLIT_NO_EMPTY);

			foreach ($order as $key => $field)
			{
				if (!array_key_exists($field, $this->fieldsMap))
				{
					$this->app->errors->addError("307");
					return;
				}
			}

			if ($order == false)
			{
				return null;
			}

			return $order;
		}
		else
		{
			return null;
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
			return $user_id;
		}

		return $this->user_id;
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

		// Results order
		$this->order = $this->getOrder();

		if ($this->order != null)
		{
			$this->order = $this->mapFieldsIn($this->order);
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

		if ($this->app->errors->errorsExist() == true)
		{
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		$this->doMap();

		// Returned data
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
	 * @throws  Exception
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

		if ($this->user_id != null)
		{
			$modelState->set('content.user_id', $this->user_id);
		}

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

			// No item found
			if ($item == false)
			{
				return false;
			}

			return $item;
		}
		// All content is requested
		else
		{
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

			// No items found
			if ($items == false)
			{
				return false;
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
		// Get only requested fields
		if ( is_integer($data) == false )
		{
			// There is no content for the request
			if ($data == false)
			{
				$data = null;
			}

			// Sort data
			if (count($this->order) > 0)
			{
				usort($data, array($this, "orderData"));
			}

			$data = $this->pruneFields($data, $this->fields);

			$dataValues = array_values($data);

			if (is_array($data) && is_array($dataValues[0]))
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
