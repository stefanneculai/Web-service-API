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
abstract class WebServiceControllerV1Base extends JControllerBase
{
	/**
	 *
	 * @var    string  The content type
	 *
	 * @since  1.0
	 */
	protected $type;

	/**
	 * @var    array  The fields and their database match
	 * @since  1.0
	 */
	protected $fieldsMap;

	/**
	 * @var    array  Required fields
	 * @since  1.0
	 */
	protected $mandatoryFields;

	/**
	 * @var    array  Optional fields
	 * @since  1.0
	 */
	protected $optionalFields;

	/**
	 * @var    array  A map with alternative fields for mandatory ones
	 * @since  1.0
	 */
	protected $alternativeFields;

	/**
	 * @var    array  An array with the available actions
	 * @since  1.0
	 */
	protected $availableActions;

	/**
	 * @var    array  The order of the output
	 * @since  1.0
	 */
	protected $order;

	/**
	 * @var    object  The model associated with the controller
	 * @since  1.0
	 */
	protected $model;

	/**
	 * @var    JUser  User associated with the controller
	 * @sicen  1.0
	 */
	protected $user;

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

	/**
	 * Fetch the fields for the current content.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  RuntimeException if file cannot be read.
	 */
	protected function readFields()
	{
		// Load the configuration file into an object.
		$fields = $this->app->readConfig('content');
		$fields = get_object_vars($fields->{$this->type});

		// Get mandatory fields
		if (isset($fields['mandatory']))
		{
			$this->mandatoryFields = $this->getArrayFields($fields['mandatory']);
		}
		else
		{
			$this->mandatoryFields = array();
		}

		// Get optional fields
		if (isset($fields['optional']))
		{
			$this->optionalFields = $this->getArrayFields($fields['optional']);
		}
		else
		{
			$this->optionalFields = array();
		}

		// Get fields map
		if (isset($fields['map']))
		{
			$this->fieldsMap = get_object_vars($fields['map']);
		}
		else
		{
			$this->fieldsMap = array();
		}

		// Get alternative fields
		if (isset($fields['alternative']))
		{
			$this->alternativeFields = get_object_vars($fields['alternative']);
		}
		else
		{
			$this->alternativeFields = array();
		}

		// Get available actions
		if (isset($fields['actions']))
		{
			$this->availableActions = preg_split('/[\s]*[,][\s]*/', $fields['actions']);
		}
		else
		{
			$this->availableActions = array();
		}
	}

	/**
	 * Get an associative array with the fields from a string
	 *
	 * @param   string  $fields  A string containing the mandatory fields
	 *
	 * @return  array
	 *
	 * @since  1.0
	 */
	protected function getArrayFields($fields)
	{
		// Get an array from fields
		$fieldsArray = preg_split('/[\s]*[,][\s]*/', $fields);

		$fieldList = array();

		// The specified field is empty
		if (count($fieldsArray) == 1 && empty($fieldsArray[0]))
		{
			return $fieldList;
		}

		// Create an array with the key beeing the name of the field
		foreach ($fieldsArray as $key => $field)
		{
			$fieldList[$field] = '';
		}

		return $fieldList;
	}

	/**
	 * Instantiate the controller.
	 *
	 * @param   string            $type   The content type
	 * @param   JInput            $input  The input object.
	 * @param   JApplicationBase  $app    The application object.
	 *
	 * @since   1.0
	 */
	public function __construct($type, JInput $input = null, JApplicationBase $app = null)
	{
		// Setup dependencies.
		$this->app = isset($app) ? $app : $this->loadApplication();
		$this->input = isset($input) ? $input : $this->loadInput();

		// Set type
		$this->type = $type;

		// Init user load table
		JUser::getTable('user', 'WebServiceTable');

		// Init a local user
		$this->user = new JUser;

		// Init model
		$this->model = new WebServiceModelBase(new JContentFactory);
	}

