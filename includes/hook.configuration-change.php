<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2017 Intelliants, LLC <https://intelliants.com>
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
 * @link https://subrion.org/
 *
 ******************************************************************************/

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    if (array_key_exists('album_gallery_page', $configurationValues)) {
        $galleryPageName = 'albums_gallery';

        $iaPage = $iaCore->factory('page', iaCore::FRONT);

        if ($configurationValues['album_gallery_page']) {
            if (!$iaDb->exists('`name` = :name', ['name' => $galleryPageName], 'pages')) {
                $galleryPage = [
                    'name' => $galleryPageName,
                    'group' => 5,
                    'service' => false,
                    'readonly' => true,
                    'status' => iaCore::STATUS_ACTIVE,
                    'alias' => 'gallery/',
                    'module' => 'albums',
                    'menus' => 'main'
                ];

                foreach ($iaCore->languages as $langKey => $langTitle) {
                    $phrase = [
                        'key' => 'page_title_' . $iaDb->getNextId(iaPage::getTable(true)),
                        'original' => 'Gallery',
                        'value' => 'Gallery',
                        'category' => 'page',
                        'code' => $langKey,
                        'module' => 'albums'
                    ];

                    $iaDb->insert($phrase, null, iaLanguage::getTable());
                }

                $iaDb->insert($galleryPage, null, iaPage::getTable());
            }
        } else {
            if ($galleryPage = $iaPage->getByName($galleryPageName, false)) {
                $iaDb->delete(sprintf("`key` = 'page_title_%d'", $galleryPage['id']), iaLanguage::getTable());
                $iaDb->delete(sprintf("`id` = %d", $galleryPage['id']), $iaPage::getTable());
            }
        }
    }
}