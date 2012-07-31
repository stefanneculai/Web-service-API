<?php
/**
 * @package     WebService.Application
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * WebService 'content' Delete method.
 *
 * @package     WebService.Application
 * @subpackage  Controller
 * @since       1.0
 */
class WebServiceControllerV1JsonTagsDelete extends WebServiceControllerV1JsonBaseDelete
{
	/**
	 * Get the list of tags from input
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	protected function getTagList()
	{
		$name = $this->input->get->getString('name');
		$list = $this->input->get->getString('list');

		if (isset($name))
		{
			return array($name);
		}
		elseif (isset($list))
		{
			$tagList = explode(',', $list);

			foreach ($tagList as $key => $tag)
			{
				$tagList[$key] = trim($tag);
			}

			return $tagList;
		}

		return null;
	}

	/**
	 * Get the list with tag IDs from input
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	protected function getIDsList()
	{
		$ids = $this->input->get->getString('ids');
		if (isset($ids))
		{
			$idsList = explode(',', $ids);

			foreach ($idsList as $key => $tag)
			{
				$idsList[$key] = trim($tag);
			}

			return $idsList;
		}

		return null;
	}

	/**
	 * Controller logic
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function execute()
	{
		// Init
		$this->init();

		// Check for errors
		if ($this->app->errors->errorsExist() == true)
		{
			$this->app->setBody(json_encode($this->app->errors->getErrors()));
			$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
			return;
		}

		$app_id = $this->input->get->getString('application_id');
		if (isset($app_id))
		{
			// Check if application exists in database
			if ($this->itemExists($app_id, 'application'))
			{
				$modelState = $this->model->getState();
				$modelState->set('content.type', $this->type);

				$tagList = $this->getTagList();
				$idsList = $this->getIDsList();

				if (!is_null($tagList))
				{
					// Check if tags exist in database or they should be created
					foreach ($tagList as $key => $tag)
					{
						// Set where clause
						$this->setWhere(array($this->mapIn('name') => $tag));

						// Check if the tag already exist
						if ($this->model->countItems() > 0)
						{
							// Get the tag
							$data = $this->getContent();
							$data = array_values($data);
							$data = $data[0];
							$tag_id = $this->pruneFields($data, array('content_id'));

							if (array_key_exists('id', $tag_id))
							{
								$tag_id = $tag_id['id'];
							}

							$result = $this->model->unmap($app_id, $tag_id);
							if ($result == false)
							{
								$this->app->setBody(json_encode($result));
								return;
							}
						}
					}

					$this->app->setBody(json_encode(true));
				}
				elseif (!is_null($idsList))
				{
					$modelState = $this->model->getState();
					$modelState->set('content.type', $this->type);

					foreach ($idsList as $key => $id)
					{
						if ($this->model->existsItem($id))
						{
							$result = $this->model->unmap($app_id, $id);
							if ($result == false)
							{
								$this->app->setBody(json_encode($result));
								return;
							}
						}
					}

					$this->app->setBody(json_encode(true));
				}
				elseif (strcmp($this->id, '*') !== 0)
				{
					$result = $this->model->unmap($app_id, $this->id);
					$this->app->setBody(json_encode($result));
				}
				else
				{
					$result = $this->model->unmap($app_id);
					$this->app->setBody(json_encode($result));
				}
			}
			// Raise error
			else
			{
				$this->app->errors->addError('204', array('application_id', $app_id));
				$this->app->setBody(json_encode($this->app->errors->getErrors()));
				$this->app->setHeader('status', $this->app->errors->getResponseCode(), true);
				return;
			}
		}
		else
		{
			// Delete content from Database
			$data = $this->deleteContent();

			// Parse the returned code
			$this->parseData($data);
		}
	}

	/**
	 * Get content by id or all content
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 * @throws  Exception
	 */
	protected function getContent()
	{
		// Get content state
		$modelState = $this->model->getState();

		// Set content type that we need
		$modelState->set('content.type', $this->type);

		$modelState->set('list.offset', '0');
		$modelState->set('list.limit', '1');

		// Get content from Database
		try
		{
			$items = $this->model->getList();
		}
		catch (Exception $e)
		{
			throw $e;
		}

		return $items;
	}
}
