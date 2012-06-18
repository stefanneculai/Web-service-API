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
	 * Find and execute the appropriate controller based on a given route.
	 *
	 * @param   string  $route  The route string for which to find and execute a controller.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 */
	public function execute($route)
	{
		// Make route to match our API structure
		$route = $this->reorderRoute($route);

		// Parse route to get only the main
		$route = $this->rewriteRoute($route);

		// Set controller prefix
		$this->setControllerPrefix('WebServiceController' . ucfirst($this->apiVersion) . ucfirst($this->apiType));

		// Get the controller name based on the route patterns and requested route.
		$name = $this->parseRoute($route);

		// Get the effective route after matching the controller
		$route = $this->removeControllerFromRoute($route);

		// Set the remainder of the route path in the input object as a local route.
		$this->input->get->set('@route', $route);

		// Append the HTTP method based suffix.
		$name .= $this->fetchControllerSuffix();

		// Get the controller object by name.
		$controller = $this->fetchController($name);

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
				$this->input->get->set($matches[1], $matches[2]);
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
	 * Get the controller class suffix string.
	 *
	 * @return  string
	 *
	 * @since   12.3
	 * @throws  RuntimeException
	 */
	protected function fetchControllerSuffix()
	{
		// Validate that we have a map to handle the given HTTP method.
		if (!isset($this->suffixMap[$this->input->getMethod()]))
		{
			throw new RuntimeException(sprintf('Unable to support the HTTP method `%s`.', $this->input->getMethod()), 404);
		}

		$postMethod = $this->input->get->getWord('_method');
		if (strcmp(strtoupper($this->input->server->getMethod()), 'POST') === 0  && $postMethod && isset($this->suffixMap[strtoupper($postMethod)]))
		{
			return ucfirst($this->suffixMap[strtoupper($postMethod)]);
		}

		return ucfirst($this->suffixMap[$this->input->getMethod()]);
	}
}
