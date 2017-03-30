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

if (iaView::REQUEST_JSON == $iaView->getRequestType()) {
    $iaAlbum = $iaCore->factoryPlugin('albums', iaCore::ADMIN, 'album');

    $iaDb->setTable(iaAlbum::getTable());

    $output = ['result' => false, 'message' => iaLanguage::get('invalid_parameters')];

    switch ($pageAction) {
        case iaCore::ACTION_READ:
            $params = [];
            if (isset($_GET['text']) && $_GET['text']) {
                $stmt = '(`title` LIKE :text OR `body` LIKE :text)';
                $iaDb->bind($stmt, ['text' => '%' . $_GET['text'] . '%']);

                $params[] = $stmt;
            }

            $output = $iaAlbum->gridRead($_GET,
                ['title', 'path', 'date', 'member_id', 'status'],
                ['status' => 'equal'],
                $params
            );

            break;

        case iaCore::ACTION_EDIT:
            $output = $iaAlbum->gridUpdate($_POST);

            break;

        case iaCore::ACTION_DELETE:
            $output = $iaAlbum->gridDelete($_POST);
    }

    $iaDb->resetTable();

    $iaView->assign($output);
}

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    $iaView->grid('_IA_URL_modules/albums/js/admin/grid');
}