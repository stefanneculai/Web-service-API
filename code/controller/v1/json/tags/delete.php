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
class WebServiceControllerV1JsonTagsDelete extends WebServiceControllerV1JsonBaseDelete
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

		$app_id = $this->input->get->getString('application_id');
		if (isset($app_id))
		{
			// Check if application exists in database
			if ($this->applicationExists($app_id))
			{
				$modelState = $this->model->getState();
				$modelState->set('content.type', $this->type);

				if (strcmp($this->id, '*') !== 0)
				{
					$result = $this->model->unmap($app_id, $this->id);
					$this->app->setBody(json_encode($result));
				}
				else
				{
					$result = $this->model->unmap($app_id);
					$this->app->setBody(json_encode($result));
				}
			}
			// Raise error
			else
			{
				$this->app->errors->addError('202');
				$this->app->setBody(json_encode($this->app->errors->getErrors()));
				$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
				return;
			}
		}
		else
		{
			// Delete content from Database
			$data = $this->deleteContent();

			// Parse the returned code
			$this->parseData($data);
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
