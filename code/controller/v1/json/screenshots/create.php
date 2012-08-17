<?php
/**
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * The class for Screenshot CREATE requests
 *
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @since       1.0
 */
class WebServiceControllerV1JsonScreenshotsCreate extends WebServiceControllerV1JsonBaseCreate
{
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

		// Init mandatory fields
		$this->getMandatoryFields();

		// Init optional fields
		$this->getOptionalFields();

		// Get media and save it
		if (isset($_FILES['screenshots']))
		{
			$this->optionalFields['media'] = $this->getMedia();
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
		// Init request
		$this->init();

		if ($this->app->errors->errorsExist() == true)
		{
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		if ($this->itemExists($this->mandatoryFields['application_id'], 'application'))
		{
			// Get content state
			$modelState = $this->model->getState();

			// Set content type that we need
			$modelState->set('content.type', 'application');
			$modelState->set('content.id', $this->mandatoryFields['application_id']);

			// Get the application
			$application = $this->model->getItem();

			// Check if application exists
			if ($application == false)
			{
				$this->parseData(false);
			}
			else
			{
				// Check if media fields were passed
				if (isset($this->optionalFields['media']))
				{
					// Get the media from application
					$appMedia = json_decode($application->media);

					// Add new media to application
					foreach (json_decode($this->optionalFields['media']) as $key => $media)
					{
						if ($appMedia != null)
						{
							$maxId = (int) max(array_keys(get_object_vars($appMedia))) + 1;
						}
						else
						{
							$maxId = 1;
						}
						$appMedia->{$maxId} = $media;
					}

					// Encode media to json and save it to database
					$application->media = json_encode($appMedia);
					try
					{
						$application->update();
						$this->parseData(true);
						return;
					}
					catch (Exception $e)
					{
						$this->parseData(false);
						return;
					}
				}
				else
				{
					$this->app->errors->addError('308', array("screenshots"));
					$this->app->setBody(json_encode($this->app->errors->getErrors()));
					$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
					return;
				}
			}
		}
		else
		{
			$this->app->errors->addError('202');
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
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
		$this->app->setBody(json_encode($data));
	}
}
