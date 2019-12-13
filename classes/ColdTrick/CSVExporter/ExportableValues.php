<?php

namespace ColdTrick\CSVExporter;

class ExportableValues {
	
	/**
	 * Get the default exportable values
	 *
	 * @param \Elgg\Hook $hook 'get_exportable_values', 'csv_exporter'
	 *
	 * @return void|array
	 */
	public static function getExportableValues(\Elgg\Hook $hook) {
		
		$content_type = $hook->getParam('type');
		if (empty($content_type)) {
			return;
		}
		
		$readable = (bool) $hook->getParam('readable', false);
		
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
			elgg_echo('csv_exporter:exportable_value:access_id') => 'access_id',
			elgg_echo('csv_exporter:exportable_value:access_id_readable') => 'csv_exporter_access_id_readable',
			elgg_echo('csv_exporter:exportable_value:icontime') => 'icontime',
			elgg_echo('csv_exporter:exportable_value:icontime_readable') => 'csv_exporter_icontime_readable',
			elgg_echo('csv_exporter:exportable_value:icon_url:master') => 'csv_exporter_icon_url_master',
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
		
		$return = $hook->getValue();
		
		$read_access_key = array_search('read_access', $return);
		if ($read_access_key !== false) {
			unset($return[$read_access_key]);
		}
		
		// which version did we want
		if (!$readable) {
			$fields = array_values($fields);
		}
		
		return array_merge($return, $fields);
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
		$result[elgg_echo('csv_exporter:exportable_value:user:groups_owned_name')] = 'csv_exporter_user_groups_owned_name';
		$result[elgg_echo('csv_exporter:exportable_value:user:groups_owned_url')] = 'csv_exporter_user_groups_owned_url';
		$result[elgg_echo('csv_exporter:exportable_value:user:friends')] = 'csv_exporter_user_friends';
		$result[elgg_echo('csv_exporter:exportable_value:user:friends:of')] = 'csv_exporter_user_friends_of';
		$result[elgg_echo('csv_exporter:exportable_value:user:banned')] = 'banned';
		
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
		$result[elgg_echo('csv_exporter:exportable_value:group:membership')] = 'csv_exporter_group_membership';
		$result[elgg_echo('csv_exporter:exportable_value:group:visibility')] = 'csv_exporter_group_visibility';
		$result[elgg_echo('csv_exporter:exportable_value:group:member_count')] = 'csv_exporter_group_member_count';
		$result[elgg_echo('csv_exporter:exportable_value:group:last_activity')] = 'csv_exporter_group_last_activity';
		$result[elgg_echo('csv_exporter:exportable_value:group:last_activity_readable')] = 'csv_exporter_group_last_activity_readable';
		$result[elgg_echo('csv_exporter:exportable_value:group:tools')] = 'csv_exporter_group_tools';
		$result[elgg_echo('csv_exporter:exportable_value:group:content_stats')] = 'csv_exporter_group_content_stats';
		
		return $result;
	}
	
	/**
	 * Export a single value for an entity
	 *
	 * @param \Elgg\Hook $hook 'export_value', 'csv_exporter'
	 *
	 * @return void|mixed
	 */
	public static function exportEntityValue(\Elgg\Hook $hook) {
		
		if (!is_null($hook->getValue())) {
			// someone already provided output
			return;
		}
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \ElggEntity) {
			return;
		}
		
		$exportable_value = $hook->getParam('exportable_value');
		
		$owner = $entity->getOwnerEntity();
		$container = $entity->getContainerEntity();
		
		if ((stristr($exportable_value, 'csv_exporter_owner_') !== false) && !$owner instanceof \ElggEntity) {
			// trying to export owner information, but owner not available
			return;
		}
		
		if ((stristr($exportable_value, 'csv_exporter_container_') !== false) && !$container instanceof \ElggEntity) {
			// trying to export container information, but container not available
			return;
		}
		
