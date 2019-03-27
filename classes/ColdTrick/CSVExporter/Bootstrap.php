<?php

namespace ColdTrick\CSVExporter;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritDoc}
	 * @see \Elgg\DefaultPluginBootstrap::init()
	 */
	public function init() {
		
		// plugin hooks
		$hooks = $this->elgg()->hooks;
		$hooks->registerHandler('get_exportable_values', 'csv_exporter', '\ColdTrick\CSVExporter\ExportableValues::getExportableValues');
		$hooks->registerHandler('export_value', 'csv_exporter', '\ColdTrick\CSVExporter\ExportableValues::exportEntityValue');
		$hooks->registerHandler('export_value', 'csv_exporter', '\ColdTrick\CSVExporter\ExportableValues::exportObjectValue');
		$hooks->registerHandler('export_value', 'csv_exporter', '\ColdTrick\CSVExporter\ExportableValues::exportUserValue');
		$hooks->registerHandler('export_value', 'csv_exporter', '\ColdTrick\CSVExporter\ExportableValues::exportGroupValue');
		
		$hooks->registerHandler('prepare:exportable_columns', 'csv_exporter', '\ColdTrick\CSVExporter\ExportableValues::exportableColumnGroupTools');
		$hooks->registerHandler('prepare:exportable_columns', 'csv_exporter', '\ColdTrick\CSVExporter\ExportableValues::exportableColumnGroupContentStats');
		$hooks->registerHandler('prepare:exportable_columns', 'csv_exporter', '\ColdTrick\CSVExporter\ExportableValues::exportableColumnLabels', 9999);
		
		$hooks->registerHandler('register', 'menu:page', '\ColdTrick\CSVExporter\PageMenu::adminMenu');
		$hooks->registerHandler('register', 'menu:page', '\ColdTrick\CSVExporter\PageMenu::groupAdminMenu');
		$hooks->registerHandler('register', 'menu:csv_exporter', '\ColdTrick\CSVExporter\CSVExporterMenu::register');
		$hooks->registerHandler('register', 'menu:csv_exporter_group', '\ColdTrick\CSVExporter\CSVExporterMenu::registerGroup');
		$hooks->registerHandler('register', 'menu:entity', '\ColdTrick\CSVExporter\EntityMenu::csvExport');
		
		$hooks->registerHandler('cron', 'minute', '\ColdTrick\CSVExporter\Cron::processExports');
		$hooks->registerHandler('cron', 'daily', '\ColdTrick\CSVExporter\Cron::cleanupExports');
		
		$hooks->registerHandler('setting', 'plugin', '\ColdTrick\CSVExporter\Plugin::saveSettings');
	}
}
