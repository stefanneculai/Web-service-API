<?php
/**
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * The class for Tag UPDATE requests
 *
 * @package     WebService.Controller
 * @subpackage  Controller
 *
 * @since       1.0
 */
class WebServiceControllerV1JsonTagsUpdate extends WebServiceControllerV1JsonBaseUpdate
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
		// This request is not available
		$this->app->errors->addError('601', array("/updates"));
		$this->app->setBody(json_encode($this->app->errors->getErrors()));
		$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
		return;
	}
}
