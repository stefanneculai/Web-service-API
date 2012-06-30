<?php
/**
 * @package     WebService.Tests
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test Case class for WebServiceModelBase
 *
 * @package     WebService.Tests
 * @subpackage  Application
 * @since       1.0
 *
 */

class WebServiceModelBaseTest extends TestCase
{
	private $_instance;

	private $_state;

	/**
	 * Prepares the environment before running a test.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 *
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		JFactory::$session = $this->getMockSession();
		JFactory::$application = $this->getMockWeb();

		$options = array(
			'driver' => 'sqlite',
			'database' => ':memory:',
			'prefix' => 'ws_'
		);

		$driver = JDatabaseDriver::getInstance($options);

		$pdo = new PDO('sqlite::memory:');
		$pdo->exec(file_get_contents(__DIR__ . '/stubs/ws.sql')) or die(print_r($pdo->errorInfo()));

		TestReflection::setValue($driver, 'connection', $pdo);
		JFactory::$database = $driver;

		$this->_instance = new WebServiceModelBase(null, $driver);
		$this->_state = TestReflection::invoke($this->_instance, 'getState');
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
		$this->_instance = null;
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Test existsItem()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceModelBase::existsItem
	 * @since   1.0
	 */
	public function testItemExists()
	{
		$actual = TestReflection::invoke($this->_instance, 'existsItem', 1);
		$this->assertEquals(true, $actual);
	}

	/**
	 * Test existsItem()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceModelBase::existsItem
	 * @since   1.0
	 */
	public function testItemDoesNotExists()
	{
		$actual = TestReflection::invoke($this->_instance, 'existsItem', 2);
		$this->assertEquals(false, $actual);
	}

	/**
	 * Test getTypes()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceModelBase::getTypes
	 * @since   1.0
	 */
	public function testGetTypes()
	{
		$types = TestReflection::invoke($this->_instance, 'getTypes', 1);

		$expectedObj = new stdClass;
		$expectedObj->content_id = 1;
		$expectedObj->type = 'general';

		$this->assertEquals(array(1 => $expectedObj), $types);
	}

	/**
	 * Test getItem()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceModelBase::getItem
	 * @since   1.0
	 */
	public function testGetItem()
	{
		TestReflection::invoke($this->_state, 'set', 'content.id', '1');
		TestReflection::invoke($this->_state, 'set', 'content.type', 'general');
		$actual = TestReflection::invoke($this->_instance, 'getItem');

		$this->assertEquals(1, $actual->id);
	}

	/**
	 * Tests __construct()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceModelBase::__construct
	 * @since   1.0
	 */
	public function test__construct()
	{
		JFactory::$database = 'factory db';
		JFactory::$application = $this->getMockWeb();

		// Construct the object.
		$model = new WebServiceModelBase;

		// Verify that the values injected into the constructor are present.
		$this->assertEquals('factory db', TestReflection::getValue($model, 'db'));
	}
}
