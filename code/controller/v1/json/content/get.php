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
		
		// Contet is not refered by a number id
		if( count($routeParts) > 0 && !is_numeric($routeParts[0]) && !empty($routeParts[0]) )
			throw new InvalidArgumentException('Unknown content path', 503);
		
		// All content is refered
		if ( count($routeParts) == 0 )
			return null;
		
		// Specific content id
		return $routeParts[0];
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
		// Content id
		$id = $this->getContentId();
		
		// Returned data
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
		
		// content model
		include_once(JPATH_BASE . '/model/content.php');
		
		// new content model
		$model = new WebServiceModelContent;
		
		// get content state
		$modelState = $model->getState();
		
		// set content data that we need
		$modelState->set('type', 'general');
		$modelState->set('content_id', $id);
		
		// get the requested data
		$this->app->setBody(json_encode($model->getData()));
	}

}