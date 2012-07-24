<?php

/**
 * @package     WebService.Model
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Web Service Api Model class.
 *
 * @package     WebService.Model
 * @subpackage  Model
 * @since       1.0
 */
class WebServiceModelBase extends JModelBase
{
	/**
	 * The content factory.
	 *
	 * @var    JContentFactory
	 * @since  1.0
	 */
	protected $factory;

	/**
	 * The database driver.
	 *
	 * @var    JDatabaseDriver
	 * @since  1.0
	 */
	protected $db;

	/**
	 * Method to instantiate the model.
	 *
	 * @param   JContentFactory  $factory  The content factory.
	 * @param   JDatabaseDriver  $db       The database adpater.
	 * @param   JRegistry        $state    The model state.
	 *
	 * @since   1.0
	 */
	public function __construct(JContentFactory $factory = null, JDatabaseDriver $db = null, JRegistry $state = null)
	{
		parent::__construct($state);

		if ($factory == null)
		{
			$this->factory = JContentFactory::getInstance();
		}
		else
		{
			$this->factory = $factory;
		}

		if ($db == null)
		{
			$this->db = JFactory::getDbo();
		}
		else
		{
			$this->db = $db;
		}
	}

	/**
	 * Method to get a list of content items.
	 *
	 * @return  array  An array JContent objects.
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function getList()
	{
		$list = array();
		$load = array();

		// Get a list of content ids and types that match the list criteria.
		$this->db->setQuery($this->getListQuery(), $this->state->get('list.offset'), $this->state->get('list.limit'));
		$matches = $this->db->loadObjectList();

		// Restructure the matches to organize the content ids by content type.
		foreach ($matches as $match)
		{
			// Set the content id in the list to preserve the ordering.
			$list[$match->content_id] = null;

			// Initialise the type container if necessary.
			if (!isset($load[$match->type]))
			{
				$load[$match->type] = array();
			}

			// Set the content id in the appropriate container.
			$load[$match->type][] = $match->content_id;
		}

		// Load the objects.
		foreach ($load as $typeAlias => $contentIds)
		{
			$objects = array();
			foreach ($contentIds as $contentAlias => $contentId)
			{
				// Load the content objects from the mapper.
				$objects[$contentId] = $this->factory->getContent($typeAlias)->load($contentId);
			}

			// Merge the objects into the list.
			$list = array_intersect_key($objects, $list) + $list;
		}

		return $list;
	}

	/**
	 * Count items
	 *
	 * @return  integer  The number of rows that match the sql
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function countItems()
	{
		$itemsArray = $this->getList();
		return count($itemsArray);
	}

	/**
	 * Method to get a database query object to load a list of items.
	 *
	 * @return  object  A JDatabaseQuery object.
	 *
	 * @since   1.0
	 */
	protected function getListQuery()
	{
		// Build the query object.
		$query = $this->db->getQuery(true);
		$query->select($query->qn('a.content_id'));
		$query->select($query->qn('b.alias', 'type'));
		$query->from($query->qn('#__content', 'a'));
		$query->innerJoin($query->qn('#__content_types', 'b') . ' ON ' . $query->qn('b.type_id') . ' = ' . $query->qn('a.type_id'));

		// Check if we should filter the list based on content type.
		if (!is_null($this->state->get('content.type')))
		{
			// Get the requested type.
			$type = $this->state->get('content.type');

			// The type can be either a string type alias or a numeric type id.
			if (is_numeric($type))
			{
				// Handle a numeric type.
				$query->where($query->qn('a.type_id') . ' = ' . (int) $type);
			}
			elseif (is_string($type))
			{
				// Handle a type alias.
				$query->where($query->qn('b.alias') . ' = ' . $query->q($type));
			}
		}

		// Check if we should filter the list based on user_id
		if (!is_null($this->state->get('content.user_id')))
		{
			// Get the requested user_id.
			$user_id = $this->state->get('content.user_id');
			$query->where($query->qn('a.created_user_id') . ' = ' . (int) $user_id);
		}

		// Check if we should filter the list based on since date
		if (!is_null($this->state->get('filter.since')))
		{
			$query->where($query->qn('a.created_date') . ' > ' . '\'' . $this->state->get('filter.since') . '\'');
		}

		// Check if we should filter the list based on before date
		if (!is_null($this->state->get('filter.before')))
		{
			$query->where($query->qn('a.created_date') . ' < ' . '\'' . $this->state->get('filter.before') . '\'');
		}

		if (!is_null($this->state->get('where.fields')))
		{
			$where = explode(',', $this->state->get('where.fields'));

			foreach ($where as $name => $value)
			{
				$query->where($query->qn('a.' . $value) . '=' . '\'' . $this->state->get('where.' . $value) . '\'');
			}
		}

		if (!is_null($this->state->get('tags.content_id')))
		{
			$query->innerJoin($query->qn('#__content_tag_map', 'c') . ' ON ' . $query->qn('c.tag_id') . ' = ' . $query->qn('a.content_id'));
			$query->where($query->qn('c.content_id') . '=' . $this->state->get('tags.content_id'));
		}

		return $query;
	}

