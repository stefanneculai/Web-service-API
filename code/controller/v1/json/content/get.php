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
	 * @var    string  The output results limit
	 * @since  1.0
	 */
	protected $limit = 20;
	
	/**
	 * @var    string  Max results per page
	 * @since  1.0
	 */
	protected $maxResults = 100;
	
	/**
	 * @var    string  The output results offset
	 * @since  1.0
	 */
	protected $offset = 0;
	
	/**
	 * @var    array  The output results fields 
	 * @since  1.0
	 */
	protected $fields;
	
	/**
	 * @var    string  The content id. It may be numeric id or '*' if all content is needed
	 * @since  1.0
	 */
	protected $id = '*';
	
	/**
	 * @var    string  The results order 
	 * @since  1.0
	 */
	protected $order = 'asc';
	
	/**
	 * @var    string  Max results per page
	 * @since  1.0
	 */
	protected $since = '1970-01-01';
	
	/**
	 * @var    string  Max results per page
	 * @since  1.0
	 */
	protected $before = 'now';
	
	/**
	 * @var    integer  Supress response codes. 401 for Unauthorized; 200 for OK
	 * @since  1.0
	 */
	protected $responseCode = 401;
	
	/**
	 * Get route parts from the input or the default one
	 * 
	 * @return  string
	 * 
	 * @since   1.0
	 */
	protected function getContentId(){
		
		// Get route from the input
		$route = $this->input->get->getString('@route');
		
		if(preg_match("/json$/", $route) >= 0)
			$route = str_replace('.json', '', $route);
		
		// Break route into more parts
		$routeParts = explode('/',$route);
		
		// Contet is not refered by a number id
		if( count($routeParts) > 0 && (!is_numeric($routeParts[0]) || $routeParts[0] < 0) && !empty($routeParts[0]))
			throw new InvalidArgumentException('Unknown content path.', $this->responseCode);
		
		// All content is refered
		if ( count($routeParts) == 0 || strlen($routeParts[0]) === 0 )
			return $this->id;
		
		// Specific content id
		return $routeParts[0];
	}
	
	/**
	 * Get the offset from input or the default one
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function getOffset(){
		$offset = $this->input->get->getString('offset');
		
		if (isset($offset)){
			if (is_numeric ($offset) && $offset > 0)
				return $offset;
			
			throw new InvalidArgumentException('Offset should be a positive number. By default the limit is set to '.$this->offset, $this->responseCode);
		}
		else
			return $this->offset;
	}
	
	/**
	 * Get the limit from input or the default one
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function getLimit(){
		$limit = $this->input->get->getString('limit');
		
		if (isset($limit)){
			$limit = min($this->maxResults, $limit);
			if (is_numeric($limit) && $limit > 0)
				return $limit;
			
			throw new InvalidArgumentException('Limit should be a positive number. By default the limit is set to '.$this->limit, $this->responseCode);
		}
		else
			return $this->limit;
	}
	
	/**
	 * Get the fields from input or the default one
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	protected function getFields(){
		
		$fields = $this->input->get->getString('fields');
		
		if (isset($fields)){
			$fields = preg_split('#[\s,]+#', $fields, null, PREG_SPLIT_NO_EMPTY);
		
			if($fields === FALSE)
				return null;
			
			return $fields;
		}
		else
			return null;
	}
	
	/**
	 * Get the order from input or the default one
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function getOrder(){
		
		$order = $this->input->get->getString('order');
		
		if(isset($order)){
			$order = strtolower($order);

			if (strcmp ($order, 'asc') === 0 || strcmp($order, 'desc') === 0)
				return $order;
			
			throw new InvalidArgumentException('Order should be "asc" or "desc". By default order is set to '.$this->order, $this->responseCode);
		}
		else 
			return $this->order;
	}
	
	/**
	 * Get the since date limitation from input or the default one
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function getSince(){
		$since = $this->input->get->getString('since');
		
		if(isset($since)){

			if (strtotime($since) != FALSE)				
				return strptime(strtotime($since),'%d/%m/%Y');
			
			throw new InvalidArgumentException('Since should be a valid date. By default all the results are returned.', $this->responseCode);
		}
		else
			return strptime(strtotime($this->since),'%d/%m/%Y');
	}
	
	/**
	 * Get the before date limitation from input or the default one
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function getBefore(){
		
		$before = $this->input->get->getString('before');
		
		if(isset($before)){
			
			// Notice: PHP 5.1 would return -1
			if (strtotime($before) != FALSE)
				return strptime(strtotime($before),'%d/%m/%Y');
				
			throw new InvalidArgumentException('Before should be a valid date. By default all the results until the current date are returned.', $this->responseCode);
		}
		else
			return strptime(strtotime($this->before),'%d/%m/%Y');
	}
	
	/**
	 * Check the input for supress_response_codes = true in order to supress the error codes.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function checkSupressResponseCodes(){
		
		$errCode = $this->input->get->getString('suppress_response_codes');
		
		if(isset($errCode)){
			$errCode = strtolower($errCode);
			
			if(strcmp($errCode, 'true') === 0){
				$this->responseCode = 200;
				return;
			}
			
			if(strcmp($errCode, 'false') === 0){
				$this->responseCode = 401;
				return;
			}
			
			throw new InvalidArgumentException('suppress_response_codes should be set to true or false', $this->responseCode);
		}
		
	}
	
	public function getResponseCode(){
		
		return $this->responseCode;
		
	}
	
	/**
	 * Init parameters 
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function init(){
		
		$this->checkSupressResponseCodes();
		
		// Content id
		$this->id = $this->getContentId();
		
		// Results offset
		$this->offset = $this->getOffset();
		
		// Results limits
		$this->limit = $this->getLimit();
		
		// Returned fields
		$this->fields = $this->getFields();
		
		// Results order
		$this->order = $this->getOrder();
		
		// Since
		$this->since = $this->getSince();
		
		// Before
		$this->before = $this->getBefore();
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
		// init request
		$this->init();
		
		// Returned data
		$data = $this->getContent($this->id);
		
		$this->app->setBody(json_encode('OK'));
	}
		
	/**
	 * Get content by id
	 * 
	 * @param   string  $id       Get content with the specified id. If id is null all entries should be returned
	 * 
	 * @return  JContent
	 * 
	 * @since   1.0
	 */
	protected function getContent($id = '*'){
		
		// content model
		include_once(JPATH_BASE . '/model/content.php');
		
		// new content model
		$model = new WebServiceModelContent;
		
		// get content state
		$modelState = $model->getState();
		
		// set content type that we need
		$modelState->set('type', 'general');
		$modelState->set('content_id', $id);
		
		// set additional options
		$modelState->set('limit', min(100, $this->input->get->getInt('per_page', 20)));
		
		// get the requested data
		$data = $model->getData();
		
		return $data;
	}
	
	/**
	 * Parse the returned data from database
	 * 
	 * @param   JContent $data as JContent
	 * 
	 * @return  array
	 * 
	 * @since   1.0
	 */
	protected function parseData($data){
		//TODO parse database data and return only what the user requested
		
		return 'Results after parse';
	}
}