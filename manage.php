<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2015 Intelliants, LLC <http://www.intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link http://www.subrion.org/
 *
 ******************************************************************************/

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	if (!iaUsers::hasIdentity())
	{
		return iaView::accessDenied();
	}

	$iaDb->setTable('albums');

	$iaUtil = $iaCore->factory('util');
	$iaUsers = $iaCore->factory('users');
	$iaPicture = $iaCore->factory('picture');

	$albumId = null;
	$albumData = null;

	if (isset($_POST['action']))
	{
		$error = false;
		$messages = array();

		switch($_POST['action'])
		{
			case 'edit_album':
			case 'add_album':
				$data = array(
					'title' => trim($_POST['title']),
					'description' => $_POST['description'],
					'date' => date(iaDb::DATETIME_SHORT_FORMAT),
					'member_id' => iaUsers::getIdentity()->id
				);

				if (empty($data['title']))
				{
					$error = true;
					$messages[] = iaLanguage::get('title_incorrect');
				}
				else
				{
					// title exists
					if ($iaDb->exists('`member_id` = :member AND `title` = :title', array('member' => iaUsers::getIdentity()->id, 'title' => $data['title']))
						&& $_POST['action'] != 'edit_album')
					{
						$error = true;
						$messages[] = iaLanguage::get('title_exists');
					}
					else
					{
						if ($_POST['action'] == 'edit_album')
						{
							$albumId = (int)$_POST['album_id'];
							if ($iaDb->exists(iaDb::convertIds($albumId)))
							{
								unset($data['date']);

								$iaDb->update($data, iaDb::convertIds($albumId));
								$error = false;
								$messages[] = iaLanguage::get('saved');
							}
							else
							{
								$error = true;
								$messages[] = iaLanguage::get('album_not_exists');
							}
						}
						else
						{
							$iaDb->insert($data);

							$error = false;
							$messages[] = iaLanguage::get('album_created');
						}
					}
				}

				$iaView->setMessages($messages, $error ? iaView::ERROR : iaView::SUCCESS);

				break;

			case 'remove_album':
				$albumData = (int)$_POST['album_id'];
				$albumData = $iaDb->one_bind(iaDb::ID_COLUMN_SELECTION, '`id` = :id AND `member_id` = :member', array('id' => $albumData, 'member' => iaUsers::getIdentity()->id));

				if ($albumData)
				{
					$photos = $iaDb->all(array('id', 'path'), "`member_id`=" . iaUsers::getIdentity()->id . " AND `album_id`=$albumData", null, null, 'albums_photos');

					if ($photos)
					{
						$photosId = array();
						foreach ($photos as $entry)
						{
							$photosId[] = $entry['id'];

							$iaPicture->delete($entry['path']);
						}
						$photosId = implode(',', $photosId);
						$iaDb->delete("`id` IN($photosId)", 'albums_photos');
					}

					$iaDb->delete(iaDb::convertIds($albumData));

					$iaView->setMessages(iaLanguage::get('album_removed'), iaView::SUCCESS);

					iaUtil::go_to(IA_URL . 'profile/albums/');
				}

				break;

			case 'add_img':
				$albumId = (int)$_POST['album_id'];
				$where = sprintf('`member_id` = %d AND `id` = %d', iaUsers::getIdentity()->id, $albumId);
				$photo = isset($_FILES['photo']) ? $_FILES['photo'] : null;

				$albumData = $iaDb->row(array('id', 'cover'), $where);
				$currentPhotosCount = (int)$iaDb->one(iaDb::STMT_COUNT_ROWS, $where);

				if (empty($albumData))
				{
					$error = true;
					$messages[] = iaLanguage::get('album_invalid');
				}
				if ($currentPhotosCount >= $iaCore->get('album_num_photos'))
				{
					$error = true;
					$messages[] = iaLanguage::get('photo_limit');
				}
				if (empty($photo) || $photo['error'])
				{
					$error = true;
					$messages[] = iaLanguage::get('choose_photo');
				}
				if (!$error && !in_array($photo['type'], array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png')))
				{
					$error = true;
					$messages[] = iaLanguage::get('unsupported_image_type');
				}

				if (!$error)
				{
					$path = $iaUtil->getAccountDir(iaUsers::getIdentity()->username);
					$tok = 'photo_' . iaUtil::generateToken();

					$imgThumbDimension = explode(',', $iaCore->get('album_thumb_dim'));
					$imgDimension = explode(',', $iaCore->get('album_dim'));

					$imageInfo = array(
						'thumb_width' => $imgThumbDimension[0],
						'thumb_height' => $imgThumbDimension[1],
						'image_width' => $imgDimension[0],
						'image_height' => $imgDimension[1],
						'resize_mode' => iaPicture::CROP
					);

					$name = $iaPicture->processImage($photo, $path, $tok, $imageInfo);

					$data = array(
						'title' => trim($_POST['title']),
						'path' => $name,
						'member_id' => iaUsers::getIdentity()->id,
						'status' => ($iaCore->get('album_autoapprove') ? iaCore::STATUS_ACTIVE : iaCore::STATUS_APPROVAL),
						'album_id' => $albumData['id']
					);
					$data['id'] = (int)$iaDb->insert($data, array('date' => iaDb::FUNCTION_NOW), 'albums_photos'); // Insert and get photo ID

					// update album cover if empty
					if (empty($albumData['cover']))
					{
						$iaDb->update(array('cover' => $name), iaDb::convertIds($albumData['id']));

						$iaView->setMessages(iaLanguage::get('album_photo_added'), iaView::SUCCESS);
					}
				}
				else
				{
					$iaView->setMessages($messages);
				}
		}
	}

	$albums = $iaDb->all(iaDb::ALL_COLUMNS_SELECTION, iaDb::convertIds(iaUsers::getIdentity()->id, 'member_id'));

	if ($iaCore->requestPath)
	{
		$albumId = (int)$iaCore->requestPath[0];
		$albumData = null;

		foreach ($albums as $entry)
		{
			if ($entry['id'] == $albumId)
			{
				$albumData = $entry;
			}
		}

		if ($albumData)
		{
			/* ACTIONS */
			$ids = '';
			$checked = array();
			if (isset($_POST['checked']))
			{
				$checked = $_POST['checked'];
				$ids = implode(',', $checked);
				array_walk($checked, 'intval');
			}
			if ($checked)
			{
				$messages = array();
				$error = false;

				switch (true)
				{
					case isset($_POST['cover']):
						$coverImage = $iaDb->one_bind('path', '`id` = :id AND `member_id` = :member', array('id' => $checked[0], 'member' => iaUsers::getIdentity()->id), 'albums_photos');

						$iaDb->update(array('cover' => $coverImage), iaDb::convertIds($albumId));
						$albumData['cover'] = $coverImage;

						break;

					case isset($_POST['delete']):
						$where = sprintf('`id` IN(%s) AND `member_id` = %d', $ids, iaUsers::getIdentity()->id);

						if ($photos = $iaDb->all(array('path'), $where, null, null, 'albums_photos'))
						{
							if ($iaDb->delete('`id` IN (' . $ids . ')', 'albums_photos'))
							{
								foreach ($photos as $entry)
								{
									$iaPicture->delete($entry['path']);

									// album's cover image removed
									if ($entry['path'] == $albumData['cover'])
									{
										$iaDb->update(array('cover' => ''), iaDb::convertIds($albumId));
										$albumData['cover'] = '';
									}
								}
							}
						}

						break;

					case isset($_POST['move']):
						$moveId = (int)$_POST['move_to'];
						$move = $iaDb->one(iaDb::ID_COLUMN_SELECTION, "`member_id`=" . iaUsers::getIdentity()->id . " AND `id`=$moveId");
						$photos = $iaDb->all(iaDb::ID_COLUMN_SELECTION, "`member_id`=" . iaUsers::getIdentity()->id . " AND `id` IN($ids)", null, null, 'albums_photos');

						if ($move)
						{
							$iaDb->update(array('album_id' => $moveId), '`id` IN (' . $ids . ')', 0, 'albums_photos');
							$messages[] = iaLanguage::get('photos_moved');
						}

						break;

					default:
						$messages[] = iaLanguage::get('invalid_parameters');
						$error = true;
				}

				$iaView->setMessages($messages, $error ? iaView::ERROR : iaView::SUCCESS);
			}

			$myPhotos = $iaDb->all(iaDb::ALL_COLUMNS_SELECTION, "`member_id`= " . iaUsers::getIdentity()->id . " AND `album_id` = {$albumId} ORDER BY `date`", 0, 0, 'albums_photos');

			$iaView->assign('photos', $myPhotos);

			iaBreadcrumb::replaceEnd(iaLanguage::get('page_title_my_albums'), IA_URL . 'profile/albums/');
			iaBreadcrumb::toEnd(iaSanitize::html($albumData['title']));

			$pageActions[] = array(
				'icon' => 'icon-arrow-left',
				'title' => iaLanguage::get('back_to_albums'),
				'url' => IA_URL . 'profile/albums/',
				'classes' => 'btn-primary'
			);

			$pageActions[] = array(
				'icon' => 'icon-edit',
				'title' => iaLanguage::get('album_edit'),
				'url' => IA_SELF . '#create-album',
				'classes' => 'btn-success" data-toggle="modal'
			);
		}
	}
	else
	{
		$pageActions[] = array(
			'icon' => 'icon-plus-sign',
			'title' => iaLanguage::get('album_add'),
			'url' => IA_SELF . '#create-album',
			'classes' => 'btn-success" data-toggle="modal'
		);
	}

	$iaView->set('actions', $pageActions);

	$iaView->assign('albums', $albums);
	$iaView->assign('header', iaLanguage::get('manage_photos') . ($albumData ? ', ' . $albumData['title'] : ''));
	$iaView->assign('in_album', (bool)$albumId);
	$iaView->assign('is_manage_albums', true);
	$iaView->assign('album', $albumData);

	$iaView->display('manage');

	$iaDb->resetTable();
}