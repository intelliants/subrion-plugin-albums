<div class="modal fade" id="upload-photo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{lang key='upload_photo'}</h4>
            </div>
            <div class="modal-body">
                <form id="photo_form" method="post" enctype="multipart/form-data">
                    {preventCsrf}
                    <div class="form-group">
                        <label for="photo">{lang key='upload_hint'}</label>
                        <input type="file" name="photo" id="photo" placeholder="{lang key='upload_photo'}">
                    </div>
                    <div class="form-group">
                        <label for="photo-title">{lang key='photo_title'}</label>
                        <input type="text" class="form-control" id="photo-title" name="title" placeholder="{lang key='photo_title'}">
                    </div>

                    <input type="hidden" name="action" value="add_img">
                    <input type="hidden" name="album_id" id="album_id" value="{$album.id}">

                    <button type="button" class="btn btn-default" data-dismiss="modal">{lang key='cancel'}</button>
                    <button id="upload-photo-btn" type="submit" class="btn btn-primary">{lang key='upload'}</button>
                </form>
            </div>
        </div>
    </div>
</div>
