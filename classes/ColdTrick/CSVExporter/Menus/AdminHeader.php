<?php

namespace ColdTrick\CSVExporter\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the admin_header menu
 */
class AdminHeader {
	
	/**
	 * Add a menu item in the admin_header
	 *
	 * @param \Elgg\Event $event 'register', 'menu:admin_header'
	 *
	 * @return null|MenuItems
	 */
	public static function register(\Elgg\Event $event): ?MenuItems {
		if (!elgg_is_admin_logged_in() || !elgg_in_context('admin')) {
			return null;
		}
		
		$return_value = $event->getValue();
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'csv_exporter_admin',
			'text' => elgg_echo('admin:administer_utilities:csv_exporter'),
			'href' => 'admin/administer_utilities/csv_exporter',
			'parent_name' => 'administer_utilities',
		]);
		
		return $return_value;
	}
}
