<?php

namespace ColdTrick\CSVExporter;

class Plugin {
	
	/**
	 * Save a plugin setting as an array
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param mixed  $return_value current return value
	 * @param array  $params       supplied params
	 *
	 * @return void|string
	 */
	public static function saveSettings($hook, $type, $return_value, $params) {
		
		if (elgg_extract('plugin_id', $params) !== 'csv_exporter') {
			return;
		}
		
		if (!is_array($return_value)) {
			return;
		}
		
		return json_encode($return_value);
	}
}
