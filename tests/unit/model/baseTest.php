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
		TestReflection::invoke($this->_state, 'set', 'content.type', 'general');
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
		TestReflection::invoke($this->_state, 'set', 'content.type', '2');
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
	 * Provides test data for getItem()
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetItem()
	{
		// Id, Type, Expected, Exception
		return array(
				array('1', 'general', '1', null),
				array(null, 'general', null, 'InvalidArgumentException'),
				array('1', null, '1', null),
				array('-1', null, null, 'UnexpectedValueException'),
				array('-1', 'general', false, null)
		);
	}

	/**
	 * Test getItem()
	 *
	 * @param   string  $id         The id to get
	 * @param   string  $type       The type of the content
	 * @param   string  $expected   The expected results
	 * @param   string  $exception  The expected exception
	 *
	 * @return  void
	 *
	 * @covers        WebServiceModelBase::getItem
	 * @dataProvider  seedGetItem
	 * @since         1.0
	 */
	public function testGetItem($id, $type, $expected, $exception)
	{
		TestReflection::invoke($this->_state, 'set', 'content.id', $id);
		TestReflection::invoke($this->_state, 'set', 'content.type', $type);

		if ($exception != null)
		{
			$this->setExpectedException($exception);
		}

		$actual = TestReflection::invoke($this->_instance, 'getItem');

		$this->assertEquals($expected, isset($actual->id) ? $actual->id : false);
	}

	/**
	 * Provides test data for createItem()
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedCreateItem()
	{
		// Type, Fields, Fields Array, Expected, Exception
		return array(
				array(null, null, array(), null, 'UnexpectedValueException'),
				array('general', null, array(), null, 'UnexpectedValueException'),
				array('general', 'field1, field2', array(), null, 'UnexpectedValueException'),

				// Missing not null field
				array(
						'general',
						'content_id, field1, field2',
						array('content_id' => '300', 'field1' => 'new field', 'field2' => 'new field'),
						'300',
						'RuntimeException'),

				// OK
				array(
						'general',
						'content_id, field1, field2, field3',
						array('content_id' => '300', 'field1' => 'new field', 'field2' => 'new field', 'field3' => 'new field'),
						'300',
						null),

				// Already exists -> this may be checked before creating item
				array(
						'general',
						'content_id, field1, field2, field3',
						array('content_id' => '1', 'field1' => 'new field', 'field2' => 'new field', 'field3' => 'new field'),
						'300',
						'RuntimeException')
		);
	}

	/**
	 * Test createItem()
	 *
	 * @param   string  $type        The type of the content
	 * @param   string  $fields      The fields of the new item
	 * @param   string  $fieldsData  The value of the new fields
	 * @param   string  $expected    The expected results
	 * @param   string  $exception   The expected exception
	 *
	 * @return  void
	 *
	 * @covers        WebServiceModelBase::createItem
	 * @dataProvider  seedCreateItem
	 * @since         1.0
	 */
	public function testCreateItem($type, $fields, $fieldsData, $expected, $exception)
	{
		TestReflection::invoke($this->_state, 'set', 'content.type', $type);
		TestReflection::invoke($this->_state, 'set', 'content.fields', $fields);

		foreach ($fieldsData as $key => $value)
		{
			TestReflection::invoke($this->_state, 'set', 'fields.' . $key, $value);
		}

		if ($exception != null)
		{
			$this->setExpectedException($exception);
		}

		$actual = TestReflection::invoke($this->_instance, 'createItem');

		$this->assertEquals(true, TestReflection::invoke($this->_instance, 'existsItem', $expected));
	}

	/**
	 * Provides test data for deleteItem()
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedDeleteItem()
	{
		// Id, Type, Expected, Exception
		return array(
				array('1', 'general', '1', null),
				array(null, 'general', null, 'InvalidArgumentException'),
				array('1', null, true, null),
				array('-1', null, null, 'UnexpectedValueException'),
				array('-1', 'general', false, null)
		);
	}

	/**
	 * Test deleteItem()
	 *
	 * @param   string  $id         The id to get
	 * @param   string  $type       The type of the content
	 * @param   string  $expected   The expected results
	 * @param   string  $exception  The expected exception
	 *
	 * @return  void
	 *
	 * @covers        WebServiceModelBase::deleteItem
	 * @dataProvider  seedDeleteItem
	 * @since         1.0
	 */
	public function testDeleteItem($id, $type, $expected, $exception)
	{
		TestReflection::invoke($this->_state, 'set', 'content.id', $id);
		TestReflection::invoke($this->_state, 'set', 'content.type', $type);

		if ($exception != null)
		{
			$this->setExpectedException($exception);
		}

		$actual = TestReflection::invoke($this->_instance, 'deleteItem');

		$this->assertEquals($expected, $actual);
	}

	/**
	 * Provides test data for deleteList()
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedDeleteList()
	{
		// Type, Expected, Exception
		return array(
				array('general', 0, null),
				array(null, null, 'UnexpectedValueException'),
		);
	}

	/**
	 * Test deleteItem()
	 *
	 * @param   string  $type       The type of the content
	 * @param   string  $expected   The expected results
	 * @param   string  $exception  The expected exception
	 *
	 * @return  void
	 *
	 * @covers        WebServiceModelBase::deleteList
	 * @dataProvider  seedDeleteList
	 * @since         1.0
	 */
	public function testDeleteList($type, $expected, $exception)
	{
		TestReflection::invoke($this->_state, 'set', 'content.type', $type);

		if ($exception != null)
		{
			$this->setExpectedException($exception);
		}

		$actual = TestReflection::invoke($this->_instance, 'deleteList');

		$this->assertEquals($expected, count(TestReflection::invoke($this->_instance, 'getList')));
	}

	/**
	 * Provides test data for updateItem()
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedUpdateItem()
	{
		// Id, Type, Fields, FieldsArray, Expected, Exception
		return array(
				array(null, 'general', null, array(), null, 'InvalidArgumentException'),
				array('-1', null, null, array(), null, 'UnexpectedValueException'),
				array('1', 'general', null, array(), null, 'UnexpectedValueException'),
				array('1', 'general', 'field1, field2', array(), null, 'UnexpectedValueException'),

				// Missing not null field
				array(
						'-1',
						'general',
						'field3',
						array('field3' => ''),
						false,
						null),

				// Missing not null field
				array(
						1,
						'general',
						'field3',
						array('field3' => ''),
						'1',
						'UnexpectedValueException'),

				array(
						1,
						'general',
						'field3, access',
						array('field3' => 'f3', 'access' => '2'),
						true,
						null),

				// OK
				array(
						'1',
						null,
						'field1, field2, field3',
						array('field1' => 'new field', 'field2' => 'new field', 'field3' => 'new field'),
						true,
						null)
		);
	}

	/**
	 * Test updateItem()
	 *
	 * @param   string  $id          The id of the content to update
	 * @param   string  $type        The type of the content
	 * @param   string  $fields      The fields of the new item
	 * @param   string  $fieldsData  The value of the new fields
	 * @param   string  $expected    The expected results
	 * @param   string  $exception   The expected exception
	 *
	 * @return  void
	 *
	 * @covers        WebServiceModelBase::updateItem
	 * @dataProvider  seedUpdateItem
	 * @since         1.0
	 */
	public function testUpdateItem($id, $type, $fields, $fieldsData, $expected, $exception)
	{
		TestReflection::invoke($this->_state, 'set', 'content.id', $id);
		TestReflection::invoke($this->_state, 'set', 'content.type', $type);
		TestReflection::invoke($this->_state, 'set', 'content.fields', $fields);

		foreach ($fieldsData as $key => $value)
		{
			TestReflection::invoke($this->_state, 'set', 'fields.' . $key, $value);
		}

		if ($exception != null)
		{
			$this->setExpectedException($exception);
		}

		$actual = TestReflection::invoke($this->_instance, 'updateItem');

		if (is_numeric($expected))
		{
			$this->assertEquals(true, TestReflection::invoke($this->_instance, 'existsItem', $expected));
		}
		else
		{
			$this->assertEquals($expected, $actual);
		}
	}

	/**
	 * Test getList() method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetList()
	{
		TestReflection::invoke($this->_state, 'set', 'content.type', 'general');
		$actual = TestReflection::invoke($this->_instance, 'getList');
		$this->assertEquals(2, count($actual));

		TestReflection::invoke($this->_state, 'set', 'filter.before', '2011-01-01');
		$actual = TestReflection::invoke($this->_instance, 'getList');
		$this->assertEquals(0, count($actual));

		TestReflection::invoke($this->_state, 'set', 'filter.before', '2011-01-02');
		$actual = TestReflection::invoke($this->_instance, 'getList');
		$this->assertEquals(1, count($actual));

		TestReflection::invoke($this->_state, 'set', 'filter.before', '2012-02-02');
		$actual = TestReflection::invoke($this->_instance, 'getList');
		$this->assertEquals(2, count($actual));

		TestReflection::invoke($this->_state, 'set', 'filter.before', null);
		TestReflection::invoke($this->_state, 'set', 'filter.since', '2011-01-01');
		$actual = TestReflection::invoke($this->_instance, 'getList');
		$this->assertEquals(2, count($actual));

		TestReflection::invoke($this->_state, 'set', 'filter.since', '2011-01-02');
		$actual = TestReflection::invoke($this->_instance, 'getList');
		$this->assertEquals(1, count($actual));

		TestReflection::invoke($this->_state, 'set', 'filter.since', '2012-02-02');
		$actual = TestReflection::invoke($this->_instance, 'getList');
		$this->assertEquals(0, count($actual));

		TestReflection::invoke($this->_state, 'set', 'content.type', '5');
		$actual = TestReflection::invoke($this->_instance, 'getList');
		$this->assertEquals(0, count($actual));
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

	/**
	 * Tests __construct()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceModelBase::__construct
	 * @since   1.0
	 */
	public function test__construct2()
	{
		JFactory::$database = 'factory db';
		JFactory::$application = $this->getMockWeb();

		$factory = new JContentFactory('TCPrefix', null, null, new JUser);

		// Construct the object.
		$model = new WebServiceModelBase($factory, null, null);

		// Verify that the values injected into the constructor are present.
		$this->assertEquals('factory db', TestReflection::getValue($model, 'db'));
	}
}
