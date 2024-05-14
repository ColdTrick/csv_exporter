import 'jquery';

$(document).on('change', '#csv-exporter-type-subtype', function() {
	var $form = $(this).closest('form');
	
	$form.find('input[name="preview"]').val(0);
	$form.submit();
});

$(document).on('change', '#csv-exporter-time', function() {
	if ($(this).val() === 'range') {
		$('#csv-exporter-range').closest('.elgg-field').show();
	} else {
		$('#csv-exporter-range').closest('.elgg-field').hide();
	}
});
