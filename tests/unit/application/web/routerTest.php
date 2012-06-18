<?php
/**
 * @package     WebService.Tests
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test Case class for WebServiceApplicationWebRouter
 *
 * @package     WebService.Tests
 * @subpackage  Application
 * @since       1.0
 */
class WebServiceApplicationWebRouterTest extends TestCase
{
	/**
	 * An instance of the class to test.
	 *
	 * @var    WebServiceApplicationWebRouter
	 * @since  1.0
	 */
	private $_instance;

	/**
	 * Provides test data for reordering route
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedReorderRouteData()
	{
		// Input, Expected
		return array(
			array('/collection1/1', '/collection1/1', false, null, null),
			array('collection', 'collection', false, null, null),
			array('collection', 'collection', false, null, null),
			array('collection1/2/collection2', 'collection2', true, "collection1", "2"),
			array('collection1/2/collection2?fields', 'collection2?fields', true, "collection1", "2"),
		);
	}

	/**
	 * Tests reorderRoute()
	 *
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $testInput  Test input or not
	 * @param   string   $key        Input key
	 * @param   string   $value      Input value
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebRouter::reorderRoute
	 * @dataProvider  seedReorderRouteData
	 * @since         1.0
	 */
	public function testReorderRoute($input,  $expected, $testInput, $key, $value)
	{
		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'reorderRoute', $input);

		// Verify the value.
		$this->assertEquals($expected, $actual);

		// Test input value
		if ($testInput == true)
		{
			$input = TestReflection::getValue($this->_instance, 'input');
			$this->assertEquals($input->get->getString($key), $value);
		}
	}

	/**
	 * Provides test data for removing controller from route
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedRemoveControllerFromRouteData()
	{
		// Input, Expected
		return array(
			array('content', ''),
			array('content/22', '22'),
			array('', ''),
		);
	}

	/**
	 * Tests removeControllerFromRoute()
	 *
	 * @param   string  $input     Input string to test.
	 * @param   string  $expected  Expected fetched string.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebRouter::removeControllerFromRoute
	 * @dataProvider  seedRemoveControllerFromRouteData
	 * @since         1.0
	 */
	public function testRemoveControllerFromRoute($input,  $expected)
	{
		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'removeControllerFromRoute', $input);

		// Verify the value.
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Provides test data for rewriting the route
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedRewriteRouteData()
	{
		// Input, Expected
		return array(
			array('collection', 'collection', 'v1', 'json'),
			array('collection/22?fields', 'collection/22', 'v1', 'json'),
			array('collection.xml', 'collection', 'v1', 'xml'),
			array('v1/collection.json', 'collection', 'v1', 'json'),
			array('v2/collection/13.json?fields', 'collection/13', 'v2', 'json'),
			array('/v2/collection/13.json/?fields', 'collection/13', 'v2', 'json'),
		);
	}

	/**
	 * Tests rewriteRoute()
	 *
	 * @param   string  $input            Input string to test.
	 * @param   string  $expected         Expected fetched string.
	 * @param   string  $expectedVersion  Expected api version
	 * @param   string  $expectedType     Expected request type
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebRouter::rewriteRoute
	 * @dataProvider  seedRewriteRouteData
	 * @since         1.0
	 */
	public function testRewriteRoute($input,  $expected, $expectedVersion, $expectedType)
	{
		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'rewriteRoute', $input);

		// Verify the value.
		$this->assertEquals($expected, $actual);

		// Get private values
		$apiVersion = TestReflection::getValue($this->_instance, 'apiVersion');
		$apiType = TestReflection::getValue($this->_instance, 'apiType');

		// Verify private values
		$this->assertEquals($apiVersion, $expectedVersion);
		$this->assertEquals($apiType, $expectedType);
	}

	/**
	 * Provides test data for testing fectch controller sufix
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedFetchControllerSuffixData()
	{
		// Input, Expected
		return array(
			array('GET', 'Get', null, false),
			array('POST', 'Get', "get", false),
			array('POST', 'Create', null, false),
			array('POST', 'Create', "post", false),
			array('PUT', 'Update', null, false),
			array('POST', 'Update', "put", false),
			array('PATCH', 'Update', null, false),
			array('POST', 'Update', "patch", false),
			array('DELETE', 'Delete', null, false),
			array('POST', 'Delete', "delete", false),
			array('HEAD', 'Head', null, false),
			array('POST', 'Head', "head", false),
			array('OPTIONS', 'Options', null, false),
			array('POST', 'Options', "options", false),
			array('POST', 'Create', "unknown_method", false),
			array('UNKNOWN', 'Create', "unknown_method", true),
		);
	}

	/**
	 * Tests fetchControllerSuffix()
	 *
	 * @param   string   $input      Input string to test.
	 * @param   string   $expected   Expected fetched string.
	 * @param   mixed    $method     Method to override POST request
	 * @param   boolean  $exception  True if an RuntimeException is expected based on invalid input
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebRouter::fetchControllerSuffix
	 * @dataProvider  seedFetchControllerSuffixData
	 * @since         1.0
	 */
	public function testFetchControllerSuffix($input,  $expected, $method, $exception)
	{
		$_SERVER['REQUEST_METHOD'] = $input;
		$_GET['_method'] = $method;

		// If we are expecting an exception set it.
		if ($exception)
		{
			$this->setExpectedException('RuntimeException');
		}

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'fetchControllerSuffix');

		// Verify the value.
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
		$testMock = $this->getMockWeb();
		$this->_instance = new WebServiceApplicationWebRouter($testMock, $testInput);
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
