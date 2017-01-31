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
			elgg_echo('csv_exporter:exportable_value:access_id') => 'access_id',
			elgg_echo('csv_exporter:exportable_value:access_id_readable') => 'csv_exporter_access_id_readable',
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
		
		$read_access_key = array_search('read_access', $return_value);
		if ($read_access_key !== false) {
			unset($return_value[$read_access_key]);
		}
		
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
		$result[elgg_echo('csv_exporter:exportable_value:user:groups_owned_name')] = 'csv_exporter_user_groups_owned_name';
		$result[elgg_echo('csv_exporter:exportable_value:user:groups_owned_url')] = 'csv_exporter_user_groups_owned_url';
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
		
		return $result;
	}
	
	/**
	 * Export a single value for an entity
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param mixed  $return_value the current return value
	 * @param array  $params       supplied params
	 *
	 * @return void|mixed
	 */
	public static function exportEntityValue($hook, $type, $return_value, $params) {
		
		if (!is_null($return_value)) {
			// someone already provided output
			return;
		}
		
		$entity = elgg_extract('entity', $params);
		if (!($entity instanceof \ElggEntity)) {
			return;
		}
		
		$exportable_value = elgg_extract('exportable_value', $params);
		
		$owner = $entity->getOwnerEntity();
		$container = $entity->getContainerEntity();
		
		if ((stristr($exportable_value, 'csv_exporter_owner_') !== false) && !($owner instanceof \ElggEntity)) {
			// trying to export owner information, but owner not available
			return;
		}
		
		if ((stristr($exportable_value, 'csv_exporter_container_') !== false) && !($container instanceof \ElggEntity)) {
			// trying to export container information, but container not available
			return;
		}
		
		switch ($exportable_value) {
			case 'csv_exporter_owner_name';
				if ($owner instanceof \ElggObject) {
					return $owner->title;
				} else {
					return $owner->name;
				}
				break;
			case 'csv_exporter_owner_username';
				if ($owner instanceof \ElggUser) {
					return $owner->username;
				} else {
					return $owner->getGUID();
				}
				break;
			case 'csv_exporter_owner_email';
				$email = $owner->email;
				if (is_email_address($email)) {
					return $email;
				}
				break;
			case 'csv_exporter_owner_url';
				return $owner->getURL();
				break;
			case 'csv_exporter_container_name';
				if ($container instanceof \ElggObject) {
					return $container->title;
				} else {
					return $container->name;
				}
				break;
			case 'csv_exporter_container_username';
				if ($container instanceof \ElggUser) {
					return $container->username;
				} else {
					return $container->getGUID();
				}
				break;
			case 'csv_exporter_container_email';
				$email = $container->email;
				if (is_email_address($email)) {
					return $email;
				}
				break;
			case 'csv_exporter_container_url';
				return $container->getURL();
				break;
			case 'csv_exporter_time_created_readable';
				return csv_exported_get_readable_timestamp($entity->time_created);
				break;
			case 'csv_exporter_time_updated_readable';
				return csv_exported_get_readable_timestamp($entity->time_updated);
				break;
			case 'csv_exporter_url';
				return $entity->getURL();
				break;
			case 'csv_exporter_access_id_readable';
				return get_readable_access_level($entity->access_id);
				break;
		}
	}
	
	/**
	 * Export a single value for an object
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param mixed  $return_value the current return value
	 * @param array  $params       supplied params
	 *
	 * @return void|mixed
	 */
	public static function exportObjectValue($hook, $type, $return_value, $params) {
		
		if (!is_null($return_value)) {
			// someone already provided output
			return;
		}
		
		$entity = elgg_extract('entity', $params);
		if (!($entity instanceof \ElggObject)) {
			return;
		}
		
		$exportable_value = elgg_extract('exportable_value', $params);
		
		switch ($exportable_value) {
			case 'csv_exporter_object_tags':
				if ($entity->tags) {
					return (array) $entity->tags;
				}
				break;
		}
	}
	
	/**
	 * Export a single value for a user
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param mixed  $return_value the current return value
	 * @param array  $params       supplied params
	 *
	 * @return void|mixed
	 */
	public static function exportUserValue($hook, $type, $return_value, $params) {
		
		if (!is_null($return_value)) {
			// someone already provided output
			return;
		}
		
		$entity = elgg_extract('entity', $params);
		if (!($entity instanceof \ElggUser)) {
			return;
		}
		
		$group_options = [
			'type' => 'group',
			'limit' => false,
			'owner_guid' => $entity->getGUID(),
		];
		
		$exportable_value = elgg_extract('exportable_value', $params);
		switch ($exportable_value) {
			case 'csv_exporter_user_last_action':
				return (int) $entity->last_action;
				break;
			case 'csv_exporter_user_last_action_readable':
				return csv_exported_get_readable_timestamp($entity->last_action);
				break;
			case 'csv_exporter_user_groups_owned_name':
				$result = [];
				
				$batch = new \ElggBatch('elgg_get_entities', $group_options);
				/* @var $group \ElggGroup */
				foreach ($batch as $group) {
					$result[] = "\"{$group->name}\"";
				}
				
				return $result;
				break;
			case 'csv_exporter_user_groups_owned_url':
				$result = [];
				
				$batch = new \ElggBatch('elgg_get_entities', $group_options);
				/* @var $group \ElggGroup */
				foreach ($batch as $group) {
					$result[] = $group->getURL();
				}
				
				return $result;
				break;
		}
	}
	
	/**
	 * Export a single value for a group
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param mixed  $return_value the current return value
	 * @param array  $params       supplied params
	 *
	 * @return void|mixed
	 */
	public static function exportGroupValue($hook, $type, $return_value, $params) {
		
		if (!is_null($return_value)) {
			// someone already provided output
			return;
		}
		
		$entity = elgg_extract('entity', $params);
		if (!($entity instanceof \ElggGroup)) {
			return;
		}
		
		$exportable_value = elgg_extract('exportable_value', $params);
		
		switch ($exportable_value) {
			case 'csv_exporter_group_member_count':
				return $entity->getMembers(['count' => true]);
				break;
			case 'csv_exporter_group_last_activity':
				return csv_exporter_get_last_group_activity($entity);
				break;
			case 'csv_exporter_group_last_activity_readable':
				$ts = csv_exporter_get_last_group_activity($entity);
				return csv_exported_get_readable_timestamp($ts);
				break;
			case 'csv_exporter_group_membership':
				if ($entity->isPublicMembership()) {
					return elgg_echo('groups:open');
				} else {
					return elgg_echo('groups:closed');
				}
				break;
			case 'csv_exporter_group_visibility':
				
				switch ($entity->access_id) {
					case ACCESS_PUBLIC:
						return elgg_echo('PUBLIC');
						break;
					case ACCESS_LOGGED_IN:
						return elgg_echo('LOGGED_IN');
						break;
					default:
						return elgg_echo('groups:access:group');
						break;
				}
				break;
		}
	}
}
