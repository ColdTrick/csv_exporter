<?php

namespace ColdTrick\CSVExporter;

use Elgg\Menu\MenuItems;

class EntityMenu {
	
	/**
	 * Change items in the CSVExport entity menu
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:entity'
	 *
	 * @return void|MenuItems
	 */
	public static function csvExport(\Elgg\Hook $hook) {
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \CSVExport) {
			return;
		}
		
		/* @var $return_value MenuItems */
		$return_value = $hook->getValue();
		
		$remove_items = [
			'edit',
		];
		foreach ($remove_items as $menu_name) {
			$return_value->remove($menu_name);
		}
		
		// add download
		if ($entity->isCompleted() && ($download_url = $entity->getDownloadURL())) {
			$return_value[] = \ElggMenuItem::factory([
				'name' => 'download',
				'text' => elgg_echo('download'),
				'href' => $download_url,
				'icon' => 'download',
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
