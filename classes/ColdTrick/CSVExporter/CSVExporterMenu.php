<?php

namespace ColdTrick\CSVExporter;

class CSVExporterMenu {
	
	/**
	 * Add menu items to the csv_exporter menu
	 *
	 * @param string          $hook         the name of the hook
	 * @param string          $type         the type of the hook
	 * @param \ElggMenuItem[] $return_value current return value
	 * @param array           $params       supplied params
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function register($hook, $type, $return_value, $params) {
		
		if (!elgg_is_admin_logged_in()) {
			return;
		}
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'configure',
			'text' => elgg_echo('csv_exporter:menu:csv_exporter:configure'),
			'href' => 'admin/administer_utilities/csv_exporter',
			'priority' => 100,
		]);
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'download',
			'text' => elgg_echo('csv_exporter:menu:csv_exporter:download'),
			'href' => 'admin/administer_utilities/csv_exporter/download',
			'priority' => 200,
		]);
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'scheduled',
			'text' => elgg_echo('csv_exporter:menu:csv_exporter:scheduled'),
			'href' => 'admin/administer_utilities/csv_exporter/scheduled',
			'priority' => 300,
		]);
		
		return $return_value;
	}
}
