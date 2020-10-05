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
	
	$class = elgg_get_entity_class($type, $subtype);
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
 * Get a list of all the exportable values for the given type/subtype
 *
 * @param string $type     the entity type
 * @param string $subtype  the entity subtype
 * @param bool   $readable readable values or just for processing (default: false)
 *
 * @return array
 */
function csv_exporter_get_exportable_group_values($type = 'object', $subtype = '') {
	
	if ($type !== 'object') {
		// @todo support more?
		return [];
	}
	
	if($type === 'object' && empty($subtype)) {
		return [];
	}
	
	$available = csv_exporter_get_exportable_values($type, $subtype);
	
	$default_allowed = [
		'title',
		'description',
		'csv_exporter_object_tags',
		'csv_exporter_owner_name',
		'csv_exporter_container_name',
		'csv_exporter_time_created_readable',
		'csv_exporter_time_updated_readable',
	];
	
	$defaults = array_intersect($default_allowed, $available);
	
	$params = [
		'type' => $type,
		'subtype' => $subtype,
		'defaults' => $defaults,
		'available' => $available,
	];
	
	$result = elgg_trigger_plugin_hook('get_exportable_values:group', 'csv_exporter', $params, $defaults);
	
	if (is_array($result)) {
		// must be available
		$result = array_intersect($result, $available);
		// prevent duplications
		$result = array_unique($result);
		// only values matter
		$result = array_values($result);
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
	
	// @todo rewrite this to QueryBuilder
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

/**
 * Prepare the selected columns for export
 *
 * @param string[] $selected_columns the column id's
 * @param string   $type             entity type
 * @param string   $subtype          entity subtype
 *
 * @return array|mixed
 */
function csv_exporter_prepare_exportable_columns($selected_columns, $type, $subtype = '') {
	
	if (empty($selected_columns) || !is_array($selected_columns) || empty($type)) {
		return $selected_columns;
	}
	
	$column_config = array_combine($selected_columns, $selected_columns);
	
	$params = [
		'type' => $type,
		'subtype' => $subtype,
		'selected_columns' => $selected_columns,
	];
	return elgg_trigger_plugin_hook('prepare:exportable_columns', 'csv_exporter', $params, $column_config);
}

/**
 * Get form vars for CSV export
 *
 * @param CSVExport $entity entity to edit
 *
 * @return array
 */
function csv_exporter_prepare_edit_form_vars(CSVExport $entity = null) {
	
	$result = [
		'type_subtype' => null,
		'time' => null,
		'created_time_lower' => null,
		'created_time_upper' => null,
		'title' => null,
		'exportable_values' => [],
	];
	
	// edit of an export
	if ($entity instanceof CSVExport) {
		foreach ($result as $name => $default_value) {
			$result[$name] = $entity->getFormData($name);
		}
		
		$result['entity'] = $entity;
	}
	
	// preview
	foreach ($result as $name => $default_value) {
		$result[$name] = get_input($name, $default_value);
	}
	
	// sticky form vars
	$stick_vars = elgg_get_sticky_values('csv_exporter');
	if (!empty($stick_vars)) {
		foreach ($stick_vars as $name => $value) {
			$result[$name] = $value;
		}
		
		elgg_clear_sticky_form('csv_exporter');
	}
	
	return $result;
}

/**
 * Get the allowed group subtypes to export
 *
 * @return false|string[]
 */
function csv_exporter_get_group_subtypes() {
	static $result;
	
	if (isset($result)) {
		return $result;
	}
	
	$result = false;
	
	$setting = elgg_get_plugin_setting('allowed_group_subtypes', 'csv_exporter');
	if (!empty($setting)) {
		$result = json_decode($setting, true);
		
		$objects = get_registered_entity_types('object');
		$result = array_intersect($result, $objects);
		
		$result = array_values($result);
	}
	
	return $result;
}

/**
 * Get the allowed type/subtypes to be exported
 *
 * @return array
 */
function csv_exporter_get_allowed_entity_types() {
	$type_subtypes = get_registered_entity_types();
	
	return elgg_trigger_plugin_hook('allowed_type_subtypes', 'csv_exporter', [], $type_subtypes);
}
