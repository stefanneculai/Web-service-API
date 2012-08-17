<?php
/**
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * The class for likes GET requests
 *
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @since       1.0
 */
class WebServiceControllerV1JsonLikesGet extends WebServiceControllerV1JsonBaseGet
{

	/**
	 * The content id for which to get likes
	 */
	protected $content_id;

	/**
	 * Get the content id
	 *
	 * @return  string  $content_id
	 *
	 * @since   1.0
	 */
	protected function getContentId()
	{
		// TODO update this class to get likes for all kind of contents

		// Get application ID from input
		$application_id = $route = $this->input->get->getString('application_id');

		// Check if application ID was passed
		if (isset($application_id))
		{
			// Update the type from likes to application, because likes are actually associated to application
			$this->type = "application";
			return $application_id;
		}

		return null;
	}

	/**
	 * Init
	 *
	 * @see     WebServiceControllerV1Base::init()
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function init()
	{
		// Read fields
		$this->readFields();

		// The content_id
		$this->content_id = $this->getContentId();

		// Returned fields
		$this->fields = $this->getFields();

		// Get limit
		$this->limit = $this->getLimit();

		// Get offset
		$this->offset = $this->getOffset();

		// Get user id
		$this->user_id = $this->getUserId();

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
		$this->init();

		// Check if we have a content ID specified
		if (is_null($this->content_id) && is_null($this->user_id))
		{
			$this->app->errors->addError("203");
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		// All likes for the specified user should be returned
		if (!is_null($this->user_id))
		{
			// Check if user exists in database
			if ($this->checkUserId() == false)
			{
				$this->app->errors->addError("204", array('user_id', $this->user_id));
				$this->app->setBody(json_encode($this->app->errors->getErrors()));
				$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
				return;
			}

			// Get model state
			$modelState = $this->model->getState();

			// Set the user id in session. This is used for updating the content in Application class
			$session = $this->app->getSession();
			$session->set('userID', $this->user_id);

			// Set limit and offset for the returned likes
			$modelState->set('list.offset', $this->offset);
			$modelState->set('list.limit', $this->limit);

			// Get data
			$data = $this->model->getList();

			// Format likes properly
			$likes = array();

			foreach ($data as $key => $content)
			{
				foreach ($content->likesArray->data as $lkey => $like)
				{
					$like->object = ucfirst($content->typeAlias);
					$like->object_id = $content->content_id;
					unset($like->user_id);
					array_push($likes, $like);
				}
			}

			$output = new stdClass;
			$output->data = $likes;
			$output->count = count($likes);

			$this->app->setBody(json_encode($output));
		}
		else
		{
			// Get content state
			$modelState = $this->model->getState();

			// Set content that we need
			$modelState->set('content.type', $this->type);
			$modelState->set('content.id', $this->content_id);

			// Get data
			$data = $this->model->getItem();

			// Format the results properly
			$this->parseData($data);
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
		if ($data == false)
		{
			$this->app->errors->addError("204", array($this->type . '_id', $this->content_id));
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		// Return only the likes from application
		else
		{
			$data = $this->pruneFields($data, $this->fields);
			$data = $data['likes'];
		}

		$this->app->setBody(json_encode($data));
	}
}
