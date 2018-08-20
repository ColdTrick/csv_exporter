<?php

namespace ColdTrick\CSVExporter;

class PageMenu {
	
	/**
	 * Add a menu item in the admin sidebar
	 *
	 * @param string          $hook         the name of the hook
	 * @param string          $type         the type of the hook
	 * @param \ElggMenuItem[] $return_value current return value
	 * @param array           $params       supplied params
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function adminMenu($hook, $type, $return_value, $params) {
		
		if (!elgg_is_admin_logged_in()) {
			return;
		}
		
		if (!elgg_in_context('admin')) {
			return;
		}
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'csv_exporter',
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
	 * @param string          $hook         the name of the hook
	 * @param string          $type         the type of the hook
	 * @param \ElggMenuItem[] $return_value current return value
	 * @param array           $params       supplied params
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function groupAdminMenu($hook, $type, $return_value, $params) {
		
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
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'csv_exporter',
			'text' => elgg_echo('csv_exporter:menu:group_profile'),
			'href' => "csv_exporter/group/{$page_owner->guid}",
		]);
		
		return $return_value;
	}
}
