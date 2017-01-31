<?php
/**
 * All helper functions for this plugin are bundled here
 */

/**
 * Get a list of all the exportable values for the given type/subtype
 *
 * @param string $type     the entity type
 * @param string $subtype  the entity subtype
 * @param bool   $readable readable values or just for processing (default: false)
 *
 * @return array
 */
function csv_exporter_get_exportable_values($type, $subtype = '', $readable = false) {
	$result = [];
	
	if (empty($type)) {
		return $result;
	}
	
	if (($type == 'object') && empty($subtype)) {
		return $result;
	}
	
	$class = get_subtype_class($type, $subtype);
	if (!empty($class)) {
		$dummy = new $class();
	} else {
		switch ($type) {
			case 'object':
				$dummy = new ElggObject();
				break;
			case 'group':
				$dummy = new ElggGroup();
				break;
			case 'site':
				$dummy = new ElggSite();
				break;
			case 'user':
				$dummy = new ElggUser();
				break;
		}
	}
	
	$exports = (array) $dummy->toObject();
	$defaults = array_keys($exports);
	
	if ($readable) {
		$new_defaults = [];
		foreach ($defaults as $name) {
			if (elgg_language_key_exists($name)) {
				$lan = elgg_echo($name);
			} elseif (elgg_language_key_exists("csv_exporter:exportable_value:{$name}")) {
				$lan = elgg_echo("csv_exporter:exportable_value:{$name}");
			} else {
				$lan = $name;
			}
			$new_defaults[$lan] = $name;
		}
		
		$defaults = $new_defaults;
	}
	
	$params = [
		'type' => $type,
		'subtype' => $subtype,
		'readable' => $readable,
		'defaults' => $defaults,
	];
	$result = elgg_trigger_plugin_hook('get_exportable_values', 'csv_exporter', $params, $defaults);
	
	if (is_array($result)) {
		// prevent duplications
		$result = array_unique($result);
	}
	
	return $result;
}

/**
 * Get the latest activity of this group based on the river
 *
 * @param ElggGroup $entity the group to check
 *
 * @return int the UNIX timestamp of the latest activity
 */
function csv_exporter_get_last_group_activity(ElggGroup $entity) {
	$result = 0;
	
	if (!($entity instanceof ElggGroup)) {
		return $result;
	}
	
	$dbprefix = elgg_get_config('dbprefix');
	
	$query = 'SELECT max(r.posted) as posted';
	$query .= " FROM {$dbprefix}river r";
	$query .= " INNER JOIN {$dbprefix}entities e ON r.object_guid = e.guid";
	$query .= " WHERE (e.container_guid = {$entity->getGUID()})";
	$query .= " OR (r.object_guid = {$entity->getGUID()})";
	
	$data = get_data($query);
	if (!empty($data)) {
		$result = (int) $data[0]->posted;
	}
	
	return $result;
}

/**
 * Get the separator to be used in the CSV file (defaults to ;)
 * Can be changed in the plugin settings
 *
 * @return string
 */
function csv_exporter_get_separator() {
	static $result;
	
	if (!isset($result)) {
		$result = ';';
		
		$setting = elgg_get_plugin_setting('separator', 'csv_exporter');
		if (strlen($setting) === 1) {
			// php fputcsv only supports 1 char seperators
			$result = $setting;
		}
	}
	
	return $result;
}

/**
 * Convert a timestamp to a readable date/time
 *
 * @param int $time the timestamp to convert
 *
 * @return string
 */
function csv_exported_get_readable_timestamp($time) {
	$time = (int) $time;
	
	return date(elgg_echo('friendlytime:date_format'), $time);
}
