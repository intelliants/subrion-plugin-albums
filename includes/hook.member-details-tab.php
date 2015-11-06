<?php
//##copyright##

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	// create virtual field to display link to album
	if ('view_member' == $iaView->name())
	{
		$iaSmarty = &$iaCore->iaView->iaSmarty;

		$memberInfo = $iaSmarty->getTemplateVars('item');

		// show link only when user has photos
		if ($iaDb->exists('`member_id` = :member', array('member' => $memberInfo['id']), 'albums'))
		{
			$album = false;
			$albums = $iaDb->all(iaDb::ALL_COLUMNS_SELECTION, "`member_id` = {$memberInfo['id']}", null, null, 'albums');

			$sections = $iaSmarty->getTemplateVars('sections');
			$iaSmarty->assign('sections', $sections);


			$iaSmarty->assign('album', $album);
			$iaSmarty->assign('albums', $albums);
			$iaSmarty->assign('is_manage_albums', false);
			$iaSmarty->assign('username', $memberInfo['username']);

			$tabs_content['albums'] = $iaSmarty->fetch(IA_PLUGINS . 'albums/templates/front/albums-list.tpl');

			$iaSmarty->assign('sections', $sections);
			$iaSmarty->assign('tabs_content', $tabs_content);
		}
	}
}