$(function()
{
	var $link = $('#move-link');
	var move_link = $link.attr('href');

	$('#create-album, #upload-photo, #move-photos').appendTo('body');

	$link.attr('href', '');
	$('#move-to').val($('#modal-move-to').val());

	$('#remove-album').on('click', function(e)
	{
		confirm(_f('album_del_confirm')) || e.preventDefault();
	});

	$('#upload-photo-btn').on('click', function(e)
	{
		$(this).addClass('disabled').html('<span class="fa fa-refresh"></span>');
	});

	$('#remove-photo').on('click', function(e)
	{
		return confirm(_f('rm_photo'));
	});

	$('#move-photo').on('click', function(e)
	{
		$('input[name="move"]').click();
	});

	$('#modal-move-to').on('change', function()
	{
		$('#move-to').val($(this).val());
	});

	$('input[name="checked[]"]').on('change', function(e)
	{
		e.preventDefault();

		var selectedImagesCount = $('input[name="checked[]"]:checked').length;

		if (selectedImagesCount > 0)
		{
			$('#remove-photo').prop('disabled', false);

			$link.prop('disabled', false).attr('href', move_link);
			$('#buttoncover').prop('disabled', 1 != selectedImagesCount);
		}
		else
		{
			$('#remove-photo, #buttoncover').prop('disabled', true);

			$link.prop('disabled', true).attr('href', '');
		}
	});
});