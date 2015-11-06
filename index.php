<?php
//##copyright##

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$albumId = false;
	$isGallery = false;

	if ($iaView->name() == 'albums' && $iaCore->get('album_gallery_page'))
	{
		$username = $iaCore->get('album_gallery_username');
		$isGallery = true;
	}
	elseif (empty($iaCore->requestPath))
	{
		return iaView::errorPage(iaView::ERROR_NOT_FOUND);
	}
	else
	{
		$username = iaSanitize::sql($iaCore->requestPath[0]);
	}

	if (empty($username))
	{
		return iaView::errorPage(iaView::ERROR_NOT_FOUND);
	}

	$album = array();

	$iaUsers = $iaCore->factory('users');
	$member = $iaUsers->getInfo($username, 'username');

	$iaView->assign('username', $username);

	if ($isGallery)
	{
		$albumId = empty($iaCore->requestPath[0]) ? false : (int)$iaCore->requestPath[0];
	}
	else
	{
		$albumId = empty($iaCore->requestPath[1]) ? false : (int)$iaCore->requestPath[1];
	}

	$alpha = strtoupper(substr($username, 0, 1));

	if (!$isGallery)
	{
		iaBreadcrumb::replaceEnd(iaLanguage::get('members'), IA_URL . 'members' . IA_URL_DELIMITER);
		iaBreadcrumb::toEnd($member['fullname'] ? $member['fullname'] : $username, IA_URL . 'members' . IA_URL_DELIMITER . 'info' . IA_URL_DELIMITER . $username . '.html');
	}

	if ($albumId)
	{
		$album = $iaDb->row(iaDb::ALL_COLUMNS_SELECTION, iaDb::convertIds($albumId), 'albums');
		$photos = array();

		if ($album)
		{
			iaBreadcrumb::toEnd($album['title']);

			$photos = $iaDb->all(iaDb::ALL_COLUMNS_SELECTION, "`member_id` = {$member['id']} AND `album_id` = '{$albumId}' AND `status` = '" . iaCore::STATUS_ACTIVE . "'", 0, $iaCore->get('album_photos_perpage', 10), 'albums_photos');
		}

		if ($photos) {
			$openGraph = array(
				'title' => $album['title'],
				'url' => IA_SELF,
				'description' => iaSanitize::html($album['description']),
				'image' => IA_CLEAR_URL . 'uploads/' . $album['cover']
			);
			$iaView->set('og', $openGraph);
		}

		$iaView->assign('album', $album);
		$iaView->assign('photos', $photos);
	}
	else
	{
		iaBreadcrumb::toEnd(iaLanguage::get('albums'));

		if ($iaCore->get('album_gallery_all_photos') && $isGallery)
		{
			$photos = $iaDb->all(iaDb::ALL_COLUMNS_SELECTION, "`member_id` = {$member['id']} AND `status` = '" . iaCore::STATUS_ACTIVE . "'", 0, 0, 'albums_photos');
			$iaView->assign('photos', $photos);
		}
		else
		{
			$albums = $iaDb->all(iaDb::ALL_COLUMNS_SELECTION, "`member_id` = {$member['id']}", 0, 0, 'albums');
			$iaView->assign('albums', $albums);
		}
	}

	$iaView->assign('in_album', (bool)$albumId);
	$iaView->assign('is_manage_albums', false);
	$iaView->assign('is_gallery', $isGallery);

	$iaView->display('index');
}