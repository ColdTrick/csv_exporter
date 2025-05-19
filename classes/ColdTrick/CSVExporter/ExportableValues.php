<?php

namespace ColdTrick\CSVExporter;

use Elgg\Groups\Tool;

/**
 * Add exportable values
 */
class ExportableValues {
	
	/**
	 * Get the default exportable values
	 *
	 * @param \Elgg\Event $event 'get_exportable_values', 'csv_exporter'
	 *
	 * @return null|array
	 */
	public static function getExportableValues(\Elgg\Event $event): ?array {
		$content_type = $event->getParam('type');
		if (empty($content_type)) {
			return null;
		}
		
		$readable = (bool) $event->getParam('readable', false);
		
		// default exportable values
		$defaults = [
			elgg_echo('csv_exporter:exportable_value:owner_name') => 'owner|csv_exporter_owner_name',
			elgg_echo('csv_exporter:exportable_value:owner_username') => 'owner|csv_exporter_owner_username',
			elgg_echo('csv_exporter:exportable_value:owner_email') => 'owner|csv_exporter_owner_email',
			elgg_echo('csv_exporter:exportable_value:owner_url') => 'owner|csv_exporter_owner_url',
			elgg_echo('csv_exporter:exportable_value:container_name') => 'container|csv_exporter_container_name',
			elgg_echo('csv_exporter:exportable_value:container_username') => 'container|csv_exporter_container_username',
			elgg_echo('csv_exporter:exportable_value:container_email') => 'container|csv_exporter_container_email',
			elgg_echo('csv_exporter:exportable_value:container_url') => 'container|csv_exporter_container_url',
			elgg_echo('csv_exporter:exportable_value:time_created_readable') => 'timestamps|csv_exporter_time_created_readable',
			elgg_echo('csv_exporter:exportable_value:time_updated_readable') => 'timestamps|csv_exporter_time_updated_readable',
			elgg_echo('csv_exporter:exportable_value:url') => 'attributes|csv_exporter_url',
			elgg_echo('csv_exporter:exportable_value:access_id') => 'attributes|access_id',
			elgg_echo('csv_exporter:exportable_value:access_id_readable') => 'attributes|csv_exporter_access_id_readable',
			elgg_echo('csv_exporter:exportable_value:icon_url:master') => 'icon|csv_exporter_icon_url_master',
			elgg_echo('csv_exporter:exportable_value:icon_present') => 'icon|csv_exporter_icon_present',
			elgg_echo('status:deleted') => 'state|deleted',
			elgg_echo('csv_exporter:exportable_value:time_deleted') => 'timestamps|time_deleted',
			elgg_echo('csv_exporter:exportable_value:time_deleted_readable') => 'timestamps|csv_exporter_time_deleted_readable',
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
		
		$return = $event->getValue();
		
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
	protected static function getObjectExportableValues(): array {
		return [
			elgg_echo('tags') => 'metadata|csv_exporter_object_tags',
		];
	}
	
	/**
	 * Get the default exportable values for users
	 *
	 * @return array
	 */
	protected static function getUserExportableValues(): array {
		$result = [];
		
		// add profile fields
		$profile_fields = elgg()->fields->get('user', 'user');
		foreach ($profile_fields as $field) {
			$metadata_name = $field['name'];
			$label = elgg_extract('#label', $field, $metadata_name);
			
			$result[$label] = "metadata|{$metadata_name}";
		}
		
		// add defaults
		$result[elgg_echo('email')] = 'metadata|email';
		$result[elgg_echo('csv_exporter:exportable_value:user:last_action')] = 'timestamps|csv_exporter_user_last_action';
		$result[elgg_echo('csv_exporter:exportable_value:user:last_action_readable')] = 'timestamps|csv_exporter_user_last_action_readable';
		$result[elgg_echo('csv_exporter:exportable_value:user:first_login')] = 'timestamps|csv_exporter_user_first_login';
		$result[elgg_echo('csv_exporter:exportable_value:user:first_login_readable')] = 'timestamps|csv_exporter_user_first_login_readable';
		$result[elgg_echo('csv_exporter:exportable_value:user:last_login')] = 'timestamps|csv_exporter_user_last_login';
		$result[elgg_echo('csv_exporter:exportable_value:user:last_login_readable')] = 'timestamps|csv_exporter_user_last_login_readable';
		$result[elgg_echo('csv_exporter:exportable_value:user:groups_owned_name')] = 'csv_exporter_user_groups_owned_name';
		$result[elgg_echo('csv_exporter:exportable_value:user:groups_owned_url')] = 'csv_exporter_user_groups_owned_url';
		$result[elgg_echo('csv_exporter:exportable_value:user:friends')] = 'counters|csv_exporter_user_friends';
		$result[elgg_echo('csv_exporter:exportable_value:user:friends:of')] = 'counters|csv_exporter_user_friends_of';
		$result[elgg_echo('csv_exporter:exportable_value:user:banned')] = 'state|banned';
		
		// group only values
		$postfix = elgg_echo('csv_exporter:exportable_value:group:postfix');
		
		$result[elgg_echo('csv_exporter:exportable_value:user:group:member:unix') . $postfix] = 'csv_exporter_group_member_since_unix';
		$result[elgg_echo('csv_exporter:exportable_value:user:group:member:readable') . $postfix] = 'csv_exporter_group_member_since_readable';
		
		return $result;
	}
	
	/**
	 * Get the default exportable values for groups
	 *
	 * @return array
	 */
	protected static function getGroupExportableValues(): array {
		$result = [];
		
		// add profile fields
		$profile_fields = elgg()->fields->get('group', 'group');
		foreach ($profile_fields as $field) {
			$metadata_name = $field['name'];
			$label = elgg_extract('#label', $field, $metadata_name);
			
			$result[$label] = "metadata|{$metadata_name}";
		}
		
		// add defaults
		$result[elgg_echo('csv_exporter:exportable_value:group:membership')] = 'state|csv_exporter_group_membership';
		$result[elgg_echo('csv_exporter:exportable_value:group:visibility')] = 'state|csv_exporter_group_visibility';
		$result[elgg_echo('csv_exporter:exportable_value:group:member_count')] = 'counters|csv_exporter_group_member_count';
		$result[elgg_echo('csv_exporter:exportable_value:group:last_activity')] = 'timestamps|csv_exporter_group_last_activity';
		$result[elgg_echo('csv_exporter:exportable_value:group:last_activity_readable')] = 'timestamps|csv_exporter_group_last_activity_readable';
		$result[elgg_echo('csv_exporter:exportable_value:group:tools')] = 'csv_exporter_group_tools';
		$result[elgg_echo('csv_exporter:exportable_value:group:content_stats')] = 'counters|csv_exporter_group_content_stats';
		
		return $result;
	}
	
	/**
	 * Export a single value for an entity
	 *
	 * @param \Elgg\Event $event 'export_value', 'csv_exporter'
	 *
	 * @return mixed
	 */
	public static function exportEntityValue(\Elgg\Event $event) {
		if (!is_null($event->getValue())) {
			// someone already provided output
			return;
		}
		
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggEntity) {
			return;
		}
		
		$exportable_value = $event->getParam('exportable_value');
		
		$owner = $entity->getOwnerEntity();
		$container = $entity->getContainerEntity();
		
		if (str_starts_with($exportable_value, 'csv_exporter_owner_') && !$owner instanceof \ElggEntity) {
			// trying to export owner information, but owner not available
			return;
		}
		
		if (str_starts_with($exportable_value, 'csv_exporter_container_') && !$container instanceof \ElggEntity) {
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
				$email = (string) $owner->email;
				if (elgg_is_valid_email($email)) {
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
				$email = (string) $container->email;
				if (elgg_is_valid_email($email)) {
					return $email;
				}
				break;
			case 'csv_exporter_container_url':
				return $container->getURL();
			
			case 'csv_exporter_time_created_readable':
				return csv_exported_get_readable_timestamp($entity->time_created);
			
			case 'csv_exporter_time_updated_readable':
				return csv_exported_get_readable_timestamp($entity->time_updated);
			
			case 'csv_exporter_time_deleted_readable':
				return csv_exported_get_readable_timestamp($entity->time_deleted);
			
			case 'csv_exporter_url':
				return $entity->getURL();
			
			case 'csv_exporter_access_id_readable':
				return elgg_get_readable_access_level($entity->access_id);
			
			case 'csv_exporter_icon_url_master':
				if (!$entity->hasIcon('master')) {
					return '';
				}
				return $entity->getIconURL([
					'size' => 'master',
				]);
			case 'csv_exporter_icon_present':
				if ($entity->hasIcon('master')) {
					return elgg_echo('option:yes');
				}
				return elgg_echo('option:no');
		}
	}
	
	/**
	 * Export a single value for an object
	 *
	 * @param \Elgg\Event $event 'export_value', 'csv_exporter'
	 *
	 * @return null|array
	 */
	public static function exportObjectValue(\Elgg\Event $event): ?array {
		if (!is_null($event->getValue())) {
			// someone already provided output
			return null;
		}
		
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggObject) {
			return null;
		}
		
		$exportable_value = $event->getParam('exportable_value');
		switch ($exportable_value) {
			case 'csv_exporter_object_tags':
				if (!elgg_is_empty($entity->tags)) {
					return (array) $entity->tags;
				}
				break;
		}
		
		return null;
	}
	
	/**
	 * Export a single value for a user
	 *
	 * @param \Elgg\Event $event 'export_value', 'csv_exporter'
	 *
	 * @return mixed
	 */
	public static function exportUserValue(\Elgg\Event $event) {
		if (!is_null($event->getValue())) {
			// someone already provided output
			return;
		}
		
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggUser) {
			return;
		}
		
		$group_options = [
			'type' => 'group',
			'limit' => false,
			'owner_guid' => $entity->guid,
			'batch' => true,
		];
		
		$exportable_value = $event->getParam('exportable_value');
		$exportable_value = substr($exportable_value, strlen('csv_exporter_user_'));
		switch ($exportable_value) {
			case 'first_login':
			case 'last_action':
			case 'last_login':
				return (int) $entity->{$exportable_value};
			
			case 'first_login_readable':
			case 'last_action_readable':
			case 'last_login_readable':
				$exportable_value = substr($exportable_value, 0, -strlen('_readable'));
				return $entity->{$exportable_value} ? csv_exported_get_readable_timestamp((int) $entity->{$exportable_value}) : '';
			
			case 'groups_owned_name':
				$result = [];
				
				$batch = elgg_get_entities($group_options);
				/* @var $group \ElggGroup */
				foreach ($batch as $group) {
					$result[] = "\"{$group->getDisplayName()}\"";
				}
				return $result;
			
			case 'groups_owned_url':
				$result = [];
				
				$batch = elgg_get_entities($group_options);
				/* @var $group \ElggGroup */
				foreach ($batch as $group) {
					$result[] = $group->getURL();
				}
				return $result;
			
			case 'friends':
				return (int) $entity->getEntitiesFromRelationship([
					'type' => 'user',
					'relationship' => 'friend',
					'count' => true,
				]);
			
			case 'friends_of':
				return (int) $entity->getEntitiesFromRelationship([
					'type' => 'user',
					'relationship' => 'friend',
					'inverse_relationship' => true,
					'count' => true,
				]);
		}
	}
	
