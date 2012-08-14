<?php
/**
 * @package     WebService.Tests
* @subpackage  Application
*
* @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE
*/

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
	 * Provides test data for mapFieldsIn
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedMapFieldsIn()
	{
		$fieldsMap = array('foo' => 'bar');

		// Input, Expected
		return array(
				array($fieldsMap, array('foo', 'test'), array('bar', 'test'))
		);
	}

	/**
	 * Tests mapFieldsIn()
	 *
	 * @param   array   $fieldsMap  The fields map
	 * @param   string  $input      Input string to test.
	 * @param   array   $expected   Expected fetched array.
	 *
	 * @return  void
	 *
	 * @dataProvider  seedMapFieldsIn
	 * @since         1.0
	 */
	public function testMapFieldsIn($fieldsMap, $input,  $expected)
	{
		$fb = TestReflection::getValue($this->_instance, 'fieldsMap');
		TestReflection::setValue($this->_instance, 'fieldsMap', $fieldsMap);

		$actual = TestReflection::invoke($this->_instance, 'mapFieldsIn', $input);

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
	 * Provides test data for mapOut
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedMapFieldsOut()
	{
		$fieldsMap = array('foo' => 'bar');

		// Input, Expected
		return array(
				array($fieldsMap, array('bar' => 'value', 'test' => 'value'), array('foo' => 'value', 'test' => 'value'))
		);
	}

	/**
	 * Tests mapFieldsOut()
	 *
	 * @param   array   $fieldsMap  The fields map
	 * @param   string  $input      Input string to test.
	 * @param   array   $expected   Expected fetched array.
	 *
	 * @return  void
	 *
	 * @dataProvider  seedMapFieldsOut
	 * @since         1.0
	 */
	public function testMapFieldsOut($fieldsMap, $input,  $expected)
	{
		$fb = TestReflection::getValue($this->_instance, 'fieldsMap');
		TestReflection::setValue($this->_instance, 'fieldsMap', $fieldsMap);

		$actual = TestReflection::invoke($this->_instance, 'mapFieldsOut', $input);

		TestReflection::setValue($this->_instance, 'fieldsMap', $fb);

		$this->assertEquals($expected, $actual);
	}

	/**
	 * Test readFields
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testReadFields()
	{
		TestReflection::setValue($this->_instance, 'type', 'test');
		TestReflection::invoke($this->_instance, 'readFields');

		$mandatoryActual = TestReflection::getValue($this->_instance, 'mandatoryFields');
		$this->assertEquals(true, isset($mandatoryActual['mandatory']));

		$optionalActual = TestReflection::getValue($this->_instance, 'optionalFields');
		$this->assertEquals(true, isset($optionalActual['optional']));

		$actionsActual = TestReflection::getValue($this->_instance, 'availableActions');
		$this->assertEquals(true, in_array('action1', $actionsActual));
		$this->assertEquals(true, in_array('action2', $actionsActual));

		$alternativeActual = TestReflection::getValue($this->_instance, 'alternativeFields');
		$this->assertEquals(true, isset($alternativeActual['alternative']));
		$this->assertEquals('alt', $alternativeActual['alternative']);

		$mapActual = TestReflection::getValue($this->_instance, 'fieldsMap');
		$this->assertEquals(true, isset($mapActual['map']));
		$this->assertEquals('map_test', $mapActual['map']);
	}

	/**
	 * Test readFields
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testReadFieldsEmpty()
	{
		TestReflection::setValue($this->_instance, 'type', 'test_empty');
		TestReflection::invoke($this->_instance, 'readFields');

		$mandatoryActual = TestReflection::getValue($this->_instance, 'mandatoryFields');
		$this->assertEquals(array(), $mandatoryActual);

		$optionalActual = TestReflection::getValue($this->_instance, 'optionalFields');
		$this->assertEquals(array(), $optionalActual);

		$actionsActual = TestReflection::getValue($this->_instance, 'availableActions');
		$this->assertEquals(array(), $actionsActual);

		$alternativeActual = TestReflection::getValue($this->_instance, 'alternativeFields');
		$this->assertEquals(array(), $alternativeActual);

		$mapActual = TestReflection::getValue($this->_instance, 'fieldsMap');
		$this->assertEquals(array(), $mapActual);
	}

	/**
	 * Test setWhere
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetWhere()
	{
		TestReflection::invoke($this->_instance, 'setWhere', array('foo' => 'bar'));
		$model = TestReflection::getValue($this->_instance, 'model');

		$modelState = $model->getState();
		$this->assertEquals('foo', $modelState->get('where.fields'));
		$this->assertEquals('bar', $modelState->get('where.foo'));
	}

	/**
	 * Test setWhere
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testItemExists()
	{
		$model = $this->getMock('WebServiceModelBase', array('existsItem'));
		$model->expects($this->any())
				->method('existsItem')
				->will($this->returnValue('ok'));
		TestReflection::setValue($this->_instance, 'model', $model);

		$actual = TestReflection::invoke($this->_instance, 'itemExists', 'foo', 'id');
		$this->assertEquals('ok', $actual);
	}

	/**
	 * Test getUserId() with no user passed
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetUserIdNull()
	{
		// Test no user id
		$_GET['user_id'] = null;

		$uid = TestReflection::invoke($this->_instance, 'getUserId');
		$this->assertEquals(null, $uid);
	}

	/**
	 * Test getUserId with valid and invalid user passed
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetUserId()
	{
		$_GET['user_id'] = 'ok';

		// Set user mock
		$user = $this->getMock('JUser', array('load'));
		$user->expects($this->at(0))
			->method('load')
			->will($this->returnValue(true));
		$user->expects($this->at(1))
			->method('load')
			->will($this->returnValue(false));
		TestReflection::setValue($this->_instance, 'user', $user);

		// Test user ID exists
		$actual = TestReflection::invoke($this->_instance, 'getUserId');
		$this->assertEquals('ok', $actual);

		// Test user ID exists
		$actual = TestReflection::invoke($this->_instance, 'getUserId');
		$app = TestReflection::getValue($this->_instance, 'app');
		$errors = TestReflection::getValue($app->errors, 'errorsArray');
		$this->assertEquals(1, count($errors));
	}

	/**
	 * Test checkUserId with null
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCheckUserIdNull()
	{
		// Test no user id
		$_GET['user_id'] = null;

		$uid = TestReflection::invoke($this->_instance, 'checkUserId');
		$this->assertEquals(false, $uid);
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
