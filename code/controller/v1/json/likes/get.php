<?php
/**
 * @package     WebService.Application
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * WebService GET likes class
 *
 * @package     WebService.Application
 * @subpackage  Controller
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
		$application_id = $route = $this->input->get->getString('application_id');
		if (isset($application_id))
		{
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

		if (is_null($this->content_id) && is_null($this->user_id))
		{
			$this->app->errors->addError("203");
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		if (!is_null($this->user_id))
		{
			$modelState = $this->model->getState();

			$session = $this->app->getSession();
			$session->set('user_likes', $this->user_id);

			$modelState->set('list.offset', $this->offset);
			$modelState->set('list.limit', $this->limit);

			$data = $this->model->getList();

			$likes = array();

			foreach ($data as $key => $content)
			{
				foreach ($content->likesArray->data as $lkey => $like)
				{
					$like->object = ucfirst($content->typeAlias);
					$like->object_id = $content->id;
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
		else
		{
			$data = $this->pruneFields($data, $this->fields);
			$data = $data['likes'];
		}

		$this->app->setBody(json_encode($data));
	}
}
