jQuery(document).ready(function($) {

	// Tabs
	$('#pows-wrapper .pows-pane:first').show();
	$('#pows-tabs a').click(function() {
		$('.pows-message').hide();
		$('#pows-tabs a').removeClass('pows-current');
		$(this).addClass('pows-current');
		$('#pows-wrapper .pows-pane').hide();
		$('#pows-wrapper .pows-pane').eq($(this).index()).show();
	});
});