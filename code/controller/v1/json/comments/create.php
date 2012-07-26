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
class WebServiceControllerV1JsonCommentsCreate extends WebServiceControllerV1JsonBaseCreate
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
		// Init
		$this->init();

		// Check for errors
		if ($this->app->errors->errorsExist() == true)
		{
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		if ( isset($this->mandatoryFields['application_id']) )
		{

		}
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
}
