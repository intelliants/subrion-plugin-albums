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
	if (array_key_exists('album_gallery_page', $configurationValues))
	{
		$galleryPageName = 'albums_gallery';

		$iaPage = $iaCore->factory('page', iaCore::ADMIN);

		if ($configurationValues['album_gallery_page'])
		{
			if (!$iaDb->exists('`name` = :name', array ('name' => $galleryPageName)))
			{
				$galleryPage = array (
					'name' => $galleryPageName,
					'group' => 5,
					'service' => false,
					'readonly' => true,
					'status' => iaCore::STATUS_ACTIVE,
					'alias' => 'gallery/',
					'extras' => 'albums',
					'menus' => array ('main')
				);

				foreach ($iaCore->languages as $langKey => $langTitle) {
					$galleryPage['titles'][$langKey] = 'Gallery';
				}

				$iaPage->insert($galleryPage);
			}
		}
		else
		{
			if ($galleryPage = $iaPage->getByName($galleryPageName, false))
			{
				$iaPage->delete($galleryPage['id']);
			}
		}
	}
}