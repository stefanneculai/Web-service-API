<?php

/**
 * @package     WebService.Model
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Web Service Api 'content' Model class.
 *
 * @package     WebService.Model
 * @subpackage  Model
 * @since       1.0
 */
class WebServiceModelContent extends JModelBase
{
	/**
	 * Get data from DB
	 *
	 * @param   array  $fields  List of the fields to be returned
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function getData($fields = null)
	{
		$factory = JContentFactory::getInstance();

		$content = $factory->getContent($this->state->get('type'))->load('1');

		$data = $this->pruneFields(array($content), $fields);

		return $data;
	}

		/**
         * Prunes fields in an array of JContent objects to a set list.
         *
         * @param   array  $list    An array of Jcontent.
         * @param   array  $fields  An array of the field names to preserve (strip all others).
         *
         * @return  void
         *
         * @since   1.0
         */
		protected function pruneFields($list, $fields)
		{
			if ($fields)
			{
				// Flip the fields so we can find the intersection by the array keys.
				$fields = array_flip($fields);

				/* @var $object JContent */
				foreach ($list as $key => $object)
				{

					// Suck out only the fields we want from the object dump.
					$list[$key] = array_uintersect_assoc(
							(array) $object->dump(), $fields,
							create_function(null, 'return 0;')
							);
				}
			}
			else
			{
				foreach ($list as $key => $object)
				{

					// Suck out only the fields we want from the object dump.
					$list[$key] = (array) $object->dump();
				}
			}

			return $list;
		}
}