		switch ($exportable_value) {
			case 'csv_exporter_owner_name':
				return $owner->getDisplayName();
			
			case 'csv_exporter_owner_username':
				if ($owner instanceof \ElggUser) {
					return $owner->username;
				}
				
				return $owner->guid;
			
			case 'csv_exporter_owner_email':
				$email = $owner->email;
				if (is_email_address($email)) {
					return $email;
				}
				break;
			case 'csv_exporter_owner_url':
				return $owner->getURL();
			
			case 'csv_exporter_container_name':
				return $container->getDisplayName();
			
			case 'csv_exporter_container_username':
				if ($container instanceof \ElggUser) {
					return $container->username;
				}
				
				return $container->guid;
			
			case 'csv_exporter_container_email':
				$email = $container->email;
				if (is_email_address($email)) {
					return $email;
				}
				break;
			case 'csv_exporter_container_url':
				return $container->getURL();
			
			case 'csv_exporter_time_created_readable':
				return csv_exported_get_readable_timestamp($entity->time_created);
			
			case 'csv_exporter_time_updated_readable':
				return csv_exported_get_readable_timestamp($entity->time_updated);
			
			case 'csv_exporter_url':
				return $entity->getURL();
			
			case 'csv_exporter_access_id_readable':
				return get_readable_access_level($entity->access_id);
			
			case 'csv_exporter_icontime_readable':
				if (!isset($entity->icontime)) {
					return '';
				}
				return csv_exported_get_readable_timestamp($entity->icontime);
			case 'csv_exporter_icon_url_master':
				if (!$entity->hasIcon('master')) {
					return '';
				}
				return $entity->getIconURL([
					'size' => 'master',
				]);
		}
	}
	
	/**
	 * Export a single value for an object
	 *
	 * @param \Elgg\Hook $hook 'export_value', 'csv_exporter'
	 *
	 * @return void|mixed
	 */
	public static function exportObjectValue(\Elgg\Hook $hook) {
		
		if (!is_null($hook->getValue())) {
			// someone already provided output
			return;
		}
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \ElggObject) {
			return;
		}
		
		$exportable_value = $hook->getParam('exportable_value');
		switch ($exportable_value) {
			case 'csv_exporter_object_tags':
				if (!elgg_is_empty($entity->tags)) {
					return (array) $entity->tags;
				}
				break;
		}
	}
	
	/**
	 * Export a single value for a user
	 *
	 * @param \Elgg\Hook $hook 'export_value', 'csv_exporter'
	 *
	 * @return void|mixed
	 */
	public static function exportUserValue(\Elgg\Hook $hook) {
		
		if (!is_null($hook->getValue())) {
			// someone already provided output
			return;
		}
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \ElggUser) {
			return;
		}
		
		$group_options = [
			'type' => 'group',
			'limit' => false,
			'owner_guid' => $entity->guid,
			'batch' => true,
		];
		
		$exportable_value = $hook->getParam('exportable_value');
		switch ($exportable_value) {
			case 'csv_exporter_user_last_action':
				return (int) $entity->last_action;
			
			case 'csv_exporter_user_last_action_readable':
				return csv_exported_get_readable_timestamp($entity->last_action);
			
			case 'csv_exporter_user_groups_owned_name':
				$result = [];
				
				$batch = elgg_get_entities($group_options);
				/* @var $group \ElggGroup */
				foreach ($batch as $group) {
					$result[] = "\"{$group->getDisplayName()}\"";
				}
				
				return $result;
			
			case 'csv_exporter_user_groups_owned_url':
				$result = [];
				
				$batch = elgg_get_entities($group_options);
				/* @var $group \ElggGroup */
				foreach ($batch as $group) {
					$result[] = $group->getURL();
				}
				
				return $result;
			
			case 'csv_exporter_user_friends':
				return (int) $entity->getEntitiesFromRelationship([
					'type' => 'user',
					'relationship' => 'friend',
					'count' => true,
				]);
			
			case 'csv_exporter_user_friends_of':
				return (int) $entity->getEntitiesFromRelationship([
					'type' => 'user',
					'relationship' => 'friend',
					'inverse_relationship' => true,
					'count' => true,
				]);
		}
	}
	
	/**
	 * Export a single value for a group
	 *
	 * @param \Elgg\Hook $hook 'export_value', 'csv_exporter'
	 *
	 * @return void|mixed
	 */
	public static function exportGroupValue(\Elgg\Hook $hook) {
		
		if (!is_null($hook->getValue())) {
			// someone already provided output
			return;
		}
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \ElggGroup) {
			return;
		}
		
		$exportable_value = $hook->getParam('exportable_value');
		switch ($exportable_value) {
			case 'csv_exporter_group_member_count':
				return $entity->getMembers(['count' => true]);
			
			case 'csv_exporter_group_last_activity':
				return csv_exporter_get_last_group_activity($entity);
			
			case 'csv_exporter_group_last_activity_readable':
				$ts = csv_exporter_get_last_group_activity($entity);
				return csv_exported_get_readable_timestamp($ts);
			
			case 'csv_exporter_group_membership':
				if ($entity->isPublicMembership()) {
					return elgg_echo('groups:open');
				}
				return elgg_echo('groups:closed');
			
			case 'csv_exporter_group_visibility':
				
				switch ($entity->access_id) {
					case ACCESS_PUBLIC:
						return elgg_echo('access:label:public');
			
					case ACCESS_LOGGED_IN:
						return elgg_echo('access:label:logged_in');
				}
				
				return elgg_echo('groups:access:group');
			
			default:
				if (stripos($exportable_value, 'csv_exporter_group_tool_') !== false) {
					$group_tool = str_ireplace('csv_exporter_group_tool_', '', $exportable_value);
					
					return (int) $entity->isToolEnabled($group_tool);
				} elseif (stripos($exportable_value, 'csv_exporter_group_content_stats_') !== false) {
					$subtype = str_ireplace('csv_exporter_group_content_stats_', '', $exportable_value);
					
					return elgg_get_entities([
						'type' => 'object',
						'subtype' => $subtype,
						'container_guid' => $entity->guid,
						'count' => true,
					]);
				}
				break;
		}
	}
	
	/**
	 * Change the label of the exported value
	 *
	 * @param \Elgg\Hook $hook 'prepare:exportable_columns', 'csv_exporter'
	 *
	 * @return void|mixed
	 */
	public static function exportableColumnLabels(\Elgg\Hook $hook) {
		
		$type = $hook->getParam('type');
		if (empty($type)) {
			return;
		}
		
		$subtype = $hook->getParam('subtype');
		
		$available_columns = csv_exporter_get_exportable_values($type, $subtype, true);
		
		$return = $hook->getValue();
		foreach ($return as $column_id => $label) {
			if ($column_id !== $label) {
				continue;
			}
			
			$new_label = array_search($column_id, $available_columns);
			if ($new_label === false) {
				// no better label found
				continue;
			}
			
			$return[$column_id] = $new_label;
		}
		
		return $return;
	}
	
	/**
	 * Change the columns when selecting group tools
	 *
	 * @param \Elgg\Hook $hook 'prepare:exportable_columns', 'csv_exporter'
	 *
	 * @return void|mixed
	 */
	public static function exportableColumnGroupTools(\Elgg\Hook $hook) {
		
		$return = $hook->getValue();
		$type = $hook->getParam('type');
		if ($type !== 'group' ||  !array_key_exists('csv_exporter_group_tools', $return)) {
			return;
		}
		
		// remove 'display' column
		unset($return['csv_exporter_group_tools']);
		
		// get available tools
		$tool_options = elgg_get_config('group_tool_options');
		if (is_callable('groups_get_group_tool_options')) {
			$tool_options = groups_get_group_tool_options();
		}
		
		foreach ($tool_options as $tool_config) {
			$tool_id = $tool_config->name;
			$label = elgg_echo('csv_exporter:exportable_value:group:tool', [$tool_id]);
			
			$return["csv_exporter_group_tool_{$tool_id}"] = $label;
		}
		
		return $return;
	}
	
	/**
	 * Change the columns when selecting group content stats
	 *
	 * @param \Elgg\Hook $hook 'prepare:exportable_columns', 'csv_exporter'
	 *
	 * @return void|mixed
	 */
	public static function exportableColumnGroupContentStats(\Elgg\Hook $hook) {
		
		$return = $hook->getValue();
		$type = $hook->getParam('type');
		if ($type !== 'group' || !array_key_exists('csv_exporter_group_content_stats', $return)) {
			return;
		}
		
		// remove 'display' column
		unset($return['csv_exporter_group_content_stats']);
		
		// get available tools
		$object_subtypes = get_registered_entity_types('object');
		
		foreach ($object_subtypes as $subtype) {
			$label = $subtype;
			if (elgg_language_key_exists("item:object:{$subtype}")) {
				$label = elgg_echo("item:object:{$subtype}");
			}
			
			$return["csv_exporter_group_content_stats_{$subtype}"] = $label;
		}
		
		return $return;
	}
}
