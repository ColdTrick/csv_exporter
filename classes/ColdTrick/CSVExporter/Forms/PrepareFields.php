<?php

namespace ColdTrick\CSVExporter\Forms;

/**
 * Prepare form fields for csv exports
 */
class PrepareFields {
	
	/**
	 * Prepare form fields
	 *
	 * @param \Elgg\Event $event 'form:prepare:fields', 'csv_exporter/edit'
	 *
	 * @return array
	 */
	public function __invoke(\Elgg\Event $event): array {
		$vars = $event->getValue();
		
		$values = [
			'type_subtype' => null,
			'time' => null,
			'created_time_lower' => null,
			'created_time_upper' => null,
			'title' => null,
			'exportable_values' => [],
			'preview' => 1,
		];
		
		// edit of an entity
		$entity = elgg_extract('entity', $vars);
		if ($entity instanceof \CSVExport) {
			foreach ($values as $name => $default_value) {
				$values[$name] = $entity->getFormData($name);
			}
		}
		
		// preview
		foreach ($values as $name => $default_value) {
			$values[$name] = get_input($name, $default_value);
		}
		
		return array_merge($values, $vars);
	}
}
