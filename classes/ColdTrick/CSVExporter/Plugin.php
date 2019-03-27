<?php

namespace ColdTrick\CSVExporter;

class Plugin {
	
	/**
	 * Save a plugin setting as an array
	 *
	 * @param \Elgg\Hook $hook 'setting', 'plugin'
	 *
	 * @return void|string
	 */
	public static function saveSettings(\Elgg\Hook $hook) {
		
		if ($hook->getParam('plugin_id') !== 'csv_exporter') {
			return;
		}
		
		$result = $hook->getValue();
		if (!is_array($result)) {
			return;
		}
		
		return json_encode($result);
	}
}
