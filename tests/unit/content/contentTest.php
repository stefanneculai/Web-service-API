<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Content
 *
 * @copyright   Copyright 2011 eBay, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Load test mocks.
require_once JPATH_PLATFORM . '/../tests/suite/joomla/content/mocks/content.php';
require_once JPATH_PLATFORM . '/../tests/suite/joomla/content/mocks/helper.php';
require_once __DIR__ . '/stubs/inspector.php';

/**
 * Tests for the JContentHelperTest class.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Content
 * @since       11.4
 */
class WebServiceContentTest extends TestCaseDatabase
{
	/**
	 * Test object.
	 *
	 * @var    JContent
	 * @since  12.1
	 */
	protected $content;

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  xml dataset
	 *
	 * @since   11.1
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__ . '/stubs/content.xml');
	}

	/**
	 * Get an original content item.
	 *
	 * @param   integer  $contentId  The id of the content.
	 * @param   integer  $userId     The id of the user.
	 * @param   string   $type       The content type.
	 *
	 * @return  JContent
	 *
	 * @since   11.4
	 */
	protected function getOriginal($contentId = 1, $userId = 1, $type = 'Inspector')
	{
		// Get a new content factory.
		$factory = new JContentFactory('WebService', null, $this->getMockWeb());

		// Get a new content object.
		$original = $factory->getContent($type)
			->load($contentId);

		// Create a guest user.
		$user = new JUser;
		$user->set('id', $userId);
		$user->set('guest', false);

		// Push the guest user into the content object.
		TestReflection::setValue($original, 'user', $user);

		return $original;
	}

	/**
	 * Method to set up the tests.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		JFactory::$session = $this->getMockSession();

		// Create a new content factory.
		$this->factory = new JContentFactory('WebService', $this->getMockDatabase(), $this->getMockWeb());

		// Create a mock type.
		$this->type = $this->factory->getType('TCType');
		$this->type->bind(
			array(
				'type_id'	=> 1,
				'title'		=> 'Test Type',
				'alias'		=> 'tctype'
			)
		);

		// Get a mock helper.
		$this->helper = JContentHelperMock::create($this);
		$this->helper->expects($this->any())
			->method('getTypes')
			->will($this->returnValue(array('tctype' => $this->type)));

		// Get the content object.
		$this->content = $this->factory->getContent('TCType', $this->helper);
	}

	/**
	 * Tears down the fixture.
	 *
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
     * @since   11.4
	 */
	public function tearDown()
	{
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Method to test that WebServiceContent::like() works as expected.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testLike()
	{
		$original = $this->getOriginal();

		$this->assertThat(
			$original->like(),
			$this->identicalTo($original),
			'Checks chaining.'
		);

		self::$driver->setQuery('SELECT * FROM #__content_likes WHERE content_id = 1 AND user_id = 1');
		$r = self::$driver->loadResult();

		$this->assertThat(
			count($r),
			$this->equalTo(1),
			'Check the like was added to the content_likes table.'
		);

		self::$driver->setQuery('SELECT `likes` FROM #__content WHERE content_id = 1');
		$r = self::$driver->loadResult();

		$this->assertThat(
			$r,
			$this->equalTo(1),
			'Check the likes incremented in the content table.'
		);

		$this->assertThat(
			$original->likes,
			$this->equalTo(1),
			'Check the likes incremented in the original object.'
		);
	}

	/**
	 * Method to test that WebServiceContent::like() works as expected.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testLikeWithParams()
	{
		$original = $this->getOriginal();

		$this->assertThat(
			$original->like('1'),
			$this->identicalTo($original),
			'Checks chaining.'
		);

		self::$driver->setQuery('SELECT * FROM #__content_likes WHERE content_id = 1 AND user_id = 1');
		$r = self::$driver->loadResult();

		$this->assertThat(
			count($r),
			$this->equalTo(1),
			'Check the like was added to the content_likes table.'
		);

		self::$driver->setQuery('SELECT `likes` FROM #__content WHERE content_id = 1');
		$r = self::$driver->loadResult();

		$this->assertThat(
			$r,
			$this->equalTo(1),
			'Check the likes incremented in the content table.'
		);

		$this->assertThat(
			$original->likes,
			$this->equalTo(1),
			'Check the likes incremented in the original object.'
		);
	}

	/**
	 * Method to test that WebServiceContent::unlike() works as expected.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testUnlike()
	{
		$original = $this->getOriginal(2, 2);

		$this->assertThat(
			$original->unlike(),
			$this->identicalTo($original),
			'Checks chaining.'
		);

		self::$driver->setQuery('SELECT * FROM #__content_likes WHERE content_id = 2 AND user_id = 2');
		$r = self::$driver->loadResult();

		$this->assertThat(
			count($r),
			$this->equalTo(0),
			'Check the like was dropped from the content_likes table.'
		);

		self::$driver->setQuery('SELECT `likes` FROM #__content WHERE content_id = 2');
		$r = self::$driver->loadResult();

		$this->assertThat(
			$r,
			$this->equalTo(3),
			'Check the likes decremented in the content table.'
		);

		$this->assertThat(
			$original->likes,
			$this->equalTo(3),
			'Check the likes decremented in the original object.'
		);
	}

	/**
	 * Method to test that WebServiceContent::unlike() works as expected.
	 *
	 * @return  void
	 *
	 * @since   11.4
	 */
	public function testUnlikeWithParams()
	{
		$original = $this->getOriginal(2, 2);

		$this->assertThat(
			$original->unlike(2),
			$this->identicalTo($original),
			'Checks chaining.'
		);

		self::$driver->setQuery('SELECT * FROM #__content_likes WHERE content_id = 2 AND user_id = 2');
		$r = self::$driver->loadResult();

		$this->assertThat(
			count($r),
			$this->equalTo(0),
			'Check the like was dropped from the content_likes table.'
		);

		self::$driver->setQuery('SELECT `likes` FROM #__content WHERE content_id = 2');
		$r = self::$driver->loadResult();

		$this->assertThat(
			$r,
			$this->equalTo(3),
			'Check the likes decremented in the content table.'
		);

		$this->assertThat(
			$original->likes,
			$this->equalTo(3),
			'Check the likes decremented in the original object.'
		);
	}
}
