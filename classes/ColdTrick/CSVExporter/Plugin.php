<?php

namespace ColdTrick\CSVExporter;

/**
 * Modify plugin settings
 */
class Plugin {
	
	/**
	 * Save a plugin setting as an array
	 *
	 * @param \Elgg\Event $event 'setting', 'plugin'
	 *
	 * @return null|string
	 */
	public static function saveSettings(\Elgg\Event $event): ?string {
		if ($event->getParam('plugin_id') !== 'csv_exporter') {
			return null;
		}
		
		$result = $event->getValue();
		if (!is_array($result)) {
			return null;
		}
		
		return json_encode($result);
	}
}
