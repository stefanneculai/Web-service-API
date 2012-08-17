<?php
/**
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * The class for Tag CREATE requests
 *
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @since       1.0
 */
class WebServiceControllerV1JsonTagsCreate extends WebServiceControllerV1JsonBaseCreate
{
	/**
	 * Get the list of tags from input
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	protected function getTagList()
	{
		// Check if the mandatory field list is set
		if (isset($this->mandatoryFields['list']))
		{
			// Convert the list of tags to array
			$tagList = explode(',', $this->mandatoryFields['list']);

			foreach ($tagList as $key => $tag)
			{
				$tagList[$key] = trim($tag);
			}

			return $tagList;
		}

		return null;
	}

	/**
	 * Get the list with tag IDs from input
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	protected function getIDsList()
	{
		// Check if ids were passed
		if (isset($this->mandatoryFields['ids']))
		{
			// Create an array of ids
			$idsList = explode(',', $this->mandatoryFields['ids']);

			foreach ($idsList as $key => $tag)
			{
				$idsList[$key] = trim($tag);
			}

			return $idsList;
		}

		return null;
	}

	/**
	 * Get a list of tag IDs
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	protected function getTagIds()
	{
		// Get the list of tags from the input
		$list = $this->getTagList();
		$ids = $this->getIDsList();
		$tagIDs = array();

		// If there is a list of tags
		if ($list != null)
		{
			// Check if tags exist in database or they should be created
			foreach ($list as $key => $tag)
			{
				// Set where clause
				$this->setWhere(array($this->mapIn('name') => $tag));

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
					$this->mandatoryFields['name'] = $tag;
					$data = $this->createContent();
				}

				// Get the tag id
				$tag_id = $this->pruneFields($data, array('content_id'));

				if (array_key_exists('id', $tag_id))
				{
					$tag_id = $tag_id['id'];
				}

				// Put the new tag in the array list
				array_push($tagIDs, $tag_id);
			}
		}

		// If there is a list of tag ids
		elseif ($ids != null)
		{
			$modelState = $this->model->getState();
			$modelState->set('content.type', $this->type);

			foreach ($ids as $key => $id)
			{
				$tagIDs = array();

				if ($this->model->existsItem($id))
				{
					array_push($tagIDs, $id);
				}
				else
				{
					$this->app->errors->addError('501', $id);
				}
			}
		}

		// There is a tag
		else
		{
			// Create content
			$data = $this->createContent();

			// Get tag id
			$tag_id = $this->pruneFields($data, array('content_id'));

			if (array_key_exists('id', $tag_id))
			{
				$tag_id = $tag_id['id'];
			}

			// Put the new tag in the array list
			array_push($tagIDs, $tag_id);
		}

		return $tagIDs;
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

		// An array with tag ids
		$tagIDs = $this->getTagIds();

		// Check for errors
		if ($this->app->errors->errorsExist() == true)
		{
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		// Check if there is an application id specified
		if (array_key_exists('application_id', $this->optionalFields))
		{
			// Check if application exists in database
			if ($this->itemExists($this->optionalFields['application_id'], 'application'))
			{
				// Get content state
				$modelState = $this->model->getState();

				// Set content type that we need
				$modelState->set('content.type', $this->type);

				// Map only one tag ID
				if (isset($this->mandatoryFields['name']))
				{
					$result = $this->model->map($this->optionalFields['application_id'], $tagIDs);
				}

				// Map an array of tag IDs
				else
				{
					$result = $this->model->map($this->optionalFields['application_id'], $tagIDs, true);
				}

				$this->app->setBody(json_encode($result));

				return;
			}
			// Raise error
			else
			{
				$this->app->errors->addError('204', array('application_id', $this->optionalFields['application_id']));
				$this->app->setBody(json_encode($this->app->errors->getErrors()));
				$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
				return;
			}
		}

		$this->parseData($tagIDs);
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
		if (is_array($data))
		{
			foreach ($data as $key => $value)
			{
				$data[$key] = new stdClass;
				$data[$key]->id = $value;
			}
		}

		$this->app->setBody(json_encode($data));
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
