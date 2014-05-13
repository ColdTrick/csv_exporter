<?php
/**
 * Add javascript to the Admin side
 */
?>
//<script>

elgg.provide("elgg.csv_exporter.admin");

elgg.csv_exporter.admin.init = function() {

	$("#csv-exporter-type-subtype").live("change", function() {
		$(this).parents("form").submit();
	});

	$("#csv-exporter-download").live("click", function() {
		var $form = $(this).parents("form");

		var old_action = $form.attr("action");
		$form.attr("action", elgg.normalize_url("action/csv_exporter/download"));
		$form.submit();

		$form.attr("action", old_action);
	});
}

elgg.register_hook_handler("init", "system", elgg.csv_exporter.admin.init);