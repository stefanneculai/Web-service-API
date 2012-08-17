<?php
/**
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * The class for Screenshot DELETE requests
 *
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @since       1.0
 */
class WebServiceControllerV1JsonScreenshotsDelete extends WebServiceControllerV1JsonBaseDelete
{
	/**
	 * Get mandatory fields from input
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function getMandatoryFields()
	{
		// Search for mandatory fields in input query
		foreach ($this->mandatoryFields as $key => $value )
		{
			// Check if mandatory field is set
			$field = $this->input->get->getString($key);
			if ( isset($field) )
			{
				$this->mandatoryFields[$key] = $field;
			}
			else
			{
				$this->app->errors->addError("308", array($key));
			}
		}
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
		// Get ids from input
		$list = $this->app->input->get->getString('ids');

		// Check if ids were passed
		if (isset($list))
		{
			// Convert the ids list to array
			$idsList = explode(',', $list);

			return $idsList;
		}

		return null;
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
		$this->readFields();

		// Content id
		$this->id = $this->getContentId();

		$this->getMandatoryFields();
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

		// Check for errors
		if ($this->app->errors->errorsExist() == true)
		{
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		// Check if application exists in database
		if ($this->itemExists($this->mandatoryFields['application_id'], 'application'))
		{
			// Get content state
			$modelState = $this->model->getState();

			// Set content type that we need
			$modelState->set('content.type', 'application');
			$modelState->set('content.id', $this->mandatoryFields['application_id']);

			// Get application
			$application = $this->model->getItem();

			// Check if application was loaded successful
			if ($application == false)
			{
				$this->parseData(false);
			}
			else
			{
				// Decode media json to array
				$appMedia = json_decode($application->media);

				// Only one screenshot should be deleted
				if (strcmp($this->id, '*') !== 0)
				{
					// Check if screenshot exists
					if (property_exists($appMedia, $this->id))
					{
						// Remove screenshot
						unset($appMedia->{$this->id});
					}
				}

				// All screenshots should be deleted or a list of screenshots
				else
				{
					// Check for ids list
					$ids = $this->getIDsList();

					// If there is a list of ids
					if ($ids != null)
					{
						// Remove each screenshot if it is passed in the array list
						foreach ($ids as $key => $id)
						{
							if (property_exists($appMedia, $id))
							{
								unset($appMedia->{$id});
							}
						}
					}

					// Remove all screenshots
					else
					{
						$appMedia = null;
					}
				}

				// Update screenshots
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
