<?php

namespace ColdTrick\CSVExporter;

class ExportableValues {
	
	/**
	 * Get the default exportable values
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param array  $return_value the current return value
	 * @param array  $params       supplied params
	 *
	 * @return void|array
	 */
	public static function getExportableValues($hook, $type, $return_value, $params) {
		
		if (empty($params) || !is_array($params)) {
			return;
		}
		
		$content_type = elgg_extract('type', $params);
		$readable = (bool) elgg_extract('readable', $params, false);
		
		// default exportable values
		$defaults = [
			elgg_echo('csv_exporter:exportable_value:owner_name') => 'csv_exporter_owner_name',
			elgg_echo('csv_exporter:exportable_value:owner_username') => 'csv_exporter_owner_username',
			elgg_echo('csv_exporter:exportable_value:owner_email') => 'csv_exporter_owner_email',
			elgg_echo('csv_exporter:exportable_value:owner_url') => 'csv_exporter_owner_url',
			elgg_echo('csv_exporter:exportable_value:container_name') => 'csv_exporter_container_name',
			elgg_echo('csv_exporter:exportable_value:container_username') => 'csv_exporter_container_username',
			elgg_echo('csv_exporter:exportable_value:container_email') => 'csv_exporter_container_email',
			elgg_echo('csv_exporter:exportable_value:container_url') => 'csv_exporter_container_url',
			elgg_echo('csv_exporter:exportable_value:time_created_readable') => 'csv_exporter_time_created_readable',
			elgg_echo('csv_exporter:exportable_value:time_updated_readable') => 'csv_exporter_time_updated_readable',
			elgg_echo('csv_exporter:exportable_value:url') => 'csv_exporter_url',
		];
		
		$content_fields = [];
		switch ($content_type) {
			case 'object':
				$content_fields = self::getObjectExportableValues();
				break;
			case 'user':
				$content_fields = self::getUserExportableValues();
				break;
			case 'group':
				$content_fields = self::getGroupExportableValues();
				break;
		}
		
		// combine default and type fields
		$fields = array_merge($defaults, $content_fields);
		
		// which version did we want
		if (!$readable) {
			$fields = array_values($fields);
		}
		
		return array_merge($return_value, $fields);
	}
	
	/**
	 * Get the default exportable values for objects
	 *
	 * @return array
	 */
	protected static function getObjectExportableValues() {
		
		return [
			elgg_echo('tags') => 'csv_exporter_object_tags',
		];
	}
	
	/**
	 * Get the default exportable values for users
	 *
	 * @return array
	 */
	protected static function getUserExportableValues() {
		
		$result = [];
		
		// add profile fields
		$profile_fields = elgg_get_config('profile_fields');
		if (!empty($profile_fields)) {
			foreach ($profile_fields as $metadata_name => $input_type) {
				$lan = $metadata_name;
				if (elgg_language_key_exists("profile:{$metadata_name}")) {
					$lan = elgg_echo("profile:{$metadata_name}");
				}
				$result[$lan] = $metadata_name;
			}
		}
		
		// add defaults
		$result[elgg_echo('email')] = 'email';
		$result[elgg_echo('csv_exporter:exportable_value:user:last_action')] = 'csv_exporter_user_last_action';
		$result[elgg_echo('csv_exporter:exportable_value:user:last_action_readable')] = 'csv_exporter_user_last_action_readable';
		
		return $result;
	}
	
	/**
	 * Get the default exportable values for groups
	 *
	 * @return array
	 */
	protected static function getGroupExportableValues() {
		
		$result = [];
		
		// add profile fields
		$profile_fields = elgg_get_config('group');
		if (!empty($profile_fields)) {
			foreach ($profile_fields as $metadata_name => $input_type) {
				$lan = $metadata_name;
				if (elgg_language_key_exists("groups:{$metadata_name}")) {
					$lan = elgg_echo("groups:{$metadata_name}");
				}
				$result[$lan] = $metadata_name;
			}
		}
		
		// add defaults
		$result[elgg_echo('csv_exporter:exportable_value:group:member_count')] = 'csv_exporter_group_member_count';
		$result[elgg_echo('csv_exporter:exportable_value:group:last_activity')] = 'csv_exporter_group_last_activity';
		$result[elgg_echo('csv_exporter:exportable_value:group:last_activity_readable')] = 'csv_exporter_group_last_activity_readable';
		
		return $result;
	}
}
