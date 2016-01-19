<?php
/**
 * Main file for this plugin
 */

require_once(dirname(__FILE__) . '/lib/functions.php');
require_once(dirname(__FILE__) . '/lib/hooks.php');

// register default Elgg events
elgg_register_event_handler('init', 'system', 'csv_exporter_init');

/**
 * Gets called when the system initializes
 *
 * @return void
 */
function csv_exporter_init() {
	
	// register plugin hooks
	elgg_register_plugin_hook_handler('get_exportable_values', 'csv_exporter', 'csv_exporter_get_exportable_values_hook');
	elgg_register_plugin_hook_handler('export_value', 'csv_exporter', 'csv_exporter_export_value_hook');
	elgg_register_plugin_hook_handler('register', 'menu:page', '\ColdTrick\CSVExporter\PageMenu::adminMenu');
	
	// register actions
	elgg_register_action('csv_exporter/download', dirname(__FILE__) . '/actions/download.php', 'admin');
}