	/**
	 * Export a single value for a user
	 *
	 * @param \Elgg\Event $event 'export_value', 'csv_exporter'
	 *
	 * @return mixed
	 */
	public static function exportUserGroupValue(\Elgg\Event $event) {
		if (!is_null($event->getValue())) {
			// someone already provided output
			return;
		}
		
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggUser) {
			return;
		}
		
		$export = $event->getParam('csv_export');
		if (!$export instanceof \CSVExport) {
			return;
		}
		
		$group = $export->getContainerEntity();
		if (!$group instanceof \ElggGroup) {
			return;
		}
		
		$exportable_value = $event->getParam('exportable_value');
		switch ($exportable_value) {
			case 'csv_exporter_group_member_since_unix':
			case 'csv_exporter_group_member_since_readable':
				$relationship = $entity->getRelationship($group->guid, 'member');
				if (!$relationship instanceof \ElggRelationship) {
					return;
				}
				
				if ($exportable_value === 'csv_exporter_group_member_since_unix') {
					return (int) $relationship->time_created;
				}
				return csv_exported_get_readable_timestamp($relationship->time_created);
		}
	}
	
	/**
	 * Export a single value for a group
	 *
	 * @param \Elgg\Event $event 'export_value', 'csv_exporter'
	 *
	 * @return mixed
	 */
	public static function exportGroupValue(\Elgg\Event $event) {
		if (!is_null($event->getValue())) {
			// someone already provided output
			return;
		}
		
		$entity = $event->getEntityParam();
		if (!$entity instanceof \ElggGroup) {
			return;
		}
		
		$exportable_value = $event->getParam('exportable_value');
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
				if (str_starts_with($exportable_value, 'csv_exporter_group_tool_')) {
					$group_tool = str_ireplace('csv_exporter_group_tool_', '', $exportable_value);
					
					return (int) $entity->isToolEnabled($group_tool);
				} elseif (str_starts_with($exportable_value, 'csv_exporter_group_content_stats_')) {
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
	 * @param \Elgg\Event $event 'prepare:exportable_columns', 'csv_exporter'
	 *
	 * @return mixed
	 */
	public static function exportableColumnLabels(\Elgg\Event $event) {
		$type = $event->getParam('type');
		if (empty($type)) {
			return;
		}
		
		$subtype = $event->getParam('subtype');
		
		$available_columns = csv_exporter_get_exportable_values($type, $subtype, true);
		foreach($available_columns as $key => $value) {
			if (!str_contains($value, '|')) {
				continue;
			}
			list($category, $value) = explode('|', $value);
			$available_columns[$key] = $value;
		}
		
		$return = $event->getValue();
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
	 * @param \Elgg\Event $event 'prepare:exportable_columns', 'csv_exporter'
	 *
	 * @return mixed
	 */
	public static function exportableColumnGroupTools(\Elgg\Event $event) {
		$return = $event->getValue();
		$type = $event->getParam('type');
		if ($type !== 'group' || !array_key_exists('csv_exporter_group_tools', $return)) {
			return;
		}
		
		// remove 'display' column
		unset($return['csv_exporter_group_tools']);
		
		// get available tools
		$tool_options = elgg()->group_tools->all();
		/* @var $tool_config Tool */
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
	 * @param \Elgg\Event $event 'prepare:exportable_columns', 'csv_exporter'
	 *
	 * @return mixed
	 */
	public static function exportableColumnGroupContentStats(\Elgg\Event $event) {
		$return = $event->getValue();
		$type = $event->getParam('type');
		if ($type !== 'group' || !array_key_exists('csv_exporter_group_content_stats', $return)) {
			return;
		}
		
		// remove 'display' column
		unset($return['csv_exporter_group_content_stats']);
		
		// get available tools
		$searchable_subtypes = elgg_extract('object', elgg_entity_types_with_capability('searchable'), []);
		foreach ($searchable_subtypes as $subtype) {
			$label = $subtype;
			if (elgg_language_key_exists("collection:object:{$subtype}")) {
				$label = elgg_echo("collection:object:{$subtype}");
			} elseif (elgg_language_key_exists("item:object:{$subtype}")) {
				$label = elgg_echo("item:object:{$subtype}");
			}
			
			$return["csv_exporter_group_content_stats_{$subtype}"] = $label;
		}
		
		return $return;
	}
}
