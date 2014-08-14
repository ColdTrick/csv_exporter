<?php
/**
 * All plugin hook callbacks are bundled here
 */

/**
 * Extend the default exportable values with some extra's
 *
 * @param string $hook        'get_exportable_values'
 * @param string $type        'csv_exporter'
 * @param array  $returnvalue the current exportable values
 * @param array  $params      supplied params
 *
 * @return array
 */
function csv_exporter_get_exportable_values_hook($hook, $type, $returnvalue, $params) {
	
	if (!empty($params) && is_array($params)) {
		$type = elgg_extract("type", $params);
		$readable = (bool) elgg_extract("readable", $params, false);
		
		if ($readable) {
			// defaults
			$returnvalue = array_merge($returnvalue, array(
				elgg_echo("csv_exporter:exportable_value:owner_name") => "csv_exporter_owner_name",
				elgg_echo("csv_exporter:exportable_value:owner_username") => "csv_exporter_owner_username",
				elgg_echo("csv_exporter:exportable_value:owner_email") => "csv_exporter_owner_email",
				elgg_echo("csv_exporter:exportable_value:owner_url") => "csv_exporter_owner_url",
				elgg_echo("csv_exporter:exportable_value:container_name") => "csv_exporter_container_name",
				elgg_echo("csv_exporter:exportable_value:container_username") => "csv_exporter_container_username",
				elgg_echo("csv_exporter:exportable_value:container_email") => "csv_exporter_container_email",
				elgg_echo("csv_exporter:exportable_value:container_url") => "csv_exporter_container_url",
				elgg_echo("csv_exporter:exportable_value:time_created_readable") => "csv_exporter_time_created_readable",
				elgg_echo("csv_exporter:exportable_value:time_updated_readable") => "csv_exporter_time_updated_readable",
				elgg_echo("csv_exporter:exportable_value:url") => "csv_exporter_url",
			));
			
			switch ($type) {
				case "object":
					$returnvalue[elgg_echo("tags")] = "csv_exporter_object_tags";
					break;
				case "user":
					// add profile fields
					$profile_fields = elgg_get_config("profile_fields");
					if (!empty($profile_fields)) {
						foreach ($profile_fields as $metadata_name => $input_type) {
							$lan = $metadata_name;
							if (elgg_echo("profile:" . $metadata_name) != $metadata_name) {
								$lan = elgg_echo("profile:" . $metadata_name);
							}
							$returnvalue[$lan] = $metadata_name;
						}
					}
					
					// others
					$returnvalue[elgg_echo("email")] = "email";
						
					break;
				case "group":
					// add profile fields
					$profile_fields = elgg_get_config("group");
					
					if (!empty($profile_fields)) {
						foreach ($profile_fields as $metadata_name => $input_type) {
							$lan = $metadata_name;
							if (elgg_echo("groups:" . $metadata_name) != $metadata_name) {
								$lan = elgg_echo("groups:" . $metadata_name);
							}
							$returnvalue[$lan] = $metadata_name;
						}
					}
						
					// others
					$returnvalue[elgg_echo("csv_exporter:exportable_value:group:member_count")] = "csv_exporter_group_member_count";
					$returnvalue[elgg_echo("csv_exporter:exportable_value:group:last_activity")] = "csv_exporter_group_last_activity";
					$returnvalue[elgg_echo("csv_exporter:exportable_value:group:last_activity_readable")] = "csv_exporter_group_last_activity_readable";
					break;
			}
		} else {
			// defaults
			$returnvalue = array_merge($returnvalue, array(
				"csv_exporter_owner_name",
				"csv_exporter_owner_username",
				"csv_exporter_owner_email",
				"csv_exporter_owner_url",
				"csv_exporter_container_name",
				"csv_exporter_container_username",
				"csv_exporter_container_email",
				"csv_exporter_container_url",
				"csv_exporter_time_created_readable",
				"csv_exporter_time_updated_readable",
				"csv_exporter_url",
			));
			
			switch ($type) {
				case "object":
					$returnvalue[] = "csv_exporter_object_tags";
					break;
				case "user":
					// add profile fields
					$profile_fields = elgg_get_config("profile_fields");
					if (!empty($profile_fields)) {
						foreach ($profile_fields as $metadata_name => $input_type) {
							$returnvalue[] = $metadata_name;
						}
					}
					
					//others
					$returnvalue[] = "email";
					
					break;
				case "group":
					// add profile fields
					$profile_fields = elgg_get_config("group");
					if (!empty($profile_fields)) {
						foreach ($profile_fields as $metadata_name => $input_type) {
							$returnvalue[] = $metadata_name;
						}
					}
					
					// others
					$returnvalue[] = "csv_exporter_group_member_count";
					$returnvalue[] = "csv_exporter_group_last_activity";
					$returnvalue[] = "csv_exporter_group_last_activity_readable";
					break;
			}
		}
	}
	
	return $returnvalue;
}