	/**
	 * Method to get a content item.
	 *
	 * @return  JContent  A content object.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function getItem()
	{
		// Get the content id and type.
		$contentId = $this->state->get('content.id');
		$contentType = $this->state->get('content.type');

		// Assert that the content id is set.
		if (empty($contentId))
		{
			throw new InvalidArgumentException(get_class($this) . '->getItem() called without a content id set in state.');
		}

		// Check if the content type is set.
		if (empty($contentType))
		{
			// Get the content type for the id.
			$results = $this->getTypes($contentId);

			// Assert that the content type was found.
			if (empty($results[$contentId]))
			{
				throw new UnexpectedValueException(sprintf('%s->getItem() could not find the content type for item %s.', get_class($this), $contentId));
			}

			// Set the content type alias.
			$contentType = $results[$contentId]->type;
		}

		if ($this->existsItem($contentId))
		{
			return $this->factory->getContent($contentType)->load($contentId);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to delete a content item.
	 *
	 * @return  JContent  A content object.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function deleteItem()
	{
		// Get the content id and type.
		$contentId = $this->state->get('content.id');
		$contentType = $this->state->get('content.type');

		// Assert that the content id is set.
		if (empty($contentId))
		{
			throw new InvalidArgumentException(sprintf('%s->deleteItem() called without a content id set in state.', get_class($this)));
		}

		// Check if the content type is set.
		if (empty($contentType))
		{
			// Get the content type for the id.
			$results = $this->getTypes($contentId);

			// Assert that the content type was found.
			if (empty($results[$contentId]))
			{
				throw new UnexpectedValueException(sprintf('%s->deleteItem() could not find the content type for item %s.', get_class($this), $contentId));
			}

			// Set the content type alias.
			$contentType = $results[$contentId]->type;
		}

		if ($this->existsItem($contentId))
		{
			$item = $this->factory->getContent($contentType)->load($contentId);
			$item->delete();
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to delete a content item.
	 *
	 * @return  JContent  A content object.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function deleteList()
	{
		$contentType = $this->state->get('content.type');

		// Check if the content type is set.
		if (empty($contentType))
		{
				throw new UnexpectedValueException(sprintf('%s->deleteItem() could not find the content type.', get_class($this)));
		}

		$this->state->set('list.offset', null);
		$this->state->set('list.limit', null);

		$objects = $this->getList();

		foreach ($objects as $key => $object)
		{
			$object->delete();
		}

		return true;
	}

	/**
	 * Method to check existance of an item by ID
	 *
	 * @param   int  $contentId  The id of the content to test if exists
	 *
	 * @return  boolean  True or false if the item exists or not in database
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function existsItem($contentId)
	{
		// Build the query object.
		$query = $this->db->getQuery(true);
		$query->select('COUNT(' . $query->qn('a.content_id') . ')');
		$query->from($query->qn('#__content', 'a'));
		$query->innerJoin($query->qn('#__content_types', 'b') . ' ON ' . $query->qn('b.type_id') . ' = ' . $query->qn('a.type_id'));
		$query->where($query->qn('a.content_id') . ' = ' . (int) $contentId);

		// Check if we should filter the list based on content type.
		if (!is_null($this->state->get('content.type')))
		{
			// Get the requested type.
			$type = $this->state->get('content.type');

			// The type can be either a string type alias or a numeric type id.
			if (is_numeric($type))
			{
				// Handle a numeric type.
				$query->where($query->qn('a.type_id') . ' = ' . (int) $type);
			}
			elseif (is_string($type))
			{
				// Handle a type alias.
				$query->where($query->qn('b.alias') . ' = ' . $query->q($type));
			}
		}

		$this->db->setQuery($query);
		$row = $this->db->loadRow();

		if ( (int) $row[0] > 0 )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to get the content types for one or more content items.
	 *
	 * @param   mixed  $contentIds  An integer or array of integer content ids.
	 *
	 * @return  array  An array of JContentType objects.
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	protected function getTypes($contentIds)
	{
		// Check if only one content id was submitted.
		if (is_scalar($contentIds))
		{
			$contentIds = array($contentIds);
		}

		// Sanitize the content ids.
		JArrayHelper::toInteger($contentIds);

		// Build a query to get the content types for the ids.
		$query = $this->db->getQuery(true);
		$query->select($query->qn('a.content_id'));
		$query->select($query->qn('b.alias', 'type'));
		$query->from($query->qn('#__content', 'a'));
		$query->innerJoin($query->qn('#__content_types', 'b') . ' ON ' . $query->qn('b.type_id') . ' = ' . $query->qn('a.type_id'));
		$query->where($query->qn('a.content_id') . ' IN(' . implode(',', $contentIds) . ')');

		// Get the content types for the ids.
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList('content_id');

		return $results;
	}

	/**
	 * Method to delete a content item.
	 *
	 * @return  JContent  A content object.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function updateItem()
	{
		// Get the content id and type.
		$contentId = $this->state->get('content.id');
		$contentType = $this->state->get('content.type');

		// Assert that the content id is set.
		if (empty($contentId))
		{
			throw new InvalidArgumentException(sprintf('%s-updateItem() called without a content id set in state.', get_class($this)));
		}

		// Check if the content type is set.
		if (empty($contentType))
		{
			// Get the content type for the id.
			$results = $this->getTypes($contentId);

			// Assert that the content type was found.
			if (empty($results[$contentId]))
			{
				throw new UnexpectedValueException(sprintf('%s->deleteItem() could not find the content type for item %s.', get_class($this), $contentId));
			}

			// Set the content type alias.
			$contentType = $results[$contentId]->type;
		}

		if ($this->existsItem($contentId))
		{
			$item = $this->factory->getContent($contentType)->load($contentId);

			// Get fields for new content and check them
			$fields = $this->state->get('content.fields');
			if (empty($fields))
			{
				throw new UnexpectedValueException('Missing fields for new object');
			}

			// Get each field for the new content
			$fieldsArray = preg_split('#[\s,]+#', $fields, null, PREG_SPLIT_NO_EMPTY);
			foreach ($fieldsArray as $key => $fieldName)
			{

				$field = null;
				if ($this->state->exists('fields.' . $fieldName))
				{
					$field = $this->state->get('fields.' . $fieldName);
				}

				if ($field == null)
				{
					throw new UnexpectedValueException('Missing field ' . $fieldName);
				}

				$item->__set($fieldName, $field);
			}

			$item->update();

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to hit a content item.
	 *
	 * @return  JContent  A content object.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function hitItem()
	{
		// Get the content id and type.
		$contentId = $this->state->get('content.id');
		$contentType = $this->state->get('content.type');

		// Assert that the content id is set.
		if (empty($contentId))
		{
			throw new InvalidArgumentException(sprintf('%s-hitItem() called without a content id set in state.', get_class($this)));
		}

		// Check if the content type is set.
		if (empty($contentType))
		{
			// Get the content type for the id.
			$results = $this->getTypes($contentId);

			// Assert that the content type was found.
			if (empty($results[$contentId]))
			{
				throw new UnexpectedValueException(sprintf('%s->hitItem() could not find the content type for item %s.', get_class($this), $contentId));
			}

			// Set the content type alias.
			$contentType = $results[$contentId]->type;
		}

		if ($this->existsItem($contentId))
		{
			$item = $this->factory->getContent($contentType)->load($contentId);

			$item->hit();

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to like a content item.
	 * TODO: check if user exists before like
	 *
	 * @return  JContent  A content object.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function likeItem()
	{
		// Get the content id and type.
		$contentId = $this->state->get('content.id');
		$contentType = $this->state->get('content.type');
		$userId = $this->state->get('content.user_id');

		// Assert that the content id is set.
		if (empty($contentId))
		{
			throw new InvalidArgumentException(sprintf('%s-likeItem() called without a content id set in state.', get_class($this)));
		}

		// Assert that the content id is set.
		if (empty($userId))
		{
			throw new InvalidArgumentException(sprintf('%s-likeItem() called without a user identifier set in state.', get_class($this)));
		}

		// Check if the content type is set.
		if (empty($contentType))
		{
			// Get the content type for the id.
			$results = $this->getTypes($contentId);

			// Assert that the content type was found.
			if (empty($results[$contentId]))
			{
				throw new UnexpectedValueException(sprintf('%s->likeItem() could not find the content type for item %s.', get_class($this), $contentId));
			}

			// Set the content type alias.
			$contentType = $results[$contentId]->type;
		}

		if ($this->existsItem($contentId))
		{
			$item = $this->factory->getContent($contentType)->load($contentId);

			$item->like($userId);

			return true;
		}
		else
		{
			return false;
		}
	}

/**
	 * Method to unlike a content item.
	 * TODO: check if user exists before unlike
	 *
	 * @return  JContent  A content object.
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function unlikeItem()
	{
		// Get the content id and type.
		$contentId = $this->state->get('content.id');
		$contentType = $this->state->get('content.type');
		$userId = $this->state->get('content.user_id');

		// Assert that the content id is set.
		if (empty($contentId))
		{
			throw new InvalidArgumentException(sprintf('%s-unlikeItem() called without a content id set in state.', get_class($this)));
		}

		// Assert that the content id is set.
		if (empty($userId))
		{
			throw new InvalidArgumentException(sprintf('%s-unlikeItem() called without a user identifier set in state.', get_class($this)));
		}

		// Check if the content type is set.
		if (empty($contentType))
		{
			// Get the content type for the id.
			$results = $this->getTypes($contentId);

			// Assert that the content type was found.
			if (empty($results[$contentId]))
			{
				throw new UnexpectedValueException(sprintf('%s->unlikeItem() could not find the content type for item %s.', get_class($this), $contentId));
			}

			// Set the content type alias.
			$contentType = $results[$contentId]->type;
		}

		if ($this->existsItem($contentId))
		{
			$item = $this->factory->getContent($contentType)->load($contentId);

			$item->unlike($userId);

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to update one item
	 *
	 * @return  JContent  A JContent object
	 *
	 * @since   1.0
	 * @throws  UnexpectedValueException
	 */
	public function createItem()
	{
		// Get the content type.
		$contentType = $this->state->get('content.type');

		// Check if the content type is set.
		if (empty($contentType))
		{
			throw new UnexpectedValueException(sprintf('%s->createItem() could not find the content type.', get_class($this)));
		}

		// Get new content
		$content = $this->factory->getContent($contentType);

		// Get fields for new content and check them
		$fields = $this->state->get('content.fields');
		if (empty($fields))
		{
			throw new UnexpectedValueException('Missing fields for new object');
		}

		// Get each field for the new content
		$fieldsArray = preg_split('#[\s,]+#', $fields, null, PREG_SPLIT_NO_EMPTY);

		foreach ($fieldsArray as $key => $fieldName)
		{
			$field = null;
			if ($this->state->exists('fields.' . $fieldName))
			{
				$field = $this->state->get('fields.' . $fieldName);
			}

			if ($field == null)
			{
				throw new UnexpectedValueException('Missing field ' . $fieldName);
			}

			$content->__set($fieldName, $field);
		}

		// Create content
		$content = $content->create();

		return $content;
	}

