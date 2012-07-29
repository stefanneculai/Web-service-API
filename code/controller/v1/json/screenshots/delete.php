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
		$list = $this->app->input->get->getString('ids');
		if (isset($list))
		{
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

		if ($this->app->errors->errorsExist() == true)
		{
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		if ($this->applicationExists($this->mandatoryFields['application_id']))
		{
			// Get content state
			$modelState = $this->model->getState();

			// Set content type that we need
			$modelState->set('content.type', 'application');
			$modelState->set('content.id', $this->mandatoryFields['application_id']);

			$application = $this->model->getItem();
			if ($application == false)
			{
				$this->app->setBody(json_encode(false));
			}
			else
			{
				$appMedia = json_decode($application->media);

				if (strcmp($this->id, '*') !== 0)
				{
					if (property_exists($appMedia, $this->id))
					{
						unset($appMedia->{$this->id});
					}
				}
				else
				{
					$ids = $this->getIDsList();
					if ($ids != null)
					{
						foreach ($ids as $key => $id)
						{
							if (property_exists($appMedia, $id))
							{
								unset($appMedia->{$id});
							}
						}
					}
				}

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

				$this->parseData($application);
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
