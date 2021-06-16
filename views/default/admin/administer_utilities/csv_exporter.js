// CSV exporter js
define(['jquery', 'elgg'], function($, elgg) {

	$(document).on('change', '#csv-exporter-type-subtype', function() {
		$(this).closest('form').submit();
	});
	
	$(document).on('change', '#csv-exporter-time', function() {
		
		if ($(this).val() === 'range') {
			$('#csv-exporter-range').closest('.elgg-field').show();
		} else {
			$('#csv-exporter-range').closest('.elgg-field').hide();
		}
	});
	
	$(document).on('click', '#csv-exporter-schedule', function() {
		var $form = $(this).closest('form');

		var old_action = $form.attr('action');
		$form.attr('action', elgg.normalize_url('action/csv_exporter/edit'));
		$form.submit();

		$form.attr('action', old_action);
	});
});
