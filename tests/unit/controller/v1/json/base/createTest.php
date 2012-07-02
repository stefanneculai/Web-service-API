<?php
/**
 * @package     WebService.Tests
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

require_once __DIR__ . '/../../../../application/stubs/webMock.php';

/**
 * Test Case class for WebServiceControllerV1JsonBaseCreate
*
* @package     WebService.Tests
* @subpackage  Application
* @since       1.0
*/
class WebServiceControllerV1JsonBaseCreateTest extends TestCase
{

	/**
	 * An instance of the class to test.
	 *
	 * @var    WebServiceControllerV1JsonBaseCreate
	 * @since  1.0
	 */
	private $_instance;

	/**
	 * Tests __construct()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceControllerV1JsonBaseCreateTest::__construct
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
		$controller = new WebServiceControllerV1JsonBaseCreate('general', $input, $this->getMockWeb());

		// Verify that the values injected into the constructor are present.
		$this->assertEquals('ok', TestReflection::getValue($controller, 'input')->test());
	}

	/** Test init
	 *
	 * @return void
	 *
	 * @covers        WebServiceControllerV1JsonBaseCreate::init
	 * @since
	 */
	public function testInit()
	{
		// Set mandatory fields
		$mandatory = array('f1' => '', 'f2' => '', 'f3' => '');
		TestReflection::setValue($this->_instance, 'mandatoryFields', $mandatory);
		foreach (array('f1' => 'test', 'f2' => 'test', 'f3' => 'test') as $key => $value)
		{
			$_GET[$key] = $value;
		}

		// Set optional fields
		$optional = array('f4' => '', 'f5' => '');
		TestReflection::setValue($this->_instance, 'optionalFields', $optional);
		foreach (array('f4' => 'test', 'f5' => 'test') as $key => $value)
		{
			$_GET[$key] = $value;
		}

		TestReflection::invoke($this->_instance, 'init');

		$am = TestReflection::getValue($this->_instance, 'mandatoryFields');
		$ao = TestReflection::getValue($this->_instance, 'optionalFields');

		// Clear
		TestReflection::setValue($this->_instance, 'mandatoryFields', array());
		TestReflection::setValue($this->_instance, 'optionalFields', array());

		foreach (array('f1' => 'test', 'f2' => 'test', 'f3' => 'test') as $key => $value)
		{
			$_GET[$key] = null;
		}

		foreach (array('f4' => 'test', 'f5' => 'test') as $key => $value)
		{
			$_GET[$key] = null;
		}

		// Result assert
		$this->assertEquals(
					array('f1' => 'test', 'f2' => 'test', 'f3' => 'test'),
					$am
				);

		$this->assertEquals(
					array('f4' => 'test', 'f5' => 'test'),
					$ao
				);
	}

	/** Test execute with errors
	 *
	 * @return void
	 *
	 * @covers        WebServiceControllerV1JsonBaseCreate::execute
	 * @since
	 */
	public function testExecute()
	{
		foreach (array('f1' => 'test', 'f2' => 'test', 'f3' => 'test') as $key => $value)
		{
			$_GET[$key] = $value;
		}

		// Get app
		$app = TestReflection::getValue($this->_instance, 'app');

		// Set errors
		TestReflection::setValue($app->errors, 'errors', true);
		$errors = TestReflection::setValue($app->errors, 'errorsArray', array('foo'));

		TestReflection::invoke($this->_instance, 'execute');

		$actual = TestReflection::invoke($app, 'getBody');
		$expected = json_encode(array('foo'));

		foreach (array('f1' => 'test', 'f2' => 'test', 'f3' => 'test') as $key => $value)
		{
			$_GET[$key] = null;
		}

		$this->assertEquals($expected, $actual);

	}

	/**
	 * Provides test data for request format detection.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetMandatoryFieldsData()
	{
		$mandatory = array('f1' => '', 'f2' => '', 'f3' => '');

		// Input, Expected, Exception
		return array(
				array($mandatory, array(), null, true, 3),
				array($mandatory, array('f1' => 'test'), null, true, 2),
				array($mandatory, array('f1' => 'test', 'f2' => 'test', 'f3' => null), null, true, 1),
				array($mandatory, array('f1' => 'test', 'f2' => 'test', 'f3' => 'test'),
						array('f1' => 'test', 'f2' => 'test', 'f3' => 'test'), false),
		);
	}

	/**
	 * Tests getMandatoryFields()
	 *
	 * @param   array    $mandatory  Associative array with the mandatory fields
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 * @param   integer  $en         Exception number
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonBaseCreate::getMandatoryFields
	 * @dataProvider  seedGetMandatoryFieldsData
	 * @since         1.0
	 */
	public function testGetMandatoryFields($mandatory, $input,  $expected, $exception, $en=0)
	{
		TestReflection::setValue($this->_instance, 'mandatoryFields', $mandatory);
		TestReflection::setValue($this->_instance, 'fieldsMap', array());

		foreach ($input as $key => $value)
		{
			$_GET[$key] = $value;
		}

		// Execute the code to test.
		TestReflection::invoke($this->_instance, 'getMandatoryFields');

		// Clean up after ourselves.
		foreach ($input as $key => $value)
		{
			$_GET[$key] = null;
		}

		// If we are expecting an exception set it.
		if ($exception)
		{
			$app = TestReflection::getValue($this->_instance, 'app');
			$errors = TestReflection::getValue($app->errors, 'errorsArray');
			$this->assertEquals($en, count($errors));
			return;
		}

		// Verify the value.
		$this->assertEquals($expected, TestReflection::getValue($this->_instance, 'mandatoryFields'));
	}

	/**
	 * Provides test data for request format detection.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetOptionalFieldsData()
	{
		$optional = array('f4' => '', 'f5' => '');

		// Input, Expected, Exception
		return array(
				array($optional, array(), array(), false),
				array($optional, array('f4' => 'test'), array('f4' => 'test'), false),
				array($optional, array('f4' => 'test', 'f5' => 'test'),
						array('f4' => 'test', 'f5' => 'test'), false),
		);
	}

	/**
	 * Tests getOptionalFields()
	 *
	 * @param   array    $optional   Associative array with the optional fields
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonBaseCreate::getOptionalFields
	 * @dataProvider  seedGetOptionalFieldsData
	 * @since         1.0
	 */
	public function testGetOptionalFields($optional, $input,  $expected, $exception)
	{
		TestReflection::setValue($this->_instance, 'optionalFields', $optional);
		TestReflection::setValue($this->_instance, 'fieldsMap', array());

		foreach ($input as $key => $value)
		{
			$_GET[$key] = $value;
		}

		// Execute the code to test.
		TestReflection::invoke($this->_instance, 'getOptionalFields');

		// Clean up after ourselves.
		foreach ($input as $key => $value)
		{
			$_GET[$key] = null;
		}

		// If we are expecting an exception set it.
		if ($exception)
		{
			$app = TestReflection::getValue($this->_instance, 'app');
			$errors = TestReflection::getValue($app->errors, 'errorsArray');
			$this->assertEquals(1, count($errors));
			return;
		}

		// Verify the value.
		$this->assertEquals($expected, TestReflection::getValue($this->_instance, 'optionalFields'));
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

		$type = 'general';
		$testInput = new JInput;
		$testMock = WebServiceApplicationWebMock::create($this);
		$this->_instance = new WebServiceControllerV1JsonBaseCreate($type, $testInput, $testMock);
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
