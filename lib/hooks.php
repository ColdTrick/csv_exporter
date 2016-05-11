<?php
/**
 * All plugin hook callbacks are bundled here
 */

/**
 * Return a value to be exported, return null to allow default behaviour
 *
 * @param string $hook        'export_value'
 * @param string $type        'csv_exporter'
 * @param mixed  $returnvalue the current value
 * @param array  $params      supplied params
 *
 * @return void|string
 */
function csv_exporter_export_value_hook($hook, $type, $returnvalue, $params) {
	
	if (!empty($returnvalue)) {
		// some output already provided
		return;
	}
	
	if (empty($params) || !is_array($params)) {
		return;
	}
	
	$type = elgg_extract('type', $params);
	$entity = elgg_extract('entity', $params);
	$exportable_value = elgg_extract('exportable_value', $params);
	
	switch ($exportable_value) {
		case 'csv_exporter_owner_name':
			$owner = $entity->getOwnerEntity();
			if ($owner) {
				if ($owner->name) {
					$returnvalue = $owner->name;
				} else {
					// the owner is an ElggObject
					$returnvalue = $owner->title;
				}
			}
			break;
		case 'csv_exporter_owner_username':
			$owner = $entity->getOwnerEntity();
			if ($owner) {
				if ($owner instanceof ElggUser) {
					$returnvalue = $owner->username;
				} else {
					// the owner is not an ElggUser
					$returnvalue = $owner->getGUID();
				}
			}
			break;
		case 'csv_exporter_owner_email':
			$owner = $entity->getOwnerEntity();
			if ($owner instanceof ElggUser) {
				$returnvalue = $owner->email;
			}
			break;
		case 'csv_exporter_owner_url':
			$owner = $entity->getOwnerEntity();
			if ($owner) {
				if (!($owner instanceof ElggSite)) {
					$returnvalue = $owner->getURL();
				} else {
					// the owner is an ElggSite
					$returnvalue = $owner->url;
				}
			}
			break;
		case 'csv_exporter_container_name':
			$container = $entity->getContainerEntity();
			if ($container) {
				if ($container->name) {
					$returnvalue = $container->name;
				} else {
					// the container is an ElggObject
					$returnvalue = $container->title;
				}
			}
			break;
		case 'csv_exporter_container_username':
			$container = $entity->getContainerEntity();
			if ($container) {
				if ($container instanceof ElggUser) {
					$returnvalue = $container->username;
				} else {
					// the container is not an ElggUser
					$returnvalue = $container->getGUID();
				}
			}
			break;
		case 'csv_exporter_container_email':
			$container = $entity->getContainerEntity();
			if ($container instanceof ElggUser) {
				$returnvalue = $container->email;
			}
			break;
		case 'csv_exporter_container_url':
			$container = $entity->getContainerEntity();
			if ($container) {
				if (!($container instanceof ElggSite)) {
					$returnvalue = $container->getURL();
				} else {
					// the container is an ElggSite
					$returnvalue = $container->url;
				}
			}
			break;
		case 'csv_exporter_time_created_readable':
			$returnvalue = date(elgg_echo('friendlytime:date_format'), $entity->time_created);
			break;
		case 'csv_exporter_time_updated_readable':
			$returnvalue = date(elgg_echo('friendlytime:date_format'), $entity->time_updated);
			break;
		case 'csv_exporter_url':
			if (!($entity instanceof ElggSite)) {
				$returnvalue = $entity->getURL();
			} else {
				// the entity is an ElggSite
				$entity->url;
			}
			break;
		case 'csv_exporter_object_tags':
			if ($entity instanceof ElggObject) {
				if ($entity->tags) {
					$tags = $entity->tags;
					if (!is_array($tags)) {
						$tags = [$tags];
					}
					
					$returnvalue = implode(', ', $tags);
				}
			}
			break;
		case 'csv_exporter_user_last_action':
			$returnvalue = $entity->last_action;
			break;
		case 'csv_exporter_user_last_action_readable':
			$returnvalue = date(elgg_echo('friendlytime:date_format'), $entity->last_action);
			break;
		case 'csv_exporter_group_member_count':
			if ($entity instanceof ElggGroup) {
				$returnvalue = $entity->getMembers(['count' => true]);
			}
			break;
		case 'csv_exporter_group_last_activity':
			if ($entity instanceof ElggGroup) {
				$returnvalue = csv_exporter_get_last_group_activity($entity);
			}
			break;
		case 'csv_exporter_group_last_activity_readable':
			if ($entity instanceof ElggGroup) {
				$ts = csv_exporter_get_last_group_activity($entity);
				$returnvalue = date(elgg_echo('friendlytime:date_format'), $ts);
			}
			break;
		default:
			// check for profile fields
			if (($type == 'user') || ($type == 'group')) {
				if (is_array($entity->$exportable_value)) {
					$returnvalue = implode(', ', $entity->$exportable_value);
				}
			}
			
			break;
	}
	
	return $returnvalue;
}
