<?php
/**
 * @package     WebService.Application
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * WebService 'content' Create method.
 *
 * @package     WebService.Application
 * @subpackage  Controller
 * @since       1.0
 */
class WebServiceControllerV1JsonUsersCreate extends WebServiceControllerV1JsonBaseCreate
{
	/**
	 * Load user
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	protected function loadUser()
	{
		$session = $this->app->getSession();
		$session->set('user', new JUser);
	}
}
