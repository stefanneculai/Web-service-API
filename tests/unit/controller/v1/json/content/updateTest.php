<?php
/**
 * @package     WebService.Tests
* @subpackage  Application
*
* @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE
*/

/**
 * Test Case class for WebServiceControllerV1JsonContentUpdate
*
* @package     WebService.Tests
* @subpackage  Application
* @since       1.0
*/
class WebServiceControllerV1JsonContentUpdateTest extends TestCase
{

	/**
	 * An instance of the class to test.
	 *
	 * @var    WebServiceControllerV1JsonContentUpdate
	 * @since  1.0
	 */
	private $_instance;

	/**
	 * Tests __construct()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceControllerV1JsonContentUpdateTest::__construct
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
		$controller = new WebServiceControllerV1JsonContentUpdate($input, $this->getMockWeb());

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
				array('', '*', true),
				array(null, '*', true),
				array('22', '22', false),
				array('-7', null, true),
				array('22/user', '22', false),
				array('bad/user', '22', true),
				array('-1/user', null, true),
				array('22.json', '22', false),
				array('-1.xml', null, true),
				array('-1.json', null, true),
				array('22/user.json', '22', false)
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
	 * @covers        WebServiceControllerV1JsonContentUpdate::getContentId
	 * @dataProvider  seedGetContentIdData
	 * @since         1.0
	 */
	public function testGetContentId($input,  $expected, $exception)
	{
		// Set the input values.
		$_GET['@route'] = $input;

		// If we are expecting an exception set it.
		if ($exception)
		{
			$this->setExpectedException('InvalidArgumentException');
		}

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'getContentId');

		// Clean up after ourselves.
		$_GET['@route'] = null;

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
	public function seedCheckSupressResponseCodesData()
	{
		// Input, Expected, Exception
		return array(
				array('', null, true),
				array(null, 401, false),
				array('true', 200, false),
				array('false', 401, false),
				array('error', null, true),
				array('TRUE', 200, false),
				array('FALSE', 401, false)
		);
	}

	/**
	 * Tests checkSupressResponseCodes()
	 *
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonContentUpdate::checkSupressResponseCodes
	 * @dataProvider  seedCheckSupressResponseCodesData
	 * @since         1.0
	 */
	public function testCheckSupressResponseCodes($input,  $expected, $exception)
	{
		// Set the input values.
		$_GET['suppress_response_codes'] = $input;

		// If we are expecting an exception set it.
		if ($exception)
		{
			$this->setExpectedException('InvalidArgumentException');
		}

		// Execute the code to test.
		TestReflection::invoke($this->_instance, 'checkSupressResponseCodes');

		// Clean up after ourselves.
		$_GET['suppress_response_codes'] = null;

		// Verify the value.
		$this->assertEquals($expected, TestReflection::getValue($this->_instance, 'responseCode'));
	}

/**
	 * Provides test data for request format detection.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetDataFieldsData()
	{
		// Input, Expected, Exception
		return array(
				array(
						array(),
						array('field1' => null, 'field2' => null, 'field3' => null, 'field4' => null, 'field5' => null,),
						false
					),
				array(
						array('field1' => null),
						array('field1' => null, 'field2' => null, 'field3' => null, 'field4' => null, 'field5' => null,),
						false
					),
				array(
						array('field1' => 'test', 'field2' => 'test2'),
						array('field1' => 'test', 'field2' => 'test2', 'field3' => null, 'field4' => null, 'field5' => null,),
						false
					),
				array(
						array('field1' => 'test', 'field2' => 'test2'),
						array('field1' => 'test', 'field2' => 'test2', 'field3' => null, 'field4' => null, 'field5' => null,),
						false
					)
		);
	}

	/**
	 * Tests getDataFields()
	 *
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonContentUpdate::getDataFields
	 * @dataProvider  seedGetDataFieldsData
	 * @since         1.0
	 */
	public function testGetDataFields($input,  $expected, $exception)
	{
		foreach ($input as $key => $value)
		{
			$_GET[$key] = $value;
		}

		// If we are expecting an exception set it.
		if ($exception)
		{
			$this->setExpectedException('InvalidArgumentException');
		}

		// Execute the code to test.
		TestReflection::invoke($this->_instance, 'getDataFields');

		// Clean up after ourselves.
		foreach ($input as $key => $value)
		{
			$_GET[$key] = null;
		}

		// Verify the value.
		$this->assertEquals($expected, TestReflection::getValue($this->_instance, 'dataFields'));
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

		$testInput = new JInput;
		$testMock = $this->getMockWeb();
		$this->_instance = new WebServiceControllerV1JsonContentUpdate($testInput, $testMock);
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
