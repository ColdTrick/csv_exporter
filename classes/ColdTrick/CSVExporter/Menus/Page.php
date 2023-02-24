<?php

namespace ColdTrick\CSVExporter\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the page menu
 */
class Page {
	
	/**
	 * Add a menu item in the group profile sidebar
	 *
	 * @param \Elgg\Event $event 'register', 'menu:page'
	 *
	 * @return null|MenuItems
	 */
	public static function groupAdminMenu(\Elgg\Event $event): ?MenuItems {
		$page_owner = elgg_get_page_owner_entity();
		if (!$page_owner instanceof \ElggGroup || !$page_owner->canEdit()) {
			return null;
		}
		
		if (!elgg_in_context('group_profile')) {
			return null;
		}
		
		if (!csv_exporter_get_group_subtypes()) {
			return null;
		}
		
		/* @var $return_value MenuItems */
		$return_value = $event->getValue();
		
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
