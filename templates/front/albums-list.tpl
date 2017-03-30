{if !empty($albums)}
    {if $is_manage_albums}
        {assign albums_baseurl "{$smarty.const.IA_URL}profile/albums/"}
    {else}
        {if isset($is_gallery) && $is_gallery}
            {assign albums_baseurl "{$smarty.const.IA_URL}albums/"}
        {else}
            {assign albums_baseurl "{$smarty.const.IA_URL}albums/{$username}/"}
        {/if}
    {/if}
    <div class="row">
        {foreach $albums as $album}
            <div class="col-md-4">
                <div class="thumbnail">
                    <a href="{$albums_baseurl}{$album.id}-album/" title="{$album.title|escape:'html'}">{ia_image file=$album.cover title=$album.title}</a>
                    <div class="caption">
                        <h4>{$album.title|escape:'html'}</h4>
                    </div>
                </div>
            </div>
            {if $album@iteration % 3 == 0}
                </div>
                <div class="row">
            {/if}
        {/foreach}
    </div>
{else}
    <div class="alert alert-info">{lang key='no_albums'}</div>
{/if}

{ia_print_css files='_IA_URL_modules/albums/templates/front/css/style'}

{ia_add_js}
$(function()
{
    $('a[data-toggle="tooltip"]').tooltip();
});
{/ia_add_js}