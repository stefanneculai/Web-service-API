<?php

/**
 * @package     WebService.Tests
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

include_once __DIR__ . '/stubs/WebServiceApplicationWebInspector.php';

/**
 * Test Case class for WebServiceApplicationWeb
 *
 * @package     WebService.Tests
 * @subpackage  Application
 * @since       1.0
 */
class WebServiceApplicationWebTest extends TestCase
{
	/**
	 * Value for test host.
	 *
	 * @var    string
	 * @since  11.3
	 */
	const TEST_HTTP_HOST = 'mydomain.com';

	/**
	 * Value for test user agent.
	 *
	 * @var    string
	 * @since  11.3
	 */
	const TEST_USER_AGENT = 'Mozilla/5.0';

	/**
	 * Value for test user agent.
	 *
	 * @var    string
	 * @since  11.3
	 */
	const TEST_REQUEST_URI = '/index.php';

	/**
	 * An instance of the class to test.
	 *
	 * @var    JApplicationWebInspector
	 * @since  11.3
	 */
	private $_instance;

	/**
	 * Prepares the environment before running a test.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		parent::setUp();

		$_SERVER['HTTP_HOST'] = self::TEST_HTTP_HOST;
		$_SERVER['HTTP_USER_AGENT'] = self::TEST_USER_AGENT;
		$_SERVER['REQUEST_URI'] = self::TEST_REQUEST_URI;
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		// Get a new JApplicationWebInspector instance.
		$this->_instance = new WebServiceApplicationWebInspector;

		// We are only coupled to Document and Language in JFactory.
		$this->saveFactoryState();

		JFactory::$document = $this->getMockDocument();
		JFactory::$language = $this->getMockLanguage();
	}

	/**
	 * Cleans up the environment after running a test.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function tearDown()
	{
		// Reset the dispatcher instance.
		TestReflection::setValue('JEventDispatcher', 'instance', null);

		// Reset some web inspector static settings.
		WebServiceApplicationWebInspector::$headersSent = false;
		WebServiceApplicationWebInspector::$connectionAlive = true;

		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Test database load
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testLoadDatabase()
	{
		// Test load database if null passed as argument
		$configOptions = array(
			'db_driver' => 'sqlite',
			'db_name' => ':memory:',
			'db_prefix' => 'ws_',
			'db_host' => 'localhost',
			'db_user' => '',
			'db_password' => ''
		);

		$options = array(
			'driver' => 'sqlite',
			'database' => ':memory:',
			'prefix' => 'ws_',
			'host' => 'localhost',
			'user' => '',
			'password' => ''
		);

		$config = TestReflection::getValue($this->_instance, 'config');
		$config->loadArray($configOptions);
		TestReflection::setValue($this->_instance, 'config', $config);

		$expected = JDatabaseDriver::getInstance($options);
		$expected->select(':memory:');

		TestReflection::invoke($this->_instance, 'loadDatabase');
		$actual = TestReflection::getValue($this->_instance, 'db');
		$this->assertEquals($expected, $actual);

		// Test load database if db passed as argument
		$expected = JDatabaseDriver::getInstance(array('database' => 'test_database'));
		TestReflection::invoke($this->_instance, 'loadDatabase', $expected);
		$actual = TestReflection::getValue($this->_instance, 'db');
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Test router load
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testLoadRouter()
	{
		$router = new WebServiceApplicationWebRouter($this->getMockWeb(), new JInput);
		$this->_instance->loadRouter($router);
		$actual = TestReflection::getValue($this->_instance, 'router');
		$this->assertEquals($router, $actual);
	}

	/**
	 * Provides test data for readConfig()
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedReadConfig()
	{
		// File name
		return array(
				array(''),
				array('empty')
				);
	}

	/**
	 * Test read config
	 *
	 * @param   string  $file  The name of the input file
	 *
	 * @return  void
	 *
	 * @dataProvider  seedReadConfig
	 * @since   1.0
	 */
	public function testReadConfig($file)
	{
		$this->setExpectedException('RuntimeException');
		$this->_instance->readConfig($file);
	}

	/**
	 * Test fetch routes
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testFetchRoutes()
	{
		$actual = TestReflection::invoke($this->_instance, 'fetchRoutes');
		$this->assertEquals('bar', $actual->foo);
	}

	/**
	 * Test fetch routes
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testAddRoutes()
	{
		$routerMock = $this->getMock('WebServiceApplicationWebRouter', array('addMap'), array($this->getMockWeb(), new JInput));

		$routerMock->expects($this->any())
					->method('addMap')
					->will($this->throwException(new RuntimeException));

		$this->setExpectedException('RuntimeException');

		TestReflection::setValue($this->_instance, 'router', $routerMock);
		TestReflection::invoke($this->_instance, 'addRoutes', array());
		TestReflection::invoke($this->_instance, 'addRoutes', array('foo', 'bar'));
	}
}
