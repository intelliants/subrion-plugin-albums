{if !$in_album}
	{if $is_gallery && $core.config.album_gallery_all_photos}
		{include file="{$smarty.const.IA_PLUGINS}albums/templates/front/photos-list.tpl"}
	{else}
		{include file="{$smarty.const.IA_PLUGINS}albums/templates/front/albums-list.tpl"}
	{/if}
{else}
	{if !$photos}
		<div class="alert alert-info">{lang key='no_photos'}</div>
	{else}
		{include file="{$smarty.const.IA_PLUGINS}albums/templates/front/block.album-info.tpl"}
		{if $photos}
			<div class="album-photos">
				<h3 class="title">{lang key='photos'}</h3>
				{include file="{$smarty.const.IA_PLUGINS}albums/templates/front/photos-list.tpl"}
			</div>
		{/if}
	{/if}
{/if}

{ia_print_css files='_IA_URL_plugins/albums/templates/front/css/style'}
