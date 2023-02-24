<?php

namespace ColdTrick\CSVExporter\Menus;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to the entity menu
 */
class Entity {
	
	/**
	 * Change items in the CSVExport entity menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:entity'
	 *
	 * @return null|MenuItems
	 */
	public static function csvExport(\Elgg\Event $event): ?MenuItems {
		$entity = $event->getEntityParam();
		if (!$entity instanceof \CSVExport) {
			return null;
		}
		
		/* @var $return_value MenuItems */
		$return_value = $event->getValue();
		
		$remove_items = [
			'edit',
		];
		foreach ($remove_items as $menu_name) {
			$return_value->remove($menu_name);
		}
		
		// add download
		$download_url = $entity->getDownloadURL();
		if ($entity->isCompleted() && !empty($download_url)) {
			$return_value[] = \ElggMenuItem::factory([
				'name' => 'download',
				'icon' => 'download',
				'text' => elgg_echo('download'),
				'href' => $download_url,
				'priority' => 100,
				'section' => 'alt',
			]);
		}
		
		// allow crashed entities to be restarted
		if ($entity->isProcessing() && elgg_is_admin_logged_in()) {
			$return_value[] = \ElggMenuItem::factory([
				'name' => 'restart',
				'icon' => 'refresh',
				'text' => elgg_echo('csv_exporter:menu:entity:restart'),
				'href' => elgg_generate_action_url('csv_exporter/admin/restart', [
					'guid' => $entity->guid,
				]),
				'confirm' => true,
			]);
		}
		
		return $return_value;
	}
}
