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

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    $albumId = false;
    $isGallery = false;

    if (($iaView->name() == 'albums' || $iaView->name() == 'albums_gallery') && $iaCore->get('album_gallery_page')) {
        $username = $iaCore->get('album_gallery_username');
        $isGallery = true;
    } elseif (empty($iaCore->requestPath)) {
        return iaView::errorPage(iaView::ERROR_NOT_FOUND);
    } else {
        $username = iaSanitize::sql($iaCore->requestPath[0]);
    }

    if (empty($username)) {
        return iaView::errorPage(iaView::ERROR_NOT_FOUND);
    }

    $album = [];

    $iaUsers = $iaCore->factory('users');
    $member = $iaUsers->getInfo($username, 'username');

    $iaView->assign('username', $username);

    if ($isGallery) {
        $albumId = empty($iaCore->requestPath[0]) ? false : (int)$iaCore->requestPath[0];
    } else {
        $albumId = empty($iaCore->requestPath[1]) ? false : (int)$iaCore->requestPath[1];
    }

    $alpha = strtoupper(substr($username, 0, 1));

    if (!$isGallery) {
        iaBreadcrumb::replaceEnd(iaLanguage::get('members'), IA_URL . 'members' . IA_URL_DELIMITER);
        iaBreadcrumb::toEnd($member['fullname'] ? $member['fullname'] : $username,
            IA_URL . 'members' . IA_URL_DELIMITER . 'info' . IA_URL_DELIMITER . $username . '.html');
    }

    if ($albumId) {
        $album = $iaDb->row(iaDb::ALL_COLUMNS_SELECTION, iaDb::convertIds($albumId), 'albums');
        $photos = [];

        if ($album) {
            iaBreadcrumb::toEnd($album['title']);

            $photos = $iaDb->all(iaDb::ALL_COLUMNS_SELECTION,
                "`member_id` = {$member['id']} AND `album_id` = '{$albumId}' AND `status` = '" . iaCore::STATUS_ACTIVE . "'",
                0, $iaCore->get('album_photos_perpage', 10), 'albums_photos');
        }

        if ($photos) {
            $openGraph = [
                'title' => $album['title'],
                'url' => IA_SELF,
                'description' => iaSanitize::html($album['description']),
                'image' => IA_CLEAR_URL . 'uploads/' . $album['cover']
            ];
            $iaView->set('og', $openGraph);
        }

        $iaView->assign('album', $album);
        $iaView->assign('photos', $photos);
    } else {
        iaBreadcrumb::toEnd(iaLanguage::get('albums'));

        if ($iaCore->get('album_gallery_all_photos') && $isGallery) {
            $photos = $iaDb->all(iaDb::ALL_COLUMNS_SELECTION,
                "`member_id` = {$member['id']} AND `status` = '" . iaCore::STATUS_ACTIVE . "'", 0, 0, 'albums_photos');
            $iaView->assign('photos', $photos);
        } else {
            $albums = !empty($member['id']) ? $iaDb->all(iaDb::ALL_COLUMNS_SELECTION, "`member_id` = {$member['id']}",
                0, 0, 'albums') : null;
            $iaView->assign('albums', $albums);
        }
    }

    $iaView->assign('in_album', (bool)$albumId);
    $iaView->assign('is_manage_albums', false);
    $iaView->assign('is_gallery', $isGallery);

    $iaView->display('index');
}