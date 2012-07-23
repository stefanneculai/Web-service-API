<?php
/**
 * @package     WebService.Application
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Web Service application router.
 *
 * @package     WebService.Application
 * @subpackage  Application
 * @since       1.0
 */
class WebServiceApplicationWebRouter extends JApplicationWebRouterRest
{
	/**
	 * @var    string  The api content type to use for messaging.
	 * @since  1.0
	 */
	protected $apiType = 'json';

	/**
	 * @var    integer  The api revision number.
	 * @since  1.0
	 */
	protected $apiVersion = 'v1';

	/**
	 * @var    array  The URL => controller map for routing requests.
	 * @since  1.0
	 */
	protected $routeMap = array(
		'#([\w\/]*)/(\d+)/(\w+)#i' => '$3'
	);

	/**
	 * @var    array  The possible actions
	 * @since  1.0
	 */
	protected $actionsMap = array(
		'#([\w\/]*)/(\d+)/(like)#i' => '$1/$2',
		'#([\w\/]*)/(\d+)/(unlike)#i' => '$1/$2',
		'#([\w\/]*)/(\d+)/(hit)#i' => '$1/$2',
		'#([\w\/]*)/(count)#i' => '$1'
	);

	protected $singular = array(
		'/(quiz)zes$/i'             => "$1",
		'/(matr)ices$/i'		    => "$1ix",
		'/(vert|ind)ices$/i'        => "$1ex",
		'/^(ox)en$/i'               => "$1",
		'/(alias)es$/i'             => "$1",
		'/(octop|vir)i$/i'          => "$1us",
		'/(cris|ax|test)es$/i'      => "$1is",
		'/(shoe)s$/i'               => "$1",
		'/(o)es$/i'                 => "$1",
		'/(bus)es$/i'               => "$1",
		'/([m|l])ice$/i'            => "$1ouse",
		'/(x|ch|ss|sh)es$/i'        => "$1",
		'/(m)ovies$/i'              => "$1ovie",
		'/(s)eries$/i'              => "$1eries",
		'/([^aeiouy]|qu)ies$/i'     => "$1y",
		'/([lr])ves$/i'             => "$1f",
		'/(tive)s$/i'               => "$1",
		'/(hive)s$/i'               => "$1",
		'/(li|wi|kni)ves$/i'        => "$1fe",
		'/(shea|loa|lea|thie)ves$/i' => "$1f",
		'/(^analy)ses$/i'           => "$1sis",
		'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i'  => "$1$2sis",
		'/([ti])a$/i'               => "$1um",
		'/(n)ews$/i'                => "$1ews",
		'/(h|bl)ouses$/i'           => "$1ouse",
		'/(corpse)s$/i'             => "$1",
		'/(us)es$/i'                => "$1",
		'/s$/i'                     => ""
	);

	protected $irregular = array(
		'move'   => 'moves',
		'foot'   => 'feet',
		'goose'  => 'geese',
		'sex'    => 'sexes',
		'child'  => 'children',
		'man'    => 'men',
		'tooth'  => 'teeth',
		'person' => 'people'
	);

	protected $uncountable = array(
		'sheep',
		'fish',
		'deer',
		'series',
		'species',
		'money',
		'rice',
		'information',
		'equipment'
	);

	/**
	 * @var    array  The possible actions
	 */
	protected $actions = array('like', 'unlike', 'count', 'hit');

	/**
	 * Singularize word
	 *
	 * @param   string  $string  A string with the word to singularize
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function singularize( $string )
	{
		// Save some time in the case that singular and plural are the same
		if (in_array(strtolower($string), $this->uncountable))
		{
			return $string;
		}

		// Check for irregular plural forms
		foreach ( $this->irregular as $result => $pattern )
		{
			$pattern = '/' . $pattern . '$/i';

			if (preg_match($pattern, $string))
			{
				return preg_replace($pattern, $result, $string);
			}
		}

		// Check for matches using regular expressions
		foreach ( $this->singular as $pattern => $result )
		{
			if (preg_match($pattern, $string))
			{
				return preg_replace($pattern, $result, $string);
			}
		}

		return $string;
	}

	/**
	 * Find and execute the appropriate controller based on a given route.
	 *
	 * @param   string  $route  The route string for which to find and execute a controller.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 *
	 */
	public function execute($route)
	{
		// Allow poor clients to make advanced requests
		$this->setMethodInPostRequest(true);

		// Move actions from route to input
		$route = $this->actionRoute($route);

		// Make route to match our API structure
		$route = $this->reorderRoute($route);

		// Parse route to get only the main
		$route = $this->rewriteRoute($route);

		// Set controller prefix
		$this->setControllerPrefix('WebServiceController' . ucfirst($this->apiVersion) . ucfirst($this->apiType));

		// Get the controller name based on the route patterns and requested route.
		$name = $this->parseRoute($route);

		$type = $this->singularize($name);

		// Get the effective route after matching the controller
		$route = $this->removeControllerFromRoute($route);

		// Set the remainder of the route path in the input object as a local route.
		$this->input->get->set('@route', $route);

		// Append the HTTP method based suffix.
		$name .= $this->fetchControllerSuffix();

		// Get the controller object by name.
		$controller = $this->fetchController($name, $type);

		// Execute the controller.
		$controller->execute();
	}

