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
	 * Provides test data for reordering route
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedActionRouteData()
	{
		// Input, Expected
		return array(
			array('content', 'content'),
			array('content/1', 'content/1'),
			array('content/1/like', 'content/1', array('action' => 'like')),
			array('content/count', 'content', array('action' => 'count'))
		);
	}

	/**
	 * Tests actionRoute()
	 *
	 * @param   string  $input           Input string to test.
	 * @param   string  $expected        Expected fetched string.
	 * @param   string  $expected_input  The data expected to find in registry
	 *
	 * @return  void
	 *
	 * @dataProvider  seedActionRouteData
	 * @since         1.0
	 */
	public function testActionRoute($input,  $expected, $expected_input=null)
	{
		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'actionRoute', $input);

		// Verify the value.
		$this->assertEquals($expected, $actual);

		if ($expected_input != null)
		{
			$actual_input = TestReflection::getValue($this->_instance, 'input');
			foreach ($expected_input as $key => $value)
			{
				$this->assertEquals($value, $actual_input->get->getString($key));
			}
		}
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