	/**
	 * Map two contnet
	 *
	 * @param   string   $content_id1  The id of content 1
	 * @param   array    $content_ids  An array of content ids to map
	 * @param   boolean  $updateAll    Update all map fields or only one
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function map($content_id1, $content_ids, $updateAll = false)
	{
		$contentType = $this->state->get('content.type');

		// Check if the content type is set.
		if (empty($contentType))
		{
			throw new UnexpectedValueException(sprintf('%s->getItem() could not find the content type.', get_class($this)));
		}

		$query = $this->db->getQuery(true);

		// Delete existing mappings
		if ($updateAll == true)
		{
			$query->delete();
			$query->from($this->db->quoteName('#__content_' . $contentType . '_map'));
			$query->where($this->db->quoteName('content_id') . ' = ' . (int) $content_id1);
			$this->db->setQuery($query);
			$this->db->execute();
			$query->clear();
		}

		$query->insert($this->db->quoteName('#__content_' . $contentType . '_map'));
		$query->columns(array($this->db->quoteName('content_id'), $this->db->quoteName($contentType . '_id')));
		foreach ($content_ids as $key => $content_id)
		{
			$query->values($content_id1 . ', ' . $content_id);
		}
		$this->db->setQuery(str_replace('INSERT INTO', 'INSERT IGNORE INTO', $query));

		try
		{
			$this->db->execute();
		}
		catch (Exception $er)
		{
			return false;
		}

		return true;
	}

	/**
	 * Unmap two contnet
	 *
	 * @param   string  $content_id1  The id of content 1
	 * @param   string  $content_id2  The id of content 2
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function unmap($content_id1, $content_id2 = null)
	{
		$contentType = $this->state->get('content.type');

		// Check if the content type is set.
		if (empty($contentType))
		{
			// Get the content type for the id.
			if (!is_null($content_id2))
			{
				$results = $this->getTypes($content_id2);

				// Assert that the content type was found.
				if (empty($results[$content_id2]))
				{
					throw new UnexpectedValueException(sprintf('%s->getItem() could not find the content type for item %s.', get_class($this), $content_id2));
				}

				// Set the content type alias.
				$contentType = $results[$content_id2]->type;
			}
			else
			{
				throw new UnexpectedValueException(sprintf('%s->getItem() could not find the content type for item %s.', get_class($this), $content_id2));
			}
		}

		$query = $this->db->getQuery(true);
		$query->clear();
		$query->delete();
		$query->from($this->db->quoteName('#__content_' . $contentType . '_map'));
		$query->where($this->db->quoteName('content_id') . '=' . $content_id1);
		if (!is_null($content_id2))
		{
			$query->where($this->db->quoteName('tag_id') . '=' . $content_id2);
		}

		print_r($query->__toString());
		die();

		$this->db->setQuery($query);

		try
		{
			$this->db->execute();
		}
		catch (Exception $er)
		{
			return false;
		}

		return true;
	}
}
