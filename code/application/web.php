<?php
/**
 * @package     WebService.Application
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Web Service Api web application class.
 *
 * @package     WebService.Application
 * @subpackage  Application
 * @since       1.0
 */
class WebServiceApplicationWeb extends JApplicationWeb
{
	/**
	 * @var    string  Response mime type.  By default this application returns JSON.
	 * @since  1.0
	 */
	public $mimeType = 'application/json';

	/**
	 * @var    JDatabaseDriver  A database object for the application to use.
	 * @since  1.0
	 */
	protected $db;

	/**
	 * @var    WebServiceRouter  A router object for the application to use.
	 * @since  1.0
	 */
	protected $router;

	/**
	 * @var    WebServiceErrors  An error object for the application to use.
	 * @since  1.0
	 */
	public $errors;

	/**
	 * The start time for measuring the execution time.
	 *
	 * @var    float
	 * @since  1.0
	 */
	private $_startTime;

	/**
	 * Array of stdClass objects containing the routes for the application
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $routes;

	/**
	 * String with path of the configuration directory
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $configPath;

	/**
	 * Overrides the parent constructor to set the execution start time.
	 *
	 * @param   mixed  $input   An optional argument to provide dependency injection for the application's
	 *                          input object.  If the argument is a JInput object that object will become
	 *                          the application's input object, otherwise a default input object is created.
	 * @param   mixed  $config  An optional argument to provide dependency injection for the application's
	 *                          config object.  If the argument is a JRegistry object that object will become
	 *                          the application's config object, otherwise a default config object is created.
	 * @param   mixed  $client  An optional argument to provide dependency injection for the application's
	 *                          client object.  If the argument is a JApplicationWebClient object that object will become
	 *                          the application's client object, otherwise a default client object is created.
	 *
	 * @since   11.3
	 */
	public function __construct(JInput $input = null, JRegistry $config = null, JApplicationWebClient $client = null)
	{
		$this->_startTime = microtime(true);

		parent::__construct($input, $config, $client);

		$this->errors = new WebServiceApplicationWebErrors($this, $this->input);
		$this->errors->checkSupressResponseCodes();
	}

	/**
	 * Allows the application to load a custom or default database driver.
	 *
	 * @param   JDatabaseDriver  $driver  An optional database driver object. If omitted, the application driver is created.
	 *
	 * @return  JApplicationBase This method is chainable.
	 *
	 * @since   12.1
	 */
	public function loadDatabase(JDatabaseDriver $driver = null)
	{
		if ($driver === null)
		{
			$this->db = JDatabaseDriver::getInstance(
				array(
					'driver' => $this->get('db_driver'),
					'host' => $this->get('db_host'),
					'user' => $this->get('db_user'),
					'password' => $this->get('db_pass'),
					'database' => $this->get('db_name'),
					'prefix' => $this->get('db_prefix')
				)
			);

			// Select the database.
			$this->db->select($this->get('db_name'));
		}
		// Use the given database driver object.
		else
		{
			$this->db = $driver;
		}

		// Set the database to our static cache.
		JFactory::$database = $this->db;

		return $this;
	}

	/**
	 * Allows the application to load a custom or default router.
	 *
	 * @param   WebServiceApplicationWebRouter  $router  An optional router object. If omitted, the standard router is created.
	 *
	 * @return  JApplicationWeb This method is chainable.
	 *
	 * @since   1.0
	 */
	public function loadRouter(WebServiceApplicationWebRouter $router = null)
	{
		$this->router = ($router === null) ? new WebServiceApplicationWebRouter($this, $this->input) : $router;

		return $this;
	}

