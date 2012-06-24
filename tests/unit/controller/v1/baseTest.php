<?php
/**
 * @package     WebService.Tests
* @subpackage  Application
*
* @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE
*/

require_once __DIR__ . '/../../application/stubs/webMock.php';

/**
 * Test Case class for WebServiceControllerV1Base
*
* @package     WebService.Tests
* @subpackage  Application
* @since       1.0
*/
class WebServiceControllerV1BaseTest extends TestCase
{

	/**
	 * An instance of the class to test.
	 *
	 * @var    WebServiceControllerV1Base
	 * @since  1.0
	 */
	private $_instance;

	/**
	 * Tests __construct()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceControllerV1Base::__construct
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
		$controller = $this->getMockForAbstractClass('WebServiceControllerV1Base', array('foo', $input, $this->getMockWeb()));

		// Verify that the values injected into the constructor are present.
		$this->assertEquals('ok', TestReflection::getValue($controller, 'input')->test());
	}

	/**
	 * Provides test data for getArrayFields
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedGetArrayFields()
	{
		// Input, Expected
		return array(
				array('', array()),
				array('foo', array('foo' => '')),
				array('foo,   gaga', array('foo' => '', 'gaga' => '')),
		);
	}

	/**
	 * Tests getArrayFields()
	 *
	 * @param   string  $input     Input string to test.
	 * @param   array   $expected  Expected fetched array.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1Base::getArrayFields
	 * @dataProvider  seedGetArrayFields
	 * @since         1.0
	 */
	public function testGetContentId($input,  $expected)
	{
		$actual = TestReflection::invoke($this->_instance, 'getArrayFields', $input);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * Provides test data for mapIn
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedMapIn()
	{
		$fieldsMap = array('foo' => 'bar');

		// Input, Expected
		return array(
				array($fieldsMap, 'foo', 'bar'),
				array($fieldsMap, 'test', 'test')
		);
	}

	/**
	 * Tests mapIn()
	 *
	 * @param   array   $fieldsMap  The fields map
	 * @param   string  $input      Input string to test.
	 * @param   array   $expected   Expected fetched array.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1Base::mapIn
	 * @dataProvider  seedMapIn
	 * @since         1.0
	 */
	public function testMapIn($fieldsMap, $input,  $expected)
	{
		$fb = TestReflection::getValue($this->_instance, 'fieldsMap');
		TestReflection::setValue($this->_instance, 'fieldsMap', $fieldsMap);

		$actual = TestReflection::invoke($this->_instance, 'mapIn', $input);

		TestReflection::setValue($this->_instance, 'fieldsMap', $fb);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * Provides test data for mapOut
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedMapOut()
	{
		$fieldsMap = array('foo' => 'bar');

		// Input, Expected
		return array(
				array($fieldsMap, 'bar', 'foo'),
				array($fieldsMap, 'test', 'test')
		);
	}

	/**
	 * Tests mapOut()
	 *
	 * @param   array   $fieldsMap  The fields map
	 * @param   string  $input      Input string to test.
	 * @param   array   $expected   Expected fetched array.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceControllerV1Base::mapOut
	 * @dataProvider  seedMapOut
	 * @since         1.0
	 */
	public function testMapOut($fieldsMap, $input,  $expected)
	{
		$fb = TestReflection::getValue($this->_instance, 'fieldsMap');
		TestReflection::setValue($this->_instance, 'fieldsMap', $fieldsMap);

		$actual = TestReflection::invoke($this->_instance, 'mapOut', $input);

		TestReflection::setValue($this->_instance, 'fieldsMap', $fb);

		$this->assertEquals($expected, $actual);
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
		$testMock = WebServiceApplicationWebMock::create($this);

		$this->_instance = $this->getMockForAbstractClass('WebServiceControllerV1Base', array('foo', $testInput, $testMock));
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
