<?php
/**
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * The class for Comment CREATE requests
 *
 * @package     WebService.Controller
 * @subpackage  Controller
 *
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

		// Check if mandatory field application_id was passed (each comment is associated with an application)
		if ($this->itemExists($this->mandatoryFields['application_id'], 'application'))
		{
			// Create content
			$data = $this->createContent();

			// Get comment id
			$comment_id = $this->pruneFields($data, array('content_id'));

			// Check if we have comment id in result
			if (array_key_exists('id', $comment_id))
			{
				$comment_id = $comment_id['id'];
			}

			// Get content state
			$modelState = $this->model->getState();

			// Set content type that we need
			$modelState->set('content.type', $this->type);

			// Map the comment with the application
			$result = $this->model->map($this->mandatoryFields['application_id'], array($comment_id));

			// If mapping is done successfully
			if ($result == true)
			{
				$returnedContent = new stdClass;
				$returnedContent->id = $comment_id;
				$this->app->setBody(json_encode($returnedContent));
			}

			// If something went wrong during mapping return false
			else
			{
				$this->parseData(false);
			}
			return;
		}

		// Throw error
		else
		{
			$this->app->errors->addError('202');
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}
	}
}
