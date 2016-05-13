<?php
/**
 * Main file for this plugin
 */

require_once(dirname(__FILE__) . '/lib/functions.php');

// register default Elgg events
elgg_register_event_handler('init', 'system', 'csv_exporter_init');

/**
 * Gets called when the system initializes
 *
 * @return void
 */
function csv_exporter_init() {
	
	// extend views
	elgg_extend_view('admin.css', 'css/csv_exporter/admin.css');
	
	// register plugin hooks
	elgg_register_plugin_hook_handler('get_exportable_values', 'csv_exporter', '\ColdTrick\CSVExporter\ExportableValues::getExportableValues');
	elgg_register_plugin_hook_handler('export_value', 'csv_exporter', '\ColdTrick\CSVExporter\ExportableValues::exportEntityValue');
	elgg_register_plugin_hook_handler('export_value', 'csv_exporter', '\ColdTrick\CSVExporter\ExportableValues::exportObjectValue');
	elgg_register_plugin_hook_handler('export_value', 'csv_exporter', '\ColdTrick\CSVExporter\ExportableValues::exportUserValue');
	elgg_register_plugin_hook_handler('export_value', 'csv_exporter', '\ColdTrick\CSVExporter\ExportableValues::exportGroupValue');
	
	elgg_register_plugin_hook_handler('register', 'menu:page', '\ColdTrick\CSVExporter\PageMenu::adminMenu');
	elgg_register_plugin_hook_handler('register', 'menu:csv_exporter', '\ColdTrick\CSVExporter\CSVExporterMenu::register');
	elgg_register_plugin_hook_handler('register', 'menu:entity', '\ColdTrick\CSVExporter\EntityMenu::csvExport');
	
	elgg_register_plugin_hook_handler('cron', 'minute', '\ColdTrick\CSVExporter\Cron::processExports');
	elgg_register_plugin_hook_handler('cron', 'daily', '\ColdTrick\CSVExporter\Cron::cleanupExports');
	
	// events
	elgg_register_event_handler('upgrade', 'system', '\ColdTrick\CSVExporter\Upgrade::setClassHandler');
	
	// register actions
	elgg_register_action('csv_exporter/edit', dirname(__FILE__) . '/actions/edit.php', 'admin');
}
