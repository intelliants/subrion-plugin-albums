{if !$in_album}
    {include 'module:albums/albums-list.tpl'}
{else}
    {include 'module:albums/block.album-info.tpl'}
    {if $photos}
        <div class="album-photos">
            <h3 class="title">{lang key='photos'}</h3>

            <form method="post" id="album-photos-form">
                {preventCsrf}

                {include 'module:albums/photos-list.tpl'}

                <div class="album-actions">
                    <button type="submit" name="cover" class="btn btn-xs btn-info" id="buttoncover" disabled>
                        <i class="icon-camera"></i>
                        {lang key='cover'}
                    </button>
                    <button type="submit" name="delete" class="btn btn-xs btn-danger delete" id="remove-photo" disabled>
                        <i class="icon-remove"></i>
                        {lang key='delete'}
                    </button>
                    {if count($albums) > 1}
                        <button type="button" name="move-link" id="move-link" class="btn btn-xs" data-toggle="modal" data-target="#move-photos" disabled>
                            <i class="icon-share-alt"></i>
                            {lang key='move'}
                        </button>

                        <input type="hidden" name="move_to" id="move-to">
                        <input type="submit" name="move" class="hidden">
                    {/if}
                </div>
            </form>
        </div>
    {/if}
{/if}

{include 'module:albums/modal.create-album.tpl'}
{include 'module:albums/modal.upload-photo.tpl'}
{include 'module:albums/modal.move-photos.tpl'}

{ia_add_media files='js:_IA_URL_modules/albums/js/frontend/album, css:_IA_URL_modules/albums/templates/front/css/style'}