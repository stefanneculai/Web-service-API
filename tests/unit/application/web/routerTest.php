<?php
/**
 * @package     WebService.Tests
 * @subpackage  Application
 *
 * @copyright   Copyright (C) {COPYRIGHT}. All rights reserved.
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
	 * Provides test data for content type fetching.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedContentTypeData()
	{
		// Input, Expected
		return array(
			array('application/vnd.webservice+json', 'application/vnd.webservice+json'),
			array('application/vnd.webservice.1+json', 'application/vnd.webservice.1+json'),
			array('application/vnd.webservice.2.raw+json', 'application/vnd.webservice.2.raw+json'),
			array('application/vnd.webservice+xml', 'application/vnd.webservice+xml'),
			array('application/json', 'application/vnd.webservice+json'),
			array('', 'application/vnd.webservice+json'),
			array('webservice', 'webservice')
		);
	}

	/**
	 * Provides test data for fetching controller class names.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedFetchControllerClassData()
	{
		// Base, Route, Method, Expected, Remainder, Exception
		return array(
			array('WebServiceControllerV1Json', 'content/7', 'GET', 'WebServiceControllerV1JsonContentGet', '7', false),
			array('WebServiceControllerV1Json', 'content', 'GET', 'WebServiceControllerV1JsonContentGet', '', false),
 			array('WebServiceControllerV1Json', 'content', 'DELETE', 'WebServiceControllerV1JsonContentDelete', '', false),
 			array('WebServiceControllerV1Json', 'content', 'PUT', 'WebServiceControllerV1JsonContentCreate', '', false),
			array('WebServiceControllerV1Json', 'content', 'POST', 'WebServiceControllerV1JsonContentUpdate', '', false),
			array('WebServiceControllerV1Json', 'content', 'PATCH', 'WebServiceControllerV1JsonContentUpdate', '', false)
		);
	}

	/**
	 * Provides test data for request format detection.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedRequestFormatData()
	{
		// Input, Version, Output, Type, Exception
		return array(
			array('application/vnd.webservice+json', 1, 'raw', 'json', false),
			array('application/vnd.webservice.1+json', 1, 'raw', 'json', false),
			array('application/vnd.webservice.1.raw+json', 1, 'raw', 'json', false),
			array('application/vnd.webservice.2+json', 2, 'raw', 'json', false),
			array('application/vnd.webservice.2.raw+json', 2, 'raw', 'json', false),
			array('application/vnd.webservice.1.full+json', 1, 'full', 'json', false),
			array('application/vnd.webservice.2.full+json', 2, 'full', 'json', false),
			array('application/vnd.webservice.full+json', 1, 'full', 'json', false),
			array('application/vnd.webservice.42+json', 42, 'raw', 'json', false),
			array('application/vnd.webservice.3', 3, 'raw', 'json', false),
			array('application/vnd.webservice.full', 1, 'full', 'json', false),
			array('application/vnd.webservice.7.full', 7, 'full', 'json', false),
			array('application/vnd.webservice+xml', 1, 'raw', 'xml', false),
			array('application/json', 1, 'raw', 'json', true),
			array('', 1, 'raw', 'json', true),
			array('datahub', 1, 'raw', 'json', true)
		);
	}

	/**
	 * Provides test data for request format detection.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedRequestMethodData()
	{
		// Input, Override, Expected, Exception
		return array(
			array('GET', '', 'GET', false),
			array('POST', '', 'POST', false),
			array('PUT', '', 'PUT', false),
			array('DELETE', '', 'DELETE', false),
			array('PATCH', '', 'PATCH', false),
			array('OPTIONS', '', 'OPTIONS', false),
			// Output should always be allcaps.
			array('Put', '', 'PUT', false),
			// If the main method is not post then the override is ignored.
			array('GET', 'POST', 'GET', false),
			array('DELETE', 'PUT', 'DELETE', false),
			array('PUT', 'GET', 'PUT', false),
			// If the main method is POST and we have an override, the override wins.
			array('POST', 'GET', 'GET', false),
			array('POST', 'PUT', 'PUT', false),
			array('POST', 'DELETE', 'DELETE', false),
			// Test some unsupported cases.  Exceptions are expected.
			array('TRACE', '', '', true),
			array('TEST', '', '', true)
		);
	}

	/**
	 * Provides test data for route rewriting.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedRewriteRouteData()
	{
		// Input, Expected
		return array(
			array('/path/to/resource', '/path/to/resource'),
			array('/container/123', '/container/123'),
			array('/path/to/42/resource', '/path/to/resource/42'),
			array('/container/123/like', '/container/like/123'),
			array('/path/to/7/resource/action', '/path/to/resource/7/action'),
			array('/321', '/321')
		);
	}

	/**
	 * Tests __construct()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceApplicationWebRouter::__construct
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
		$router = new WebServiceApplicationWebRouter($input, $this->getMockWeb());

		// Verify that the values injected into the constructor are present.
		$this->assertEquals('ok', TestReflection::getValue($router, 'input')->test());
	}

	/**
	 * Tests detectRequestFormat()
	 *
	 * @param   string   $input      Content-Type header string to test.
	 * @param   integer  $version    Expected API version.
	 * @param   string   $output     Expected API output format.
	 * @param   string   $type       Expected API request type.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebRouter::detectRequestFormat
	 * @dataProvider  seedRequestFormatData
	 * @since         1.0
	 */
		public function testDetectRequestFormat($input, $version, $output, $type, $exception)
	{
		// If we are expecting an exception set it.
		if ($exception)
		{
			$this->setExpectedException('InvalidArgumentException');
		}

		// Execute the code to test.
		TestReflection::invoke($this->_instance, 'detectRequestFormat', $input);

		// Verify the found values.
		$this->assertEquals($version, TestReflection::getValue($this->_instance, 'apiVersion'));
		$this->assertEquals($output, TestReflection::getValue($this->_instance, 'apiOutput'));
		$this->assertEquals($type, TestReflection::getValue($this->_instance, 'apiType'));
	}

	/**
	 * Tests fetchContentType()
	 *
	 * @param   string   $input     Content-Type header string to test.
	 * @param   string   $expected  Expected fetched string.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebRouter::fetchContentType
	 * @dataProvider  seedContentTypeData
	 * @since         1.0
	 */
	public function testFetchContentType($input, $expected)
	{
		// Set the input value.
		$_SERVER['CONTENT_TYPE'] = $input;

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'fetchContentType', $input);

		// Clean up after ourselves.
		$_SERVER['CONTENT_TYPE'] = null;

		// Verify the value.
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Tests fetchControllerClass()
	 *
	 * @param   string   $base       Controller class base name.
	 * @param   string   $route      Request route for which to fetch the controller class.
	 * @param   string   $method     HTTP request method.
	 * @param   string   $expected   Expected controller class name.
	 * @param   string   $remainder  Expected route remainder to be placed in @route within the JInput object.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebRouter::fetchControllerClass
	 * @dataProvider  seedFetchControllerClassData
	 * @since         1.0
	 */
	public function testFetchControllerClass($base, $route, $method, $expected, $remainder, $exception)
	{
		// If we are expecting an exception set it.
		if ($exception)
		{
			$this->setExpectedException('InvalidArgumentException');
		}

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'fetchControllerClass', $base, new JURI($route), $method);

		// Verify the value.
		$this->assertEquals($expected, $actual);
		$this->assertEquals($remainder, TestReflection::getValue($this->_instance, 'input')->get->get('@route'));
	}

	/**
	 * Tests fetchRequestMethod()
	 *
	 * @param   string   $input      Request Method string to test.
	 * @param   string   $override   Method override string to use in the query string.
	 * @param   string   $expected   Expected fetched string.
	 * @param   boolean  $exception  True if an InvalidArgumentException is expected based on invalid input.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebRouter::fetchRequestMethod
	 * @dataProvider  seedRequestMethodData
	 * @since         1.0
	 */
	public function testFetchRequestMethod($input, $override, $expected, $exception)
	{
		// Set the input values.
		
		$_SERVER['REQUEST_METHOD'] = $input;
		$_GET['_method'] = $override;
		
		// If we are expecting an exception set it.
		if ($exception)
		{
			$this->setExpectedException('InvalidArgumentException');
		}

		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'fetchRequestMethod');

		// Clean up after ourselves.
		$_SERVER['REQUEST_METHOD'] = null;
		$_GET['_method'] = null;

		// Verify the value.
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Tests getController()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceApplicationWebRouter::getController
	 * @since   1.0
	 */
	public function testGetController()
	{
		$this->markTestIncomplete("getController test not implemented");

		$this->_instance->getController();
	}

	/**
	 * Tests rewriteRoute()
	 *
	 * @param   string   $input     Route to rewrite.
	 * @param   string   $expected  Expected route string.
	 *
	 * @return  void
	 *
	 * @covers        WebServiceApplicationWebRouter::rewriteRoute
	 * @dataProvider  seedRewriteRouteData
	 * @since         1.0
	 */
	public function testRewriteRoute($input, $expected)
	{
		// Execute the code to test.
		$actual = TestReflection::invoke($this->_instance, 'rewriteRoute', $input);

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

		$this->_instance = new WebServiceApplicationWebRouter(new JInput(array()), $this->getMockWeb());
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
