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
 * @package     WebSercice.Application
 * @subpackage  Application
 * @since       1.0
 */
class WebServiceApplicationWebRouter
{
	/**
	 * @var    string  The api content type to use for messaging.
	 * @since  1.0
	 */
	protected $apiType = 'json';

	/**
	 * @var    string  The api output type to use for content fields.
	 * @since  1.0
	 */
	protected $apiOutput = 'raw';

	/**
	 * @var    integer  The api revision number.
	 * @since  1.0
	 */
	protected $apiVersion = 1;

	/**
	 * @var    JApplicationWeb  The application object.
	 * @since  1.0
	 */
	protected $app;

	/**
	 * @var    JInput  The application input object.
	 * @since  1.0
	 */
	protected $input;

	/**
	 * @var    array  The map of HTTP methods to controller types.
	 * @since  1.0
	 */
	protected $methodMap = array(
		'PUT' => 'Create',
		'POST' => 'Update',
		'PATCH' => 'Update',
		'DELETE' => 'Delete',
		'GET' => 'Get',
		'OPTIONS' => 'Options'
	);

	/**
	 * @var    array  The URL => controller map for routing requests.
	 * @since  1.0
	 */
	protected $routeMap = array(
		'#([\w\/]*)/(\d+)/(\w+)#i' => '$1/$3/$2'
	);

	/**
	 * Object constructor.
	 *
	 * @param   JInput           $input  The input object.
	 * @param   JApplicationWeb  $app    The application object.
	 *
	 * @since 1.0
	 */
	public function __construct(JInput $input, JApplicationWeb $app)
	{
		$this->input = $input;
		$this->app = $app;
	}

	/**
	 * Method to get a controller object based on the incoming request.
	 *
	 * @param   string  $route  The request route for which to get a controller.
	 *
	 * @return  JController
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function getController($route)
	{
		// Build the base namespace for the controller class based on API version and type.
		$base = $this->fetchControllerBaseName();

		// Get the request method.
		$method = $this->fetchRequestMethod();

		// Get a URI object for the current route.
		$uri = JUri::getInstance($this->rewriteRoute($route));

		// Get the controller object.
		$class = $this->fetchControllerClass($base, $uri, $method);
		$controller = new $class($this->input, $this->app);

		return $controller;
	}

	/**
	 * Method to get the API request format information.
	 *
	 * @param   string  $type  The request Content-Type header string.
	 *
	 * @return void
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	protected function detectRequestFormat($type)
	{
		// Make sure we are dealing with a valid WebService content type.
		$matches = array();
		if (!preg_match('#application/vnd\.webservice(\.([0-9]+))?(\.([a-z]+))?(\+([a-z]+))?#', strtolower($type), $matches))
		{
			throw new InvalidArgumentException($type . ' not a valid Web Service mime type.', 400);
		}

		// Set the API version if available.
		if (!empty($matches[2]))
		{
			$this->apiVersion = (int) $matches[2];
		}

		// Set the API output type if available.
		if (!empty($matches[4]))
		{
			$this->apiOutput = $matches[4];
		}

		// Set the API request type if available.
		if (!empty($matches[6]))
		{
			$this->apiType = $matches[6];
		}
	}

	/**
	 * Get the content type from the request.  This will fallback to a default.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function fetchContentType()
	{
		// If no explicit content type is set use the default.
		if (empty($_SERVER['CONTENT_TYPE']))
		{
			$type = 'application/vnd.webservice+json';
		}
		// If we have an explicit content type set, get it from the input.
		else
		{
			$type = strtolower($this->input->server->getString('CONTENT_TYPE'));
		}

		// Clean up the default json fallback type.
		if ($type == 'application/json')
		{
			$type = 'application/vnd.webservice+json';
		}

		return $type;
	}

	/**
	 * Get the base namespace for the controller class based on API version and type.
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 * @since   1.0
	 */
	protected function fetchControllerBaseName()
	{
		return 'WebServiceControllerV' . $this->apiVersion . ucfirst($this->apiType);
	}

	/**
	 * Method to get a controller class name based on the incoming request.
	 *
	 * @param   string  $base    Controller class base name.
	 * @param   JURI    $uri     JURI object for the route to fetch the controller class.
	 * @param   string  $method  HTTP request method.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	protected function fetchControllerClass($base, $uri, $method)
	{
		// Convert the base path into an array of segments to build the controller.
		$parts = explode('/', trim($uri->getPath(), ' /'));

		// Iterate backwards over the route segments so we get the most specific class to handle the request.
		for ($i = count($parts); $i > 0; $i--)
		{
			// Build the controller class name from the path information.
			$class = $base . JStringNormalise::toCamelCase(implode(' ', array_slice($parts, 0, $i))) . ucfirst($this->methodMap[$method]);

			// If the requested controller exists let's use it.
			if (class_exists($class))
			{
				// Set the remainder of the route path in the input object as a local route.
				$this->input->get->set('@route', implode('/', array_slice($parts, $i)));

				return $class;
			}
		}

		// Nothing found. Panic.
		throw new InvalidArgumentException('Unable to handle the request for route: ' . $uri, 400);
	}

	/**
	 * Method to get the request method for the incoming request.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	protected function fetchRequestMethod()
	{
		// Some clients don't support anything other than GET and POST so let's give them a way to play too.
		$postMethod = $this->input->get->getWord('_method');
		if (strcmp(strtoupper($this->input->server->getMethod()), 'POST') === 0  && $postMethod)
		{
			$method = strtoupper($postMethod);
		}
		// Standard use case.
		else
		{
			$method = strtoupper($this->input->server->getMethod());
		}

		// Is the request method supported?
		if (empty($this->methodMap[$method]))
		{
			throw new InvalidArgumentException('Unknown request method: ' . $method, 400);
		}

		return $method;
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
	protected function rewriteRoute($input)
	{
		// Get the patterns and replacement fields from the route map.
		$pattern = array_keys($this->routeMap);
		$replace = array_values($this->routeMap);

		$output = preg_replace($pattern, $replace, $input);

		return $output;
	}
}
