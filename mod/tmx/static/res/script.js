/** @src: http://stackoverflow.com/a/7141354 */
$.fn.exists = function () {
	return this.length !== 0;
}

$('#menu').append('<div class="elem" id="notify-box">Info</div>');


$('#notify-box').on('click', function() {
	$(this).remove();
});

if ($('#notify-box[data-expire]').exists())
	setTimeout(function() {
		$('#notify-box[data-expire]').remove();
	}, 5000)
