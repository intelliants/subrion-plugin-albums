<?php
//##copyright##

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