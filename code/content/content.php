<?php

/**
 * @package     WebService.Content
 * @subpackage  Content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Web Service Api 'content' class.
 *
 * @package     WebService.Content
 * @subpackage  Content
 * @since       1.0
 */
class WebServiceContent extends JContent
{
/**
	 * Method to like the content object.
	 *
	 * @param   integer  $user_id  The id of the user
	 *
	 * @return  JContent  The content object.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function like($user_id = null)
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Build the query to create the like record.
		$query = $this->db->getQuery(true);
		$query->insert($this->db->qn('#__content_likes'));
		$query->columns(array('content_id', 'user_id'));

		if ($user_id == null)
		{
			$query->values((int) $this->content_id . ',' . (int) $this->user->get('id'));
		}
		else
		{
			$query->values((int) $this->content_id . ',' . (int) $user_id);
		}

		// Create the like record.
		$this->db->setQuery($query);
		$this->db->query();

		// Build the query to update the likes count.
		$query = $this->db->getQuery(true);
		$query->update($this->db->qn('#__content'));
		$query->set('likes = likes+1');
		$query->where('content_id = ' . (int) $this->content_id);

		// Update the likes count.
		$this->db->setQuery($query);
		$this->db->query();

		// Update the likes.
		$this->likes += 1;

		return $this;
	}

	/**
	 * Method to unlike the content object.
	 *
	 * @param   integer  $user_id  The id of the user
	 *
	 * @return  JContent  The content object.
	 *
	 * @since   12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function unlike($user_id = null)
	{
		// Assert the object is loaded.
		$this->assertIsLoaded();

		// Build a query to delete the like record.
		$query = $this->db->getQuery(true);
		$query->delete('#__content_likes');
		$query->where('content_id = ' . (int) $this->content_id);

		if ($user_id == null)
		{
			$query->where('user_id = ' . (int) $this->user->get('id'));
		}
		else
		{
			$query->where('user_id = ' . (int) $user_id);
		}

		// Delete the like record.
		$this->db->setQuery($query);
		$this->db->query();

		// Build the query to update the likes count.
		$query = $this->db->getQuery(true);
		$query->update($this->db->qn('#__content'));
		$query->set('likes = likes-1');
		$query->where('likes > 0');
		$query->where('content_id = ' . (int) $this->content_id);

		// Update the likes count.
		$this->db->setQuery($query);
		$this->db->query();

		// Update the likes.
		$this->likes -= 1;

		return $this;
	}
}
