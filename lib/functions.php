<?php
/**
 * All helper functions for this plugin are bundled here
 */

use Elgg\Database\RiverTable;
use Elgg\Database\Select;

/**
 * Get a list of all the exportable values for the given type/subtype
 *
 * @param string $type     the entity type
 * @param string $subtype  the entity subtype
 * @param bool   $readable readable values or just for processing (default: false)
 *
 * @return array
 */
function csv_exporter_get_exportable_values(string $type, string $subtype = '', bool $readable = false): array {
	if (empty($type)) {
		return [];
	}
	
	if ($type == 'object' && empty($subtype)) {
		return [];
	}
	
	$class = elgg_get_entity_class($type, $subtype);
	if (!empty($class)) {
		$dummy = new $class();
	} else {
		switch ($type) {
			case 'object':
				$dummy = new \ElggObject();
				break;
			case 'group':
				$dummy = new \ElggGroup();
				break;
			case 'site':
				$dummy = new \ElggSite();
				break;
			case 'user':
				$dummy = new \ElggUser();
				break;
		}
	}
	
	$exports = (array) $dummy->toObject();
	$defaults = array_keys($exports);
	
	$categories = [
		'container_guid' => 'container',
		'time_created' => 'timestamps',
		'time_updated' => 'timestamps',
		'owner_guid' => 'owner',
		'description' => 'metadata',
		'title' => 'metadata',
		'guid' => 'attributes',
		'read_access' => 'attributes',
		'type' => 'attributes',
		'subtype' => 'attributes',
		'name' => 'metadata',
		'username' => 'metadata',
		'language' => 'metadata',
	];
	
	$skip = [
		'read_access',
	];
	
	if ($readable) {
		$new_defaults = [];
		foreach ($defaults as $name) {
			if (in_array($name, $skip)) {
				continue;
			}
			
			if (elgg_language_key_exists($name)) {
				$lan = elgg_echo($name);
			} elseif (elgg_language_key_exists("csv_exporter:exportable_value:{$name}")) {
				$lan = elgg_echo("csv_exporter:exportable_value:{$name}");
			} else {
				$lan = $name;
			}
			
			$new_defaults[$lan] = elgg_extract($name, $categories, 'misc') . "|{$name}";;
		}
		
		$defaults = $new_defaults;
	}
	
	$params = [
		'type' => $type,
		'subtype' => $subtype,
		'readable' => $readable,
		'defaults' => $defaults,
	];
	$result = elgg_trigger_event_results('get_exportable_values', 'csv_exporter', $params, $defaults);
	
	if (is_array($result)) {
		// prevent duplications
		$result = array_unique($result);
	}
	
	return $result;
}

/**
 * Get a list of all the exportable values for the given type/subtype
 *
 * @param string $type    the entity type
 * @param string $subtype the entity subtype
 *
 * @return array
 */
function csv_exporter_get_exportable_group_values(string $type = 'object', string $subtype = ''): array {
	if ($type !== 'object' && $type !== 'user') {
		// @todo support more?
		return [];
	}
	
	if ($type === 'object' && empty($subtype)) {
		return [];
	}
	
	$available = csv_exporter_get_exportable_values($type, $subtype);
	
	$default_allowed = [];
	switch ($type) {
		case 'object':
			$default_allowed = [
				'title',
				'description',
				'csv_exporter_object_tags',
				'csv_exporter_owner_name',
				'csv_exporter_container_name',
				'csv_exporter_time_created_readable',
				'csv_exporter_time_updated_readable',
			];
			break;
		case 'user':
			$default_allowed = [
				'name',
				'username',
				'email',
				'banned',
				'csv_exporter_group_member_since_unix',
				'csv_exporter_group_member_since_readable',
			];
			
			// add profile fields
			$profile_fields = elgg()->fields->get('user', $subtype);
			foreach ($profile_fields as $field) {
				$default_allowed[] = $field['name'];
			}
			break;
	}
	
	$defaults = array_intersect($default_allowed, $available);
	
	$params = [
		'type' => $type,
		'subtype' => $subtype,
		'defaults' => $defaults,
		'available' => $available,
	];
	
	$result = elgg_trigger_event_results('get_exportable_values:group', 'csv_exporter', $params, $defaults);
	
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
 * @param \ElggGroup $entity the group to check
 *
 * @return int the UNIX timestamp of the latest activity
 */
function csv_exporter_get_last_group_activity(\ElggGroup $entity): int {
	$select = Select::fromTable(RiverTable::TABLE_NAME, RiverTable::DEFAULT_JOIN_ALIAS);
	$entities = $select->joinEntitiesTable($select->getTableAlias(), 'object_guid');
	
	$select->addSelect("max({$select->getTableAlias()}.posted) as posted")
		->where($select->compare("{$entities}.container_guid", '=', $entity->guid, ELGG_VALUE_GUID))
		->orWhere($select->compare("{$select->getTableAlias()}.object_guid", '=', $entity->guid, ELGG_VALUE_GUID));
	
	$data = elgg()->db->getData($select);
	if (empty($data)) {
		return 0;
	}
	
	return (int) $data[0]->posted;
}

/**
 * Get the separator to be used in the CSV file (defaults to ;)
 * Can be changed in the plugin settings
 *
 * @return string
 */
function csv_exporter_get_separator(): string {
	static $result;
	
	if (!isset($result)) {
		$result = ';';
		
		$setting = elgg_get_plugin_setting('separator', 'csv_exporter');
		if (strlen($setting) === 1) {
			// php fputcsv only supports 1 char separators
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
function csv_exported_get_readable_timestamp(int $time): string {
	if ($time === 0) {
		return '';
	}
	
	return date(elgg_echo('friendlytime:date_format'), $time);
}

/**
 * Prepare the selected columns for export
 *
 * @param string[] $selected_columns the column id's
 * @param string   $type             entity type
 * @param string   $subtype          entity subtype
 *
 * @return array
 */
function csv_exporter_prepare_exportable_columns(array $selected_columns, string $type, string $subtype = ''): array {
	if (empty($selected_columns) || empty($type)) {
		return $selected_columns;
	}
	
	$column_config = array_combine($selected_columns, $selected_columns);
	
	$params = [
		'type' => $type,
		'subtype' => $subtype,
		'selected_columns' => $selected_columns,
	];
	return elgg_trigger_event_results('prepare:exportable_columns', 'csv_exporter', $params, $column_config);
}

/**
 * Get the allowed group subtypes to export
 *
 * @return string[]
 */
function csv_exporter_get_group_subtypes(): array {
	static $result;
	
	if (isset($result)) {
		return $result;
	}
	
	$result = [];
	
	$setting = elgg_get_plugin_setting('allowed_group_subtypes', 'csv_exporter');
	if (!empty($setting)) {
		$result = json_decode($setting, true);
		
		$searchable = elgg_entity_types_with_capability('searchable');
		$searchable_objects = elgg_extract('object', $searchable, []);
		$searchable_users = elgg_extract('user', $searchable, []);
		$searchable_subtypes = array_merge($searchable_objects, $searchable_users);
		
		$result = array_intersect($result, $searchable_subtypes);
		
		$result = array_values($result);
	}
	
	return $result;
}

/**
 * Get the allowed type/subtypes to be exported
 *
 * @return array
 */
function csv_exporter_get_allowed_entity_types(): array {
	$type_subtypes = elgg_entity_types_with_capability('searchable');
	
	return elgg_trigger_event_results('allowed_type_subtypes', 'csv_exporter', [], $type_subtypes);
}
