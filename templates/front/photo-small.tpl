<div class="thumbnail">
    <a href="{ia_image file=$photo.path url=true type='large'}" rel="ia_lightbox[{$album.title|default:0}]" title="{$photo.title|escape:'html'}">
        {ia_image file=$photo.path title=$photo.title}
        {if $photo.status == 'rejected'}
            <span class="label photo-label label-warning">{lang key=$photo.status}</span>
        {elseif $photo.status == 'approval'}
            <span class="label photo-label">{lang key=$photo.status}</span>
        {/if}
    </a>
    {if isset($is_manage_albums) && $is_manage_albums}
        <input type="checkbox" name="checked[]" value="{$photo.id}" class="photo-checkbox">
    {/if}
</div>
