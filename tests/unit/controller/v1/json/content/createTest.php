<?php
/**
 * @package     WebService.Tests
* @subpackage  Application
*
* @copyright   Copyright (C) {COPYRIGHT}. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE
*/

/**
 * Test Case class for WebServiceControllerV1JsonContentCreate
*
* @package     WebService.Tests
* @subpackage  Application
* @since       1.0
*/
class WebServiceControllerV1JsonContentCreateTest extends TestCase
{

	/**
	 * An instance of the class to test.
	 *
	 * @var    WebServiceControllerV1JsonContentCreate
	 * @since  1.0
	 */
	private $_instance;
	
	/**
	 * Tests __construct()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceControllerV1JsonContentCreateTest::__construct
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
		$controller = new WebServiceControllerV1JsonContentCreate($input, $this->getMockWeb());
	
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
	public function seedCheckSupressResponseCodesData()
	{
		// Input, Expected, Exception
		return array(
				array('', NULL, true),
				array(NULL, 401, false),
				array('true', 200, false),
				array('false', 401, false),
				array('error', NULL, true),
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
	 * @covers        WebServiceControllerV1JsonContentCreate::checkSupressResponseCodes
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
	public function seedGetMandatoryDataData()
	{
		// Input, Expected, Exception
		return array(
				array(array(), NULL, true),
				array(array('field1'=>'test'), NULL, true),
				array(array('field1'=>'test', 'field2'=>'test', 'field3'=> null), NULL, true),
				array(array('field1'=>'test', 'field2'=>'test', 'field3'=> 'test'), 
						array('field1'=>'test', 'field2'=>'test', 'field3'=> 'test'), false),
		);
	}
	
	/**
	 * Tests getMandatoryData()
	 *
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonContentCreate::getMandatoryData
	 * @dataProvider  seedGetMandatoryDataData
	 * @since         1.0
	 */
	public function testGetMandatoryData($input,  $expected, $exception)
	{
		foreach($input as $key => $value){
			$_GET[$key] = $value;
		}
	
		// If we are expecting an exception set it.
		if ($exception)
		{
			$this->setExpectedException('InvalidArgumentException');
		}
	
		// Execute the code to test.
		TestReflection::invoke($this->_instance, 'getMandatoryData');
	
		// Clean up after ourselves.
		foreach($input as $key => $value){
			$_GET[$key] = null;
		}
	
		// Verify the value.
		$this->assertEquals($expected, TestReflection::getValue($this->_instance, 'mandatoryData'));
	}
	
	/**
	 * Provides test data for request format detection.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetOptionalDataData()
	{
		// Input, Expected, Exception
		return array(
				array(array(), array('field4'=>'', 'field5'=>''), false),
				array(array('field4'=>'test'), array('field4'=>'test', 'field5'=>''), false),
				array(array('field4'=>'test', 'field5'=>'test'), 
						array('field4'=>'test', 'field5'=>'test'), false),
				array(array('field4'=>'test', 'field5'=>'test'),
						array('field4'=>'test', 'field5'=>'test'), false)
		);
	}
	
	/**
	 * Tests getOptionalData()
	 *
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1JsonContentCreate::getOptionalData
	 * @dataProvider  seedGetOptionalDataData
	 * @since         1.0
	 */
	public function testGetOptionalData($input,  $expected, $exception)
	{
		foreach($input as $key => $value){
			$_GET[$key] = $value;
		}
	
		// If we are expecting an exception set it.
		if ($exception)
		{
			$this->setExpectedException('InvalidArgumentException');
		}
	
		// Execute the code to test.
		TestReflection::invoke($this->_instance, 'getOptionalData');
	
		// Clean up after ourselves.
		foreach($input as $key => $value){
			$_GET[$key] = null;
		}
	
		// Verify the value.
		$this->assertEquals($expected, TestReflection::getValue($this->_instance, 'optionalData'));
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
	
		$this->_instance = new WebServiceControllerV1JsonContentCreate(new JInput(array()), $this->getMockWeb());
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