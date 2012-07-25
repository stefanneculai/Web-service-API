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
class WebServiceControllerV1JsonLikesGet extends WebServiceControllerV1Base
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
		$this->content_id = $this->getContentId();
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

		if (is_null($this->content_id))
		{
			$this->app->errors->addError("203");
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		// Get content state
		$modelState = $this->model->getState();

		// Set content that we need
		$modelState->set('content.id', $this->content_id);

		$data = $this->model->getLikes();

		// Format the results properly
		$this->parseData($data);
	}

	/**
	 * Parse the returned data from database
	 *
	 * @param   array  $data  The array with results
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
