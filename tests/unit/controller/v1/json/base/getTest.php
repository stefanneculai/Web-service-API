<?php

/**
 * @package     WebService.Tests
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once __DIR__ . '/../../../../application/stubs/webMock.php';

/**
 * Test Case class for WebServiceControllerV1JsonBaseGet
 *
 * @package     WebService.Tests
 * @subpackage  Application
 * @since       1.0
 */
class WebServiceControllerV1JsonBaseGetTest extends TestCase
{

	/**
	 * An instance of the class to test.
	 *
	 * @var    WebServiceControllerV1JsonBaseGet
	 * @since  1.0
	 */
	private $_instance;

	/**
	 * Tests __construct()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceControllerV1JsonBaseGetTest::__construct
	 * @since   1.0
	 */
	public function test__construct()
	{
		// Create the mock.
		$input = $this->getMock('JInput', array('test'), array(), '', false);
		$input->expects($this->any())
		->method('test')
		->will(
				$this->returnValue('ok')
		);

		// Construct the object.
		$controller = new WebServiceControllerV1JsonBaseGet('general', $input, $this->getMockWeb());

		// Verify that the values injected into the constructor are present.
		$this->assertEquals('ok', TestReflection::getValue($controller, 'input')->test());
	}

	/**
	 * Provides test data for request format detection.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetOrderData()
	{

		// Input, Expected, Exception
		return array(
				array('', null),
				array('field1', array('field1')),
				array('field1, field2', array('field1', 'field2')),
				array(null, null),
				array('foo, bar',null, true),
				array('wrong_field','wrong_field', true)
		);
	}

	/**
	 * Tests getOrder()
	 *
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonBaseGet::getOrder
	 * @dataProvider  seedGetOrderData
	 * @since         1.0
	 */
	public function testGetOrder($input,  $expected, $exception=false)
	{
		$fieldsMap = array(
			'id' => 'id',
			'created_at' => 'created_at',
			'user_id' => 'user_id',
			'field1' => 'field1',
			'field2' => 'field2',
			'field3' => 'field3',
			'field4' => 'field4',
			'field5' => 'field5'
		);

		// Set the input values.
		$_GET['order'] = $input;

		// Set fields map
		TestReflection::setValue($this->_instance, 'fieldsMap', $fieldsMap);

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'getOrder');

		// Clean up after ourselves.
		$_GET['order'] = null;

		if ($exception)
		{
			$app = TestReflection::getValue($this->_instance, 'app');
			$errors = TestReflection::invoke($app->errors, 'getErrors');
			$this->assertEquals(1, count($errors));
			return;
		}

		// Verify the value.
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Provides test data for request format detection.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetLimitData()
	{
		// Input, Expected, Exception
		return array(
				array('', null, true),
				array(null, 20, false),
				array('wrong', null, true),
				array(22, 22, false),
				array(150, 100, false),
				array(99, 99, false),
				array(-1, null, true),
				array(0, null, true)
		);
	}

	/**
	 * Tests getLimit()
	 *
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonBaseGet::getLimit
	 * @dataProvider  seedGetLimitData
	 * @since         1.0
	 */
	public function testGetLimit($input,  $expected, $exception)
	{
		// Set the input values.
		$_GET['limit'] = $input;

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'getLimit');

		// Clean up after ourselves.
		$_GET['limit'] = null;

		// If we are expecting an exception set it.
		if ($exception)
		{
			$app = TestReflection::getValue($this->_instance, 'app');
			$errors = TestReflection::invoke($app->errors, 'getErrors');
			$this->assertEquals(1, count($errors));
			return;
		}

		// Verify the value.
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Provides test data for request format detection.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetOffsetData()
	{
		// Input, Expected, Exception
		return array(
				array('', null, true),
				array(null, 0, false),
				array(10, 10, false),
				array(200, 200, false),
				array(-1, null, true),
				array(0, 0, false)
		);
	}

	/**
	 * Tests getOffset()
	 *
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonBaseGet::getOffset
	 * @dataProvider  seedGetOffsetData
	 * @since         1.0
	 */
	public function testGetOffset($input,  $expected, $exception)
	{
		// Set the input values.
		$_GET['offset'] = $input;

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'getOffset');

		// Clean up after ourselves.
		$_GET['offset'] = null;

		// If we are expecting an exception set it.
		if ($exception)
		{
			$app = TestReflection::getValue($this->_instance, 'app');
			$errors = TestReflection::invoke($app->errors, 'getErrors');
			$this->assertEquals(1, count($errors));
			return;
		}

		// Verify the value.
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Provides test data for request format detection.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetContentIdData()
	{
		// Input, Expected, Exception
		return array(
				array('', '*', false),
				array(null, '*', false),
				array('22', '22', false),
				array('-7', null, true),
				array('22/user', '22', false),
				array('bad/user', '22', true),
				array('-1/user', null, true),
		);
	}

