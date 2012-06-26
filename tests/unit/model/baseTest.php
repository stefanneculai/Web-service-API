<?php
/**
 * @package     WebService.Tests
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test Case class for WebServiceModelBase
 *
 * @package     WebService.Tests
 * @subpackage  Application
 * @since       1.0
 */

class WebServiceModelBaseTest extends TestCase
{
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

		$this->saveFactoryState();

		JFactory::$session = $this->getMockSession();

		$factory = new JContentFactory('TPrefix', $this->getMockDatabase(), $this->getMockWeb(), new JUser);

		$this->_instance = new WebServiceModelBase($factory, $this->getMockDatabase());
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
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Tests __construct()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceModelBase::__construct
	 * @since   1.0
	 */
	public function test__construct()
	{
		JFactory::$database = 'factory db';

		$factory = new JContentFactory('TPrefix', $this->getMockDatabase(), $this->getMockWeb());

		// Construct the object.
		$model = new WebServiceModelBase($factory);

		// Verify that the values injected into the constructor are present.
		$this->assertEquals('factory db', TestReflection::getValue($model, 'db'));
		$this->assertEquals('TPrefix', TestReflection::getValue(TestReflection::getValue($model, 'factory'), 'prefix'));
	}

	/**
	 * Test existsItem()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceModelBase::existsItem
	 * @since   1.0
	 */
	public function testItemExists()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test existsItem()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceModelBase::existsItem
	 * @since   1.0
	 */
	public function testItemDoesNotExists()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test getTypes()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceModelBase::getTypes
	 * @since   1.0
	 */
	public function testGetTypes()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test getItem()
	 *
	 * @return  void
	 *
	 * @covers  WebServiceModelBase::getItem
	 * @since   1.0
	 */
	public function testGetItem()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}
}