	/**
	 * Rewrite routes to be compatible with the application's controller layout.
	 *
	 * @param   string  $input  Route string to rewrite.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function reorderRoute($input)
	{
		// Get the patterns and replacement fields from the route map.
		$pattern = array_keys($this->routeMap);
		$replace = array_values($this->routeMap);

		// Replace the route
		$output = preg_replace($pattern, $replace, $input);

		// If there are changes in the route, make the changes in the input
		foreach ($this->routeMap as $pattern => $replace)
		{
			// /collection1/id/collection2 becames /collection2?collection1=id
			if (preg_match($pattern, $input, $matches))
			{
				$this->input->get->set($this->singularize($matches[1]) . '_id', $matches[2]);
			}
		}

		return $output;
	}

	/**
	 * Move actions from route to input and change route
	 *
	 * @param   string  $input  Route string to review
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function actionRoute($input)
	{
		$pattern = array_keys($this->actionsMap);
		$replace = array_values($this->actionsMap);

		$output = preg_replace($pattern, $replace, $input);

		foreach ($this->actionsMap as $pattern => $replace)
		{
			// /collection1/id/collection2 becames /collection2?collection1=id
			if (preg_match($pattern, $input, $matches))
			{
				if (in_array($matches[2], $this->actions))
				{
					$this->input->get->set('action', $matches[2]);
				}
				elseif (in_array($matches[3], $this->actions))
				{
					$this->input->get->set('action', $matches[3]);
				}
			}
		}

		return $output;
	}

	/**
	 * Get the effective route after matching the controller by removing the controller name
	 *
	 * @param   string  $route  The route string which to parse
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function removeControllerFromRoute($route)
	{
		// Explode route
		$parts = explode('/', trim($route, ' /'));

		// Remove the first part of the route
		unset($parts[0]);

		// Reindex the array
		$parts = array_values($parts);

		// Build route back
		$route = implode('/', $parts);

		return $route;
	}

	/**
	 * Gets from the current route the api version and the output format
	 *
	 * @param   string  $route  The route string which to parse
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function rewriteRoute($route)
	{
		// Get the path from the route
		$uri = JUri::getInstance($route);

		// Explode path in multiple parts
		$parts = explode('/', trim($uri->getPath(), ' /'));

		// Get version
		if (preg_match('/^v\d$/', $parts[0]))
		{
			$this->apiVersion = $parts[0];
			unset($parts[0]);
			$parts = array_values($parts);
		}

		// Check if there is a json request
		if (preg_match('/(\.json)$/', $parts[count($parts) - 1]))
		{
			$this->apiType = 'json';
			$parts[count($parts) - 1] = str_replace('.json', '', $parts[count($parts) - 1]);
		}

		// Check if there is a xml request
		if (preg_match('/(\.xml)$/', $parts[count($parts) - 1]))
		{
			$this->apiType = 'xml';
			$parts[count($parts) - 1] = str_replace('.xml', '', $parts[count($parts) - 1]);
		}

		// Build route back
		$route = implode('/', $parts);

		return $route;
	}

	/**
	 * Get a JController object for a given name.
	 *
	 * @param   string  $name  The controller name (excluding prefix) for which to fetch and instance.
	 * @param   string  $type  The type of the content
	 *
	 * @return  JController
	 *
	 * @since   12.3
	 * @throws  RuntimeException
	 *
	 * @codeCoverageIgnore
	 */
	protected function fetchController($name, $type)
	{
		// Derive the controller class name.
		$class = $this->controllerPrefix . ucfirst($name);

		// If the controller class does not exist panic.
		if (!class_exists($class) || !is_subclass_of($class, 'JController'))
		{
			throw new RuntimeException(sprintf('Unable to locate controller `%s`.', $class), 404);
		}

		// Instantiate the controller.
		$controller = new $class($type, $this->input, $this->app);

		return $controller;
	}
}