	/**
	 * Tests getContentId()
	 *
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonBaseGet::getContentId
	 * @dataProvider  seedGetContentIdData
	 * @since         1.0
	 */
	public function testGetContentId($input,  $expected, $exception)
	{
		// Set the input values.
		$_GET['@route'] = $input;

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'getContentId');

		// Clean up after ourselves.
		$_GET['@route'] = null;

		// If we are expecting an exception set it.
		if ($exception)
		{
			$app = TestReflection::getValue($this->_instance, 'app');
			$errors = TestReflection::getValue($app->errors, 'errorsArray');
			$this->assertEquals(1, count($errors));
			return;
		}

		// Verify the value.
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Provides test data for request format detection.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetSinceData()
	{
		$date1 = new JDate('1970-01-01');
		$date1 = $date1->toSql();

		$date2 = new JDate('2001-01-01');
		$date2 = $date2->toSql();

		$date3 = new JDate('1999-03-03');
		$date3 = $date3->toSql();

		$date4 = new JDate('now');
		$date4 = $date4->toSql();

		// Input, Expected, Exception
		return array(
				array('', null, true, false),
				array('1970-01-01', $date1, false, false),
				array('-0001-01-01', null, true, false),
				array('2001-01-01', $date2, false, false),
				array(null, $date1 , false, false),
				array('99-03-03', $date3 , false, false),
				array('now', $date4, false, true)
		);
	}

	/**
	 * Tests getSince()
	 *
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 * @param   boolean  $over       True if an Returned date should be after the current date
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonBaseGet::getSince
	 * @dataProvider  seedGetSinceData
	 * @since         1.0
	 */
	public function testGetSince($input,  $expected, $exception, $over)
	{
		// Set the input values.
		$_GET['since'] = $input;

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'getSince');

		// Clean up after ourselves.
		$_GET['since'] = null;

		// If we are expecting an exception set it.
		if ($exception)
		{
			$app = TestReflection::getValue($this->_instance, 'app');
			$errors = TestReflection::getValue($app->errors, 'errorsArray');
			$this->assertEquals(1, count($errors));
			return;
		}

