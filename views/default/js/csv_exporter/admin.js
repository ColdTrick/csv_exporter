// CSV exporter js
define(['jquery', 'elgg'], function($, elgg) {

	elgg.provide('elgg.csv_exporter.admin');
	
	elgg.csv_exporter.admin.init = function() {
	
		$(document).on('change', '#csv-exporter-type-subtype', function() {
			$(this).parents('form').submit();
		});
	
		$(document).on('click', '#csv-exporter-download', function() {
			var $form = $(this).parents('form');
	
			var old_action = $form.attr('action');
			$form.attr('action', elgg.normalize_url('action/csv_exporter/download'));
			$form.submit();
	
			$form.attr('action', old_action);
		});
	}
	
	elgg.register_hook_handler('init', 'system', elgg.csv_exporter.admin.init);
	
});
