{if !$in_album}
    {if $is_gallery && $core.config.album_gallery_all_photos}
        {include 'module:albums/photos-list.tpl'}
    {else}
        {include 'module:albums/albums-list.tpl'}
    {/if}
{else}
    {if !$photos}
        <div class="alert alert-info">{lang key='no_photos'}</div>
    {else}
        {include 'module:albums/block.album-info.tpl'}
        {if $photos}
            <div class="album-photos">
                <h3 class="title">{lang key='photos'}</h3>
                {include 'module:albums/photos-list.tpl'}
            </div>
        {/if}
    {/if}
{/if}
