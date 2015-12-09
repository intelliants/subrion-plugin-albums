{ia_block title="{lang key='album'}" name='album_info' classname='box-clear' collapsible=false}
	<div class="media">
		<div class="media-left">
			{printImage imgfile=$album.cover width=$core.config.thumb_w height=$core.config.thumb_h class='media-object'}
		</div>

		<div class="media-body">
			<h4 class="media-heading">{$album.title|escape:'html'}</h4>
			<p>{$album.description|escape:'html'}</p>

			{if isset($is_manage_albums) && $is_manage_albums}
				<form method="post" class="album-actions">
					{preventCsrf}
					<input type="hidden" name="action" value="remove_album">
					<input type="hidden" name="album_id" value="{$album.id}">

					<a href="{$smarty.const.IA_SELF}#upload-photo" class="btn btn-xs btn-info" data-toggle="modal">
						<i class="icon-upload"></i>
						{lang key='upload_photo'}
					</a>
					<a href="{$smarty.const.IA_SELF}#create-album" id="album_edit" class="btn btn-xs btn-info" data-toggle="modal">
						<i class="icon-edit"></i>
						{lang key='edit'}
					</a>
					<button type="submit" class="btn btn-xs btn-danger delete" id="remove-album">
						<i class="icon-remove"></i>
						{lang key='delete'}
					</button>
				</form>
			{/if}
		</div>
	</div>
{/ia_block}