/** @src: http://stackoverflow.com/a/7141354 */
$.fn.exists = function () {
	return this.length !== 0;
}


$('textarea:not([readonly])').tabby();

$('#menu').append('<div class="elem" id="notify-box" data-expire="true">Info</div>');

$('#notify-box').on('click', function() {
	$(this).remove();
});

if ($('#notify-box[data-expire]').exists())
	setTimeout(function() {
		$('#notify-box[data-expire]').fadeOut('1000', function() {
			$('#notify-box[data-expire]').remove();
		});
	}, 4000)
