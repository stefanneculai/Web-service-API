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
		$app_id = $this->input->get->getString('application_id');
		if (isset($app_id))
		{
			if ($this->itemExists($app_id, 'application'))
			{
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
