<?php

namespace ColdTrick\CSVExporter;

class CSVExporterMenu {
	
	/**
	 * Add menu items to the csv_exporter menu
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:csv_exporter'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function register(\Elgg\Hook $hook) {
		
		if (!elgg_is_admin_logged_in()) {
			return;
		}
		
		$return_value = $hook->getValue();
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'configure',
			'text' => elgg_echo('csv_exporter:menu:csv_exporter:configure'),
			'href' => 'admin/administer_utilities/csv_exporter',
			'priority' => 100,
		]);
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'downloads',
			'text' => elgg_echo('csv_exporter:menu:csv_exporter:downloads'),
			'href' => 'admin/administer_utilities/csv_exporter/downloads',
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