/**
 * Return a value to be exported, return null to allow default behaviour
 *
 * @param string $hook        'export_value'
 * @param string $type        'csv_exporter'
 * @param mixed  $returnvalue the current value
 * @param array  $params      supplied params
 *
 * @return null|string
 */
function csv_exporter_export_value_hook($hook, $type, $returnvalue, $params) {
	
	if (empty($returnvalue) && !empty($params) && is_array($params)) {
		$type = elgg_extract("type", $params);
		$entity = elgg_extract("entity", $params);
		$exportable_value = elgg_extract("exportable_value", $params);
		
		switch ($exportable_value) {
			case "csv_exporter_owner_name":
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
			case "csv_exporter_owner_username":
				$owner = $entity->getOwnerEntity();
				if ($owner) {
					if (elgg_instanceof($owner, "user")) {
						$returnvalue = $owner->username;
					} else {
						// the owner is not an ElggUser
						$returnvalue = $owner->getGUID();
					}
				}
				break;
			case "csv_exporter_owner_email":
				$owner = $entity->getOwnerEntity();
				if ($owner) {
					if (elgg_instanceof($owner, "user")) {
						$returnvalue = $owner->email;
					}
				}
				break;
			case "csv_exporter_owner_url":
				$owner = $entity->getOwnerEntity();
				if ($owner) {
					if (!elgg_instanceof($owner, "site")) {
						$returnvalue = $owner->getURL();
					} else {
						// the owner is an ElggSite
						$returnvalue = $owner->url;
					}
				}
				break;
			case "csv_exporter_container_name":
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
			case "csv_exporter_container_username":
				$container = $entity->getContainerEntity();
				if ($container) {
					if (elgg_instanceof($container, "user")) {
						$returnvalue = $container->username;
					} else {
						// the container is not an ElggUser
						$returnvalue = $container->getGUID();
					}
				}
				break;
			case "csv_exporter_container_email":
				$container = $entity->getContainerEntity();
				if ($container) {
					if (elgg_instanceof($container, "user")) {
						$returnvalue = $container->email;
					}
				}
				break;
			case "csv_exporter_container_url":
				$container = $entity->getContainerEntity();
				if ($container) {
					if (!elgg_instanceof($container, "site")) {
						$returnvalue = $container->getURL();
					} else {
						// the container is an ElggSite
						$returnvalue = $container->url;
					}
				}
				break;
			case "csv_exporter_time_created_readable":
				$returnvalue = date(elgg_echo("friendlytime:date_format"), $entity->time_created);
				break;
			case "csv_exporter_time_updated_readable":
				$returnvalue = date(elgg_echo("friendlytime:date_format"), $entity->time_updated);
				break;
			case "csv_exporter_url":
				if (!elgg_instanceof($entity, "site")) {
					$returnvalue = $entity->getURL();
				} else {
					// the entity is an ElggSite
					$entity->url;
				}
				break;
			case "csv_exporter_object_tags":
				if (elgg_instanceof($entity, "object")) {
					if ($entity->tags) {
						$tags = $entity->tags;
						if (!is_array($tags)) {
							$tags = array($tags);
						}
						
						$returnvalue = implode(", ", $tags);
					}
				}
				break;
			case "csv_exporter_group_member_count":
				if (elgg_instanceof($entity, "group")) {
					$returnvalue = $entity->getMembers(0, 0, true);
				}
				break;
			case "csv_exporter_group_last_activity":
				if (elgg_instanceof($entity, "group")) {
					$returnvalue = csv_exporter_get_last_group_activity($entity);
				}
				break;
			case "csv_exporter_group_last_activity_readable":
				if (elgg_instanceof($entity, "group")) {
					$ts = csv_exporter_get_last_group_activity($entity);
					$returnvalue = date(elgg_echo("friendlytime:date_format"), $ts);
				}
				break;
			default:
				// check for profile fields
				if (($type == "user") || ($type == "group")) {
					if (is_array($entity->$exportable_value)) {
						$returnvalue = implode(", ", $entity->$exportable_value);
					}
				}
				
				break;
		}
	}
	
	return $returnvalue;
}
