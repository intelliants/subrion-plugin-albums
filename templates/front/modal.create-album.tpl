<div class="modal fade" id="create-album">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">{if $album}{lang key='album_edit'}{else}{lang key='album_add'}{/if}</h4>
			</div>
			<div class="modal-body">
				<form method="post" enctype="multipart/form-data">
					{preventCsrf}
					<div class="form-group">
						<label for="album-title">{lang key='title'}:</label>
						<input class="form-control" type="text" id="album-title" name="title" value="{$album.title|escape:'html'|default:''}">
					</div>
					<div class="form-group">
						<label for="album-desc">{lang key='description'}:</label>
						<textarea id="album-desc" class="form-control" name="description" rows="3">{$album.description|escape:'html'|default:''}</textarea>
					</div>

					<input type="hidden" name="action" value="{if $album}edit_album{else}add_album{/if}">
					<input type="hidden" name="album_id" value="{$album.id}">

					<button type="button" class="btn btn-default" data-dismiss="modal">{lang key='cancel'}</button>
					<button type="submit" class="btn btn-primary">{lang key='save'}</button>
				</form>
			</div>
		</div>
	</div>
</div>