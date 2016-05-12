<?php

namespace ColdTrick\CSVExporter;

class EntityMenu {
	
	/**
	 * Change items in the CSVExport entity menu
	 *
	 * @param string          $hook
	 * @param string          $type
	 * @param \ElggMenuItem[] $return_value
	 * @param array           $params
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function csvExport($hook, $type, $return_value, $params) {
		
		$entity = elgg_extract('entity', $params);
		if (!($entity instanceof \CSVExport)) {
			return;
		}
		
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
				'text' => elgg_view_icon('download'),
				'title' => elgg_echo('download'),
				'href' => $download_url,
				'priority' => 100,
			]);
		}
		
		return $return_value;
	}
}
