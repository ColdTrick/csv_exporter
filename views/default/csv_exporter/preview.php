<?php
/**
 * Show a preview of the exported content
 * 
 * @uses $vars['type'] the entity type
 * @uses $vars['subtype'] the entity subtype,
 * @uses $vars['exportable_values'] the values to export
 */

$type = elgg_extract("type", $vars);
$subtype = elgg_extract("subtype", $vars);

$exportable_values = elgg_extract("exportable_values", $vars);

$readable_values = csv_exporter_get_exportable_values($type, $subtype, true);

$content = "<table class='elgg-table'>";

$content .= "<thead>";
$content .= "<tr>";
foreach ($exportable_values as $name) {
	$content .= "<th>" . array_search($name, $readable_values) . "</th>";
}
$content .= "</tr>";
$content .= "</thead>";

$content .= "<tbody>";

$limit = max(0, get_input("limit", 25));
$offset = max(0, get_input("offset", 0));
$options = array(
	"type" => $type,
	"subtype" => $subtype,
	"limit" => $limit,
	"offset" => $offset
);
$entities = new ElggBatch("elgg_get_entities", $options);
foreach ($entities as $entity) {
	
	// params for hook
	$params = array(
		"type" => $type,
		"subtype" => $subtype,
		"entity" => $entity,
	);
	
	$content .= "<tr>";
	
	foreach ($exportable_values as $metadata_name) {
		$params["exportable_value"] = $metadata_name;
		
		$value = elgg_trigger_plugin_hook("export_value", "csv_exporter", $params);
		if ($value === null) {
			$value = $entity->$metadata_name;
		}
		
		$content .= "<td>" . $value . "</td>";
	}
	
	$content .= "</tr>";
	
}

$content .= "</tbody>";
$content .= "</table>";

$options["count"] = true;
$content .= elgg_view("navigation/pagination", array(
	"limit" => $limit,
	"offset" => $offset,
	"count" => elgg_get_entities($options),
	"base_url" => elgg_http_add_url_query_elements(current_page_url(), array(
		"type_subtype" => empty($subtype) ? $type : $type . ":" . $subtype,
		"exportable_values" => $exportable_values
	))
));

echo elgg_view_module("inline", elgg_echo("csv_exporter:admin:preview:title"), $content, array("id" => "preview"));
