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
			'name' => 'csv_exporter_admin',
			'text' => elgg_echo('admin:administer_utilities:csv_exporter'),
			'href' => 'admin/administer_utilities/csv_exporter',
			'parent_name' => 'administer_utilities',
			'section' => 'administer',
		]);
		
		return $return_value;
	}
	
	/**
	 * Add a menu item in the group profile sidebar
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:page'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function groupAdminMenu(\Elgg\Hook $hook) {
		
		$page_owner = elgg_get_page_owner_entity();
		if (!$page_owner instanceof \ElggGroup || !$page_owner->canEdit()) {
			return;
		}
		
		if (!elgg_in_context('group_profile')) {
			return;
		}
		
		if (!csv_exporter_get_group_subtypes()) {
			return;
		}
		
		$return_value = $hook->getValue();
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'csv_exporter',
			'text' => elgg_echo('csv_exporter:menu:group_profile'),
			'href' => elgg_generate_url('collection:object:csv_export:group', [
				'guid' => $page_owner->guid,
			]),
		]);
		
		return $return_value;
	}
}
