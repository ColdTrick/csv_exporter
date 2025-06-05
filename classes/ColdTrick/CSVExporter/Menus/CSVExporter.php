<?php

namespace ColdTrick\CSVExporter\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the csv_exporter menu
 */
class CSVExporter {
	
	/**
	 * Add menu items to the csv_exporter menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:csv_exporter'
	 *
	 * @return null|MenuItems
	 */
	public static function register(\Elgg\Event $event): ?MenuItems {
		if (!elgg_is_admin_logged_in()) {
			return null;
		}
		
		/* @var $return_value MenuItems */
		$return_value = $event->getValue();
		
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
			'name' => 'download_all',
			'text' => elgg_echo('csv_exporter:menu:csv_exporter:download:all'),
			'href' => 'admin/administer_utilities/csv_exporter/download?filter=all',
			'priority' => 250,
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
