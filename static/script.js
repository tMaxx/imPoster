$(document).on('click', 'div.frame a.switch', function(event) {
	event.preventDefault();
	$(this).parent().remove();
});