		// Verify the value.
		if ($over == false)
		{
			$this->assertEquals($expected, $actual);
		}
		else
		{
			$expected = new JDate($expected);
			$actual = new JDate($actual);

			$this->assertGreaterThanOrEqual($expected, $actual);
		}
	}

	/**
	 * Provides test data for request format detection.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetBeforeData()
	{
		$date1 = new JDate('1970-01-01');
		$date1 = $date1->toSql();

		$date2 = new JDate('2001-01-01');
		$date2 = $date2->toSql();

		$date3 = new JDate('1999-03-03');
		$date3 = $date3->toSql();

		$date4 = new JDate('now');
		$date4 = $date4->toSql();

		// Input, Expected, Exception
		return array(
				array('', null, true, false),
				array('1970-01-01', $date1, false, false),
				array('-0001-01-01', null, true, false),
				array('2001-01-01', $date2, false, false),
				array(null, $date4 , false, true),
				array('99-03-03', $date3 , false, false),
				array('now', $date4, false, true)
		);
	}

	/**
	 * Tests getBefore()
	 *
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 * @param   boolean  $over       True if an Returned date should be after the current date
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonBaseGet::getBefore
	 * @dataProvider  seedGetBeforeData
	 * @since         1.0
	 */
	public function testGetBefore($input,  $expected, $exception, $over)
	{
		// Set the input values.
		$_GET['before'] = $input;

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'getBefore');

		// Clean up after ourselves.
		$_GET['before'] = null;

		// If we are expecting an exception set it.
		if ($exception)
		{
			$app = TestReflection::getValue($this->_instance, 'app');
			$errors = TestReflection::getValue($app->errors, 'errorsArray');
			$this->assertEquals(1, count($errors));
			return;
		}

		// Verify the value.
		if ($over == false)
		{
			$this->assertEquals($expected, $actual);
		}
		else
		{
			$expected = new JDate($expected);
			$actual = new JDate($actual);

			$this->assertGreaterThanOrEqual($expected, $actual);
		}
	}

	/**
	 * Provides test data for request format detection.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetFieldsData()
	{
		$map = array(
				'name' => 'name',
				'surname' => 'surname',
				'content' => 'content',
				'created_at' => 'created_at',
				'user_id' => 'user_id'
				);

		// Input, Expected, Exception
		return array(
				array($map, null, array_keys($map), false),
				array($map, '', array_keys($map), false),
				array($map, 'name, surname', array('name','surname'), false),
				array($map, 'name, surname, foo', array('name','surname'), false),
				array($map, 'content, created_at, user_id', array('content','created_at', 'user_id'), false),
		);
	}

	/**
	 * Tests getFields()
	 *
	 * @param   array    $map        An associative array with the map for the fields
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonBaseGet::getFields
	 * @dataProvider  seedGetFieldsData
	 * @since         1.0
	 */
	public function testGetFields($map, $input, $expected, $exception)
	{
		TestReflection::setValue($this->_instance, 'fieldsMap', $map);

		// Set the input values.
		$_GET['fields'] = $input;

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'getFields');

		// Clean up after ourselves.
		$_GET['fields'] = null;

		// If we are expecting an exception set it.
		if ($exception)
		{
			$app = TestReflection::getValue($this->_instance, 'app');
			$errors = TestReflection::getValue($app->errors, 'errorsArray');
			$this->assertEquals(1, count($errors));
			return;
		}
		// Verify the value.
		$this->assertEquals($expected, $actual);
	}

	/** Test init
	 *
	 * @return void
	 *
	 * @covers        WebServiceControllerV1JsonBaseGet::init
	 * @since
	 */
	public function testInit()
	{
		$map = array(
				'created_at' => 'created_at',
				'user_id' => 'user_id',
				'f1' => 'field1',
				'f2' => 'field2'
				);

		TestReflection::setValue($this->_instance, 'fieldsMap', $map);

		$_GET['@route'] = '22';
		$_GET['fields'] = 'content, created_at, user_id';
		$_GET['before'] = '1970-01-01';
		$_GET['since'] = '1970-01-01';
		$_GET['offset'] = 10;
		$_GET['limit'] = 20;
		$_GET['order'] = 'f1, f2';

		TestReflection::invoke($this->_instance, 'init');

		// Test expected id
		$ai = TestReflection::getValue($this->_instance, 'id');
		$this->assertEquals('22', $ai);

		// Test expected fields
		$af = TestReflection::getValue($this->_instance, 'fields');
		$this->assertEquals(
				array_values(
						array(
							TestReflection::invoke($this->_instance, 'mapIn', 'created_at'),
							TestReflection::invoke($this->_instance, 'mapIn', 'user_id'),
						)
					),
				array_values($af)
				);

		// Test expected before
		$ab = TestReflection::getValue($this->_instance, 'before');
		$d = new JDate('1970-01-01');
		$this->assertEquals($d->toSql(), $ab);

		// Test expected since
		$as = TestReflection::getValue($this->_instance, 'since');
		$d = new JDate('1970-01-01');
		$this->assertEquals($d->toSql(), $as);

		// Test expected offset
		$ao = TestReflection::getValue($this->_instance, 'offset');
		$this->assertEquals(10, $ao);

		// Test expected limit
		$al = TestReflection::getValue($this->_instance, 'limit');
		$this->assertEquals(20, $al);

		// Test expected limit
		$ao = TestReflection::getValue($this->_instance, 'order');
		$this->assertEquals(array('field1', 'field2'), $ao);
	}

	/** Test execute with errors
	 *
	 * @return void
	 *
	 * @covers        WebServiceControllerV1JsonBaseGet::execute
	 * @since
	 */
	public function testExecuteWithErrors()
	{
		// Get app
		$app = TestReflection::getValue($this->_instance, 'app');

		// Set errors
		TestReflection::setValue($app->errors, 'errors', true);
		$errors = TestReflection::setValue($app->errors, 'errorsArray', array('foo'));

		TestReflection::invoke($this->_instance, 'execute');

		$actual = TestReflection::invoke($app, 'getBody');
		$expected = json_encode(array('foo'));

		$this->assertEquals($expected, $actual);
	}

	/**
	 * Provides test data for getAction
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetAction()
	{
		// Input, Expected, Exception
		return array(
				array(null),
				array('like')
		);
	}

	/**
	 * Tests getAction()
	 *
	 * @param   string  $data  Input to test
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonBaseGet::getAction
	 * @dataProvider  seedGetAction
	 * @since         1.0
	 */
	public function testGetAction($data)
	{
		// Set the input values.
		$_GET['action'] = $data;

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'getAction');

		// Clean up after ourselves.
		$_GET['action'] = null;

		// Verify the value.
		$this->assertEquals($data, $actual);
	}

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

		$options = array(
			'driver' => 'sqlite',
			'database' => ':memory:',
			'prefix' => 'ws_'
		);

		$driver = JDatabaseDriver::getInstance($options);

		$pdo = new PDO('sqlite::memory:');
		$pdo->exec(file_get_contents(JPATH_TESTS . '/unit/model/stubs/ws.sql')) or die(print_r($pdo->errorInfo()));

		TestReflection::setValue($driver, 'connection', $pdo);
		JFactory::$database = $driver;
		JFactory::$application = $this->getMockWeb();

		$testInput = new JInput;
		$testMock = WebServiceApplicationWebMock::create($this);

		$this->_instance = new WebServiceControllerV1JsonBaseGet('general', $testInput, $testMock);
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

		parent::tearDown();
	}

}
