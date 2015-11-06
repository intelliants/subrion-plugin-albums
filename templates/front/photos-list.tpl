<div class="row">
	{foreach $photos as $photo}
		<div class="col-md-4">
			{include file="{$smarty.const.IA_PLUGINS}albums/templates/front/photo-small.tpl"}
			{if $photo@iteration % 3 == 0}
				</div>
				<div class="row">
			{/if}
		</div>
	{/foreach}	
</div>
