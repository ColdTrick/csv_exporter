<?php

namespace ColdTrick\CSVExporter;

class EntityMenu {
	
	/**
	 * Change items in the CSVExport entity menu
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:entity'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function csvExport(\Elgg\Hook $hook) {
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \CSVExport) {
			return;
		}
		
		$return_value = $hook->getValue();
		
		$remove_items = [
			'edit',
		];
		/* @var $menu_item \ElggMenuItem */
		foreach ($return_value as $index => $menu_item) {
			if (!in_array($menu_item->getName(), $remove_items)) {
				continue;
			}
			
			unset($return_value[$index]);
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
		
		return $return_value;
	}
}
