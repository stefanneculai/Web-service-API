<?php
/**
 * @package     WebService.Application
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) {COPYRIGHT}. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * WebService 'content' method.
 *
 * @package     WebService.Application
 * @subpackage  Controller
 * @since       1.0
 */
class WebServiceControllerV1JsonContentGet extends JControllerBase
{
	/**
	 * Get route parts from the input
	 * 
	 * @return  void
	 * 
	 * @since   1.0
	 */
	protected function getContentId(){
		
		// Get route from the input
		$route = $this->input->get->getString('@route');
		
		// Break route into more parts
		$routeParts = explode('/',$route);
		
		// Path is longer than it should be
		if( count($routeParts) > 1)
			throw new InvalidArgumentException('Unknown content path '.$route, 502);
		
		// Contet is not refered by a number id
		if( !is_numeric($route) && !empty($route))
			throw new InvalidArgumentException('Unknown content path '.$route, 503);
		
		return $route;
		
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
		$id = $this->getContentId();
		
		$this->getContent($id);
	}
		
	/**
	 * Get content by id
	 * 
	 * @param   string  $id       Content id. All entries if null
	 * @param   string  $options  Conditions to apply on the content returned 
	 * 
	 * @return  void
	 * 
	 * @since   1.0
	 */
	protected function getContent($id = null){
		
		if ( $id != null)
			$this->app->setBody(json_encode('Content with id '.$id));
		else 
			$this->app->setBody(json_encode('All entries from content'));
		
	}

}