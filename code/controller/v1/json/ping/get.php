<?php
/**
 * @package     Vangelis.Application
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) {COPYRIGHT}. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Vangelis 'ping' method.
 *
 * @package     Vangelis.Application
 * @subpackage  Controller
 * @since       1.0
 */
class VangelisControllerV1JsonPingGet extends JControllerBase
{
	public function execute()
	{
		$this->app->setBody(json_encode('Pong'));
	}

}