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
}

elgg.register_hook_handler("init", "system", elgg.csv_exporter.admin.init);