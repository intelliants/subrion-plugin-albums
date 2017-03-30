<div class="modal fade" id="move-photos">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{lang key='move_to'}</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <select id="modal-move-to" class="form-control">
                        {foreach $albums as $member_album}
                            {if $member_album.id != $album.id}
                                <option value="{$member_album.id}" {if $member_album.id == $album.id}selected="selected"{/if}>{$member_album.title|escape:'html'}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>

                <button type="button" class="btn btn-default" data-dismiss="modal">{lang key='cancel'}</button>
                <button id="move-photo" type="submit" class="btn btn-primary">{lang key='move'}</button>
            </div>
        </div>
    </div>
</div>