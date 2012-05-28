<?php

/**
 * @package     WebService.Application
 * @subpackage  Application
 *
 * @copyright   Copyright (C) {COPYRIGHT}. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Web Service Api 'content' Model class.
 *
 * @package     WebService.Application
 * @subpackage  Application
 * @since    1.0
 */
class WebServiceModelContent extends JModelBase
{
	public function getData(){
		
		$factory = JContentFactory::getInstance();
	
		$content = $factory->getContent($this->state->get('type'))->load('1');
		
		return $content;
	}
}

?>