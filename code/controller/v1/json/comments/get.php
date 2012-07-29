<?php
/**
 * @package     WebService.Application
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * WebService GET media class
 *
 * @package     WebService.Application
 * @subpackage  Controller
 * @since       1.0
 */
class WebServiceControllerV1JsonCommentsGet extends WebServiceControllerV1JsonBaseGet
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
			$modelState = $this->model->getState();

			// Set content type that we need
			$modelState->set('comments.content_id', $app_id);
		}
	}
}
