<?php

namespace ColdTrick\CSVExporter;

class PageMenu {
	
	/**
	 * Add a menu item in the admin sidebar
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:page'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function adminMenu(\Elgg\Hook $hook) {
		
		if (!elgg_is_admin_logged_in() || !elgg_in_context('admin')) {
			return;
		}
		
		$return_value = $hook->getValue();
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'csv_exporter',
			'text' => elgg_echo('admin:administer_utilities:csv_exporter'),
			'href' => 'admin/administer_utilities/csv_exporter',
			'parent_name' => 'administer_utilities',
			'section' => 'administer',
		]);
		
		return $return_value;
	}
}
