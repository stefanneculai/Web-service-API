<?php
/**
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * The class for Tag GET requests
 *
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @since       1.0
 */
class WebServiceControllerV1JsonTagsGet extends WebServiceControllerV1JsonBaseGet
{
	/**
	 * Do the mapping with of tags with applications
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function doMap()
	{
		// Get application ID from input
		$app_id = $this->input->get->getString('application_id');

		// Check if application ID was passed
		if (isset($app_id))
		{
			// Check if application exists in database
			if ($this->itemExists($app_id, 'application'))
			{
				// Get content state
				$modelState = $this->model->getState();

				// Set content type that we need
				$modelState->set('tags.content_id', $app_id);
			}
			else
			{
				$this->app->errors->addError('204', array('application_id', $app_id));
			}
		}
	}
}
