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
    // create virtual field to display link to album
    if ('view_member' == $iaView->name()) {
        $iaSmarty = &$iaCore->iaView->iaSmarty;

        $memberInfo = $iaSmarty->getTemplateVars('item');

        // show link only when user has photos
        if ($iaDb->exists('`member_id` = :member', ['member' => $memberInfo['id']], 'albums')) {
            $album = false;
            $albums = $iaDb->all(iaDb::ALL_COLUMNS_SELECTION, "`member_id` = {$memberInfo['id']}", null, null,
                'albums');

            $sections = $iaSmarty->getTemplateVars('sections');
            $iaSmarty->assign('sections', $sections);

            $iaSmarty->assign('album', $album);
            $iaSmarty->assign('albums', $albums);
            $iaSmarty->assign('is_manage_albums', false);
            $iaSmarty->assign('username', $memberInfo['username']);

            $tabs_content['albums'] = $iaSmarty->fetch(IA_MODULES . 'albums/templates/front/albums-list.tpl');

            $iaSmarty->assign('sections', $sections);
            $iaSmarty->assign('tabs_content', $tabs_content);
        }
    }
}