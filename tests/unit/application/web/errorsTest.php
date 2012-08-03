<?php

/**
 * @package     WebService.Tests
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test Case class for WebServiceApplicationWebErrors
 *
 * @package     WebService.Tests
 * @subpackage  Application
 * @since       1.0
 */
class WebServiceApplicationWebErrorsTest extends TestCase
{
	/**
	 * An instance of the class to test.
	 *
	 * @var    WebServiceApplicationWebErrors
	 * @since  1.0
	 */
	private $_instance;

/**
	 * Tests __construct()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceApplicationWebErrors::__construct
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

		$webMock = MockWebServiceApplicationWeb::create($this);

		// Construct the object.
		$errorsClass = new WebServiceApplicationWebErrors($webMock, $input);

		// Verify that the values injected into the constructor are present.
		$this->assertEquals('ok', TestReflection::getValue($errorsClass, 'input')->test());
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
		$testMock = MockWebServiceApplicationWeb::create($this);
		$this->_instance = new WebServiceApplicationWebErrors($testMock, $testInput);
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
				array('', 400, true),
				array(null, 400, false),
				array('true', 200, false),
				array('false', 400, false),
				array('error', 400, true),
				array('TRUE', 200, false),
				array('FALSE', 400, false)
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
	 * @covers        WebServiceApplicationWebErrors::checkSupressResponseCodes
	 * @dataProvider  seedCheckSupressResponseCodesData
	 * @since         1.0
	 */
	public function testCheckSupressResponseCodes($input,  $expected, $exception)
	{
		// Set the input values.
		$_GET['suppress_response_codes'] = $input;

		// Execute the code to test.
		TestReflection::invoke($this->_instance, 'checkSupressResponseCodes');

		// Clean up after ourselves.
		$_GET['suppress_response_codes'] = null;

		if ($exception)
		{
			$errors = TestReflection::invoke($this->_instance, 'getErrors');
			$this->assertEquals(1, count($errors));
			return;
		}

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
	public function seedAddErrorData()
	{
		$errorsMap = json_decode("{
					\"1001\":
						{
							\"code\": \"1001\",
							\"message\": \"Message for code 1001\",
							\"more_info\": \"More info fore code 1001\",
							\"response_code\": \"400\"
						},
					\"1002\":
						{
							\"code\": \"1001\",
							\"message\": \"Message for code 1001 $1\",
							\"more_info\": \"More info fore code 1001\",
							\"response_code\": \"400\"
						}
					}");

		$error['code'] = "foo";
		$error['message'] = 'This error is not known';
		$error['more_info'] = 'A link where to find more info about an unknown error';
		$error['response_code'] = '400';

		// Input, Expected, Exception
		return array(
				array("1001", $errorsMap, get_object_vars($errorsMap->{"1001"})),
				array("foo", $errorsMap, $error)
		);
	}

	/**
	 * Tests checkAddError()
	 *
	 * @param   string  $input     Input string to test.
	 * @param   array   $errorMap  The errors map
	 * @param   array   $expected  Expected fetched string.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebErrors::addError
	 * @dataProvider  seedAddErrorData
	 * @since         1.0
	 */
	public function testAddError($input,  $errorMap, $expected)
	{
		TestReflection::setValue($this->_instance, 'errorsMap', $errorMap);

		// Execute the code to test.
		TestReflection::invoke($this->_instance, 'addError', $input);
		$actual = array_pop(TestReflection::getValue($this->_instance, 'errorsArray'));

		// Verify the value.
		$this->assertEquals($expected, $actual);
		$this->assertEquals(true, TestReflection::getValue($this->_instance, 'errors'));
		$this->assertEquals(400, TestReflection::getValue($this->_instance, 'responseCode'));
	}

	/**
	 * Tests checkAddError()
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebErrors::addError
	 * @covers        WebServiceApplicationWebErrors::customMessage
	 *
	 * @since         1.0
	 */
	public function testAddCustomError()
	{
		$errorsMap = json_decode("{
					\"1001\":
						{
							\"code\": \"1001\",
							\"message\": \"Message for code 1001\",
							\"more_info\": \"More info fore code 1001\",
							\"response_code\": \"400\"
						},

					\"1002\":
						{
							\"code\": \"1001\",
							\"message\": \"Message for code 1001 $1\",
							\"more_info\": \"More info fore code 1001\",
							\"response_code\": \"400\"
						}
					}");

		TestReflection::setValue($this->_instance, 'errorsMap', $errorsMap);

		// Execute the code to test.
		TestReflection::invoke($this->_instance, 'addError', "1002", array("foo"));
		$actual = array_pop(TestReflection::getValue($this->_instance, 'errorsArray'));

		// Verify the value.
		$this->assertEquals("Message for code 1001 foo", $actual['message']);
		$this->assertEquals(true, TestReflection::getValue($this->_instance, 'errors'));
		$this->assertEquals(400, TestReflection::getValue($this->_instance, 'responseCode'));
	}

	/**
	 * Tests unknownError()
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebErrors::unknownError
	 * @since         1.0
	 */
	public function testUnknownError()
	{
		$expected['code'] = "foo";
		$expected['message'] = 'This error is not known';
		$expected['more_info'] = 'A link where to find more info about an unknown error';
		$expected['response_code'] = '400';

		$actual = TestReflection::invoke($this->_instance, 'unknownError', $expected['code']);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * Tests getErrors()
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebErrors::getErrors
	 * @since         1.0
	 */
	public function testGetErrors()
	{
		$expected = array('foo', 'bar');
		TestReflection::setValue($this->_instance, 'errorsArray', $expected);

		$actual = TestReflection::invoke($this->_instance, 'getErrors');

		$this->assertEquals($expected, $actual);
	}

	/**
	 * Tests errorsExist()
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebErrors::errorsExist
	 * @since         1.0
	 */
	public function testErrorsExist()
	{
		TestReflection::setValue($this->_instance, 'errors', false);

		$actual = TestReflection::invoke($this->_instance, 'errorsExist');

		$this->assertEquals(false, $actual);
	}

	/**
	 * Tests getResponseCode()
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebErrors::getResponseCode
	 * @since         1.0
	 */
	public function testGetResponseCode()
	{
		TestReflection::setValue($this->_instance, 'responseCode', 100);

		$actual = TestReflection::invoke($this->_instance, 'getResponseCode');

		$this->assertEquals(100, $actual);
	}

	/**
	 * Tests fetchErrorsData()
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebErrors::fetchErrorsData
	 * @since         1.0
	 */
	public function testFetchErrorsData()
	{
		$actual = TestReflection::invoke($this->_instance, 'fetchErrorsData');

		$this->assertEquals(true, property_exists($actual, '301'));
		$this->assertEquals(true, property_exists($actual, '302'));
		$this->assertEquals(2, count(get_object_vars($actual)));
	}
}
