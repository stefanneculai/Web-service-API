<?php
/**
 * @package     WebService.Application
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * WebService base controller content class
 *
 * @package     WebService.Application
 * @subpackage  Controller
 * @since       1.0
 */
abstract class WebServiceControllerV1JsonContentBase extends JControllerBase
{
	/**
	 * @var    array  The fields and their database match
	 * @since  1.0
	 */
	protected $fieldsMap = array(
		'id' => 'id',
		'created_at' => 'created_at',
		'user_id' => 'user_id',
		'field1' => 'field1',
		'field2' => 'field2',
		'field3' => 'field3',
		'field4' => 'field4',
		'field5' => 'field5'
	);

	/**
	 * @var    array  Required fields
	 * @since  1.0
	 */
	protected $mandatoryFields = array(
			'field1' => '',
			'field2' => '',
			'field3' => ''
			);

	/**
	 * @var    array  Optional fields
	 * @since  1.0
	 */
	protected $optionalFields = array(
			'field4' => '',
			'field5' => ''
			);

	/**
	 * Abstract function to init parameters
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	abstract protected function init();

	/**
	 * Abstract method to parse the returned data from database
	 *
	 * @param   mixed  $data  A JContent object, an array of JContent or a boolean.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	abstract protected function parseData($data);

}
