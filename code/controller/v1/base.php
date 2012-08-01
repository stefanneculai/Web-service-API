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
		// Initialise variables.
		$fields = array();

		// Ensure that required path constants are defined.
		if (!defined('JPATH_CONFIGURATION'))
		{
			$path = getenv('WEBSERVICE_CONFIG');
			if ($path)
			{
				define('JPATH_CONFIGURATION', realpath($path));
			}
			else
			{
				define('JPATH_CONFIGURATION', realpath(dirname(JPATH_BASE) . '/config'));
			}
		}

		// Set the configuration file path for the application.
		if (file_exists(JPATH_CONFIGURATION . '/content.json'))
		{
			$file = JPATH_CONFIGURATION . '/content.json';
		}
		else
		{
			// Default to the distribution configuration.
			$file = JPATH_CONFIGURATION . '/content.dist.json';
		}

		if (!is_readable($file))
		{
			throw new RuntimeException('Content file does not exist or is unreadable.');
		}

		// Load the configuration file into an object.
		$fields = json_decode(file_get_contents($file));
		$fields = get_object_vars($fields->{$this->type});

		if ($fields == null)
		{
			throw new RuntimeException('Content file cannot be decoded.');
		}

		$this->mandatoryFields = $this->getArrayFields($fields['mandatory']);
		$this->optionalFields = $this->getArrayFields($fields['optional']);
		$this->fieldsMap = get_object_vars($fields['map']);

		if (isset($fields['alternative']))
		{
			$this->alternativeFields = get_object_vars($fields['alternative']);
		}
		else
		{
			$this->alternativeFields = array();
		}

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
		$fieldsArray = explode(',', $fields);

		$fieldList = array();

		if (count($fieldsArray) == 1 && strlen(trim($fieldsArray[0])) == 0)
		{
			return $fieldList;
		}

		foreach ($fieldsArray as $key => $field)
		{
			$fieldList[trim($field)] = '';
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
	 * @since  12.1
	 */
	public function __construct($type, JInput $input = null, JApplicationBase $app = null)
	{
		// Setup dependencies.
		$this->app = isset($app) ? $app : $this->loadApplication();
		$this->input = isset($input) ? $input : $this->loadInput();

		$this->type = $type;

		// Init user load table
		JUser::getTable('user', 'WebServiceTable');

		// Init model
		$this->model = new WebServiceModelBase(new JContentFactory('WebService'));
	}

	/**
	 * Prunes fields in an array of JContent objects to a set list.
	 *
	 * @param   mixed  $list    An array of Jcontent or a Jcontent object
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

		if ($fields)
		{
			// Flip the fields so we can find the intersection by the array keys.
			$fields = array_flip($fields);

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
			else
			{
				$list = array_uintersect_assoc(
							(array) $list->dump(), $fields,
							create_function(null, 'return 0;')
							);

				$list = $this->mapFieldsOut($list);
			}
		}
		else
		{
			if (is_array($list))
			{
				foreach ($list as $key => $object)
				{
					// Suck out only the fields we want from the object dump.
					$list[$key] = (array) $object->dump();
					$list[$key] = $this->mapFieldsOut($list[$key]);
				}
			}
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
	 * Map in an array
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
	 * Map out an associative array
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

		$modelState->set('where.fields', implode(',', array_keys($conditions)));

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
		$modelState = $this->model->getState();

		$typeBackup = $modelState->get('content.type');
		$idBackup = $modelState->get('content.id');

		$modelState->set('content.type', $type);
		$modelState->set('content.id', $id);

		$exists = $this->model->existsItem($id);

		$modelState->set('content.type', $typeBackup);
		$modelState->set('content.id', $idBackup);

		return $exists;
	}
}
