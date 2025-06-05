<?php

namespace ColdTrick\CSVExporter\Menus\Filter;

use Elgg\Menu\MenuItems;

/**
 * Add menu items to a filter menu
 */
class CSVExporterGroup {
	
	/**
	 * Add menu items to the csv_exporter/group menu
	 *
	 * @param \Elgg\Event $event 'register', 'menu:filter:csv_exporter/group'
	 *
	 * @return null|MenuItems
	 */
	public function __invoke(\Elgg\Event $event): ?MenuItems {
		$entity = $event->getParam('filter_entity');
		if (!$entity instanceof \ElggGroup || !$entity->canEdit()) {
			return null;
		}
		
		/* @var $return_value MenuItems */
		$return_value = $event->getValue();
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'configure',
			'text' => elgg_echo('csv_exporter:menu:csv_exporter:configure'),
			'href' => elgg_generate_url('collection:object:csv_export:group', [
				'guid' => $entity->guid,
			]),
			'priority' => 100,
		]);
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'download',
			'text' => elgg_echo('csv_exporter:menu:csv_exporter:download'),
			'href' => elgg_generate_url('collection:object:csv_export:group', [
				'guid' => $entity->guid,
				'filter' => 'download',
			]),
			'priority' => 200,
		]);
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'scheduled',
			'text' => elgg_echo('csv_exporter:menu:csv_exporter:scheduled'),
			'href' => elgg_generate_url('collection:object:csv_export:group', [
				'guid' => $entity->guid,
				'filter' => 'scheduled',
			]),
			'priority' => 300,
		]);
		
		return $return_value;
	}
}
