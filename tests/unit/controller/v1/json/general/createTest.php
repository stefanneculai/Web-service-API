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
 * Test Case class for WebServiceControllerV1JsonGeneralCreate
*
* @package     WebService.Tests
* @subpackage  Application
* @since       1.0
*/
class WebServiceControllerV1JsonGeneralCreateTest extends TestCase
{

	/**
	 * An instance of the class to test.
	 *
	 * @var    WebServiceControllerV1JsonGeneralCreate
	 * @since  1.0
	 */
	private $_instance;

	/**
	 * Tests __construct()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceControllerV1JsonGeneralCreateTest::__construct
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
		$controller = new WebServiceControllerV1JsonGeneralCreate('general', $input, $this->getMockWeb());

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
	public function seedGetMandatoryFieldsData()
	{
		$mandatory = array('field1' => '', 'field2' => '', 'field3' => '');

		// Input, Expected, Exception
		return array(
				array($mandatory, array(), null, true, 3),
				array($mandatory, array('field1' => 'test'), null, true, 2),
				array($mandatory, array('field1' => 'test', 'field2' => 'test', 'field3' => null), null, true, 1),
				array($mandatory, array('field1' => 'test', 'field2' => 'test', 'field3' => 'test'),
						array('field1' => 'test', 'field2' => 'test', 'field3' => 'test'), false),
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
	 * @covers        WebServiceControllerV1JsonGeneralCreate::getMandatoryFields
	 * @dataProvider  seedGetMandatoryFieldsData
	 * @since         1.0
	 */
	public function testGetMandatoryFields($mandatory, $input,  $expected, $exception, $en=0)
	{
		TestReflection::setValue($this->_instance, 'mandatoryFields', $mandatory);

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
		$optional = array('field4' => '', 'field5' => '');

		// Input, Expected, Exception
		return array(
				array($optional, array(), array('field4' => '', 'field5' => ''), false),
				array($optional, array('field4' => 'test'), array('field4' => 'test', 'field5' => ''), false),
				array($optional, array('field4' => 'test', 'field5' => 'test'),
						array('field4' => 'test', 'field5' => 'test'), false),
				array($optional, array('field4' => 'test', 'field5' => 'test'),
						array('field4' => 'test', 'field5' => 'test'), false)
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
	 * @covers        WebServiceControllerV1JsonGeneralCreate::getOptionalFields
	 * @dataProvider  seedGetOptionalFieldsData
	 * @since         1.0
	 */
	public function testGetOptionalFields($optional, $input,  $expected, $exception)
	{
		TestReflection::setValue($this->_instance, 'optionalFields', $optional);

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

		$type = 'general';
		$testInput = new JInput;
		$testMock = WebServiceApplicationWebMock::create($this);
		$this->_instance = new WebServiceControllerV1JsonGeneralCreate($type, $testInput, $testMock);
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
