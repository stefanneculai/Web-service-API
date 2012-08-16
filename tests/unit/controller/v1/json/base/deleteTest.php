<?php
/**
 * @package     WebService.Tests
* @subpackage  Application
*
* @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE
*/

/**
 * Test Case class for WebServiceControllerV1JsonBaseDelete
*
* @package     WebService.Tests
* @subpackage  Application
* @since       1.0
*/
class WebServiceControllerV1JsonBaseDeleteTest extends TestCase
{

	/**
	 * An instance of the class to test.
	 *
	 * @var    WebServiceControllerV1JsonBaseDelete
	 * @since  1.0
	 */
	private $_instance;

	/**
	 * Tests __construct()
	 *
	 * @return  void
	 *
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
		$controller = new WebServiceControllerV1JsonBaseDelete('general', $input, $this->getMockWeb());

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
	public function seedGetContentIdData()
	{
		// Input, Expected, Exception
		return array(
				array(22, 22, false),
				array('bad', null, true),
				array(null, '*', false)
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
	 * @dataProvider  seedGetContentIdData
	 * @since         1.0
	 */
	public function testGetContentId($input,  $expected, $exception)
	{
		// Set the input values.
		$_GET['content_id'] = $input;

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'getContentId');

		// Clean up after ourselves.
		$_GET['content_id'] = null;

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
			$this->assertGreaterThanOrEqual(new JDate($expected), new JDate($actual));
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

	/** Test execute with errors
	 *
	 * @return void
	 *
	 * @since
	 */
	public function testExecute()
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

	/** Test init
	 *
	 * @return void
	 *
	 * @since
	 */
	public function testInit()
	{
		$_GET['content_id'] = '22';
		$_GET['before'] = '1970-01-01';
		$_GET['since'] = '1970-01-01';

		TestReflection::invoke($this->_instance, 'init');

		// Test expected id
		$ai = TestReflection::getValue($this->_instance, 'id');
		$this->assertEquals('22', $ai);

		// Test expected before
		$ab = TestReflection::getValue($this->_instance, 'before');
		$d = new JDate('1970-01-01');
		$this->assertEquals($d->toSql(), $ab);

		// Test expected since
		$as = TestReflection::getValue($this->_instance, 'since');
		$d = new JDate('1970-01-01');
		$this->assertEquals($d->toSql(), $as);
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
		$pdo->exec(file_get_contents(JPATH_TESTS . '/schema/ws.sql')) or die(print_r($pdo->errorInfo()));

		TestReflection::setValue($driver, 'connection', $pdo);
		JFactory::$database = $driver;
		JFactory::$application = $this->getMockWeb();

		$testInput = new JInput;
		$testMock = MockWebServiceApplicationWeb::create($this);
		$this->_instance = new WebServiceControllerV1JsonBaseDelete('general', $testInput, $testMock);
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
