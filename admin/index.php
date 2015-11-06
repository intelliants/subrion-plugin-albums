<?php
//##copyright##

if (iaView::REQUEST_JSON == $iaView->getRequestType())
{
	$iaAlbum = $iaCore->factoryPlugin('albums', iaCore::ADMIN, 'album');

	$iaDb->setTable(iaAlbum::getTable());

	$output = array('result' => false, 'message' => iaLanguage::get('invalid_parameters'));

	switch ($pageAction)
	{
		case iaCore::ACTION_READ:
			$params = array();
			if (isset($_GET['text']) && $_GET['text'])
			{
				$stmt = '(`title` LIKE :text OR `body` LIKE :text)';
				$iaDb->bind($stmt, array('text' => '%' . $_GET['text'] . '%'));

				$params[] = $stmt;
			}

			$output = $iaAlbum->gridRead($_GET,
				array('title', 'path', 'date', 'member_id', 'status'),
				array('status' => 'equal'),
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

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$iaView->grid('_IA_URL_plugins/albums/js/admin/grid');
}