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
class WebServiceControllerV1JsonScreenshotsUpdate extends WebServiceControllerV1JsonBaseUpdate
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
		$this->app->errors->addError('601', array("/screenshots"));
		$this->app->setBody(json_encode($this->app->errors->getErrors()));
		$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
		return;
	}
}