	/**
	 * Prunes fields in an array of JContent objects to a set list.
	 *
	 * @param   mixed  $list    An array of JContent or a JContent object
	 * @param   array  $fields  An array of the field names to preserve (strip all others).
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 */
	protected function pruneFields($list, $fields)
	{
		if ($list == null)
		{
			return array();
		}

		// If a list of fields is passed
		if ($fields)
		{
			// Flip the fields so we can find the intersection by the array keys.
			$fields = array_flip($fields);

			// $list is an array of JContent
			if (is_array($list))
			{
				/* @var $object JContent */
				foreach ($list as $key => $object)
				{

					// Suck out only the fields we want from the object dump.
					$list[$key] = array_uintersect_assoc(
							(array) $object->dump(), $fields,
							create_function(null, 'return 0;')
							);

					$list[$key] = $this->mapFieldsOut($list[$key]);
				}
			}

			// $list a JContent object
			else
			{
				$list = array_uintersect_assoc(
							(array) $list->dump(), $fields,
							create_function(null, 'return 0;')
							);

				$list = $this->mapFieldsOut($list);
			}
		}

		// All fields should be returned
		else
		{
			// $list is an array of JContent
			if (is_array($list))
			{
				foreach ($list as $key => $object)
				{
					// Suck out only the fields we want from the object dump.
					$list[$key] = (array) $object->dump();
					$list[$key] = $this->mapFieldsOut($list[$key]);
				}
			}

			// $list a JContent object
			else
			{
				$list = (array) $list->dump();
				$list = $this->mapFieldsOut($list);
			}
		}

		return $list;
	}

	/**
	 * Map in a string
	 *
	 * @param   string  $string  String to map in
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function mapIn($string)
	{
		if (array_key_exists($string, $this->fieldsMap))
		{
			return $this->fieldsMap[$string];
		}

		return $string;
	}

	/**
	 * Map out a string
	 *
	 * @param   string  $string  String to map out
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function mapOut($string)
	{
		$reverseMap = array_flip($this->fieldsMap);
		if (array_key_exists($string, $reverseMap))
		{
			return $reverseMap[$string];
		}

		return $string;
	}

	/**
	 * Map in an array of fields
	 *
	 * @param   array  $data  An array to map in
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	protected function mapFieldsIn($data)
	{
		foreach ($data as $key => $value)
		{
			$data[$key] = $this->mapIn($value);
		}

		return $data;
	}

	/**
	 * Map out an associative array of fields and value
	 *
	 * @param   array  $data  An array to map out
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	protected function mapFieldsOut($data)
	{
		foreach ($data as $key => $value)
		{
			if ( strcmp($this->mapOut($key), $key) !== 0)
			{
				$data[$this->mapOut($key)] = $data[$key];
				unset($data[$key]);
			}
		}

		return $data;
	}

	/**
	 * Set conditions
	 *
	 * @param   array  $conditions  An associative array with the conditions
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setWhere($conditions)
	{
		// Get content state
		$modelState = $this->model->getState();

		// Set where condition fields
		$modelState->set('where.fields', implode(',', array_keys($conditions)));

		// Set each fields in where condition
		foreach ($conditions as $key => $value)
		{
			$modelState->set('where.' . $key, $value);
		}
	}

	/**
	 * Check if content exists in database
	 *
	 * @param   integer  $id    The contnet ID
	 * @param   string   $type  The content type
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	protected function itemExists($id, $type)
	{
		// Get content state
		$modelState = $this->model->getState();

		// Save old values from state (not sure if that should be done)
		$typeBackup = $modelState->get('content.type');
		$idBackup = $modelState->get('content.id');

		// Set new content and id
		$modelState->set('content.type', $type);
		$modelState->set('content.id', $id);

		// Check if item exists
		$exists = $this->model->existsItem($id);

		// Restore old values from state
		$modelState->set('content.type', $typeBackup);
		$modelState->set('content.id', $idBackup);

		return $exists;
	}

	/** Get the user_id from input and check if it exists in database
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	protected function checkUserId()
	{
		$user_id = $this->input->get->getString('user_id');
		if (isset($user_id))
		{
			return $this->user->load($user_id);
		}

		return false;
	}

	/**
	 * Get the user_id passed in input or null if it does not exist in database
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function getUserId()
	{
		$user_id = $this->input->get->getString('user_id');
		if (isset($user_id))
		{
			if ($this->checkUserId() == true)
			{
				return $user_id;
			}
			else
			{
				$this->app->errors->addError("201", array($this->input->get->getString('user_id')));
			}
		}

		return null;
	}
}