	/**
	 * Load additional data for a content after loading it
	 *
	 * @param   JContnet  $content  The content object
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function contentAfterLoad($content)
	{
		// Parse media
		$media = json_decode($content->media);
		$mediaArray = array();

		if ($media instanceOf stdClass)
		{
			foreach ($media as $key => $value)
			{
				$md = new stdClass;
				$md->id = $key;
				$md->image_url = $value;

				array_push($mediaArray, $md);
			}
		}

		$content->mediaArray = new stdClass;
		$content->mediaArray->data = $mediaArray;
		$content->mediaArray->count = count($mediaArray);

		// Get likes
		$db = JFactory::$database;
		$query = $db->getQuery(true);
		$query->select($query->qn('user_id'));
		$query->select($query->qn('like_state'));
		$query->from('#__content_likes');

		$query->where('content_id = ' . (int) $content->content_id);

		$session = JFactory::getSession();
		$userLikes = $session->get('userID');

		if (isset($userLikes))
		{
			$query->where('user_id = ' . (int) $userLikes);
		}

		$db->setQuery($query);

		$likes = $db->loadObjectList();

		$content->likesArray = new stdClass;
		$content->likesArray->data = $likes;
		$content->likesArray->count = count($likes);

		// Get content type
		if (isset($userLikes))
		{
			$db = JFactory::$database;
			$query = $db->getQuery(true);

			$query->select($query->qn('alias'));
			$query->from('#__content_types');

			$query->where('type_id = ' . (int) $content->type_id);

			$db->setQuery($query);

			$type = $db->loadResult();

			$content->typeAlias = $type;
		}
	}

	/**
	 * Execute the application.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function doExecute()
	{
		try
		{
			// Set db in factory
			$this->dbo = JFactory::getDbo();

			// Set session in factory
			$this->session = JFactory::getSession();
			$this->session->initialise($this->input);
			$this->session->start();

			// Set application in factory
			JFactory::$application = $this;

			$this->loadDispatcher();

			// Refister event for changing content after loading it
			$this->registerEvent('onContentAfterLoad', 'WebServiceApplicationWeb::contentAfterLoad');

			// Get routes
			$this->routes = $this->fetchRoutes();
			$this->addRoutes($this->routes);

			// Get the controller instance based on the request and execute it
			$this->router->execute($this->get('uri.route'));
		}
		// If there is any exception send it in the request
		catch (Exception $e)
		{
			$this->errors->addError('808', array($e->getMessage()));
			$this->setBody(json_encode($this->errors->getErrors()));
			$this->setHeader('status', $this->errors->getResponseCode(), true);
			return;
		}
	}

	/**
	 * Fetch data from configuration file
	 *
	 * @param   string  $fileName  The file name
	 *
	 * @return  array
	 *
	 * @since   1.0
	 * @throws   RuntimeException if file cannot be read.
	 */
	public function readConfig($fileName)
	{
		// Initialise variables.
		$config = array();

		$file = realpath(JPATH_CONFIGURATION . '/' . $fileName . '.json');

		// Check if file exists
		if (!$file)
		{
			$file = realpath(JPATH_CONFIGURATION . '/' . $fileName . '.dist.json');
		}

		// Check if file is readable
		if (!is_readable($file))
		{
			throw new RuntimeException(sprintf('File %s is unreadable.', $file));
		}

		// Load the configuration file into an object.
		$config = json_decode(file_get_contents($file));

		// Check if configuration file hasn't any error
		if ($config == null)
		{
			throw new RuntimeException(sprintf('Configuration file %s cannot be decoded.', $file));
		}

		return $config;
	}

	/**
	 * Fetch the routes for the application.
	 *
	 * @return  object  An object to be loaded into the application configuration.
	 *
	 * @since   1.0
	 */
	protected function fetchRoutes()
	{
		return $this->readConfig('routes');
	}

	/**
	 * Method to set the routes for the application
	 *
	 * @param   array  $routes  An array of routes to add to the application
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function addRoutes($routes)
	{
		foreach ($routes as $route => $controller)
		{
			$this->router->addMap($route, $controller);
		}
	}

	/**
	 * Fetch the configuration data for the application.
	 *
	 * @return  object  An object to be loaded into the application configuration.
	 *
	 * @since   1.0
	 * @throws  RuntimeException if file cannot be read.
	 */
	protected function fetchConfigurationData()
	{
		return $this->readConfig('config');
	}

	/**
	 * Method to send the application response to the client.  All headers will be sent prior to the main
	 * application output data.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function respond()
	{
		$runtime = microtime(true) - $this->_startTime;

		// Send the content-type header.
		$this->setHeader('Content-Type', $this->mimeType . '; charset=' . $this->charSet);

		// Set the Server and X-Powered-By Header.
		$this->setHeader('Server', '', true);
		$this->setHeader('X-Powered-By', 'Web Service/1.0', true);
		$this->setHeader('X-Runtime', $runtime, true);

		// Send the response.
		$this->sendHeaders();
		echo $this->getBody();
	}
}
