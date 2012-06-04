<?php

/**
 * @package     WebService.Application
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Web Service Api 'content' Model class.
 *
 * @package     WebService.Application
 * @subpackage  Application
 * @since       1.0
 */
class WebServiceModelContent extends JModelBase
{
	/**
	 * Get data from DB
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function getData()
	{
		$factory = JContentFactory::getInstance();

		$content = $factory->getContent($this->state->get('type'))->load('1');

		return $content;
	}
}
