<?php

$type_subtype = get_input("type_subtype");
$exportable_values = get_input("exportable_values");

list($type, $subtype) = explode(":", $type_subtype);

// create a temp file
$fh = tmpfile();

$available_values = csv_exporter_get_exportable_values($type, $subtype, true);
$headers = array();
foreach ($exportable_values as $export_value) {
	$headers[] = array_search($export_value, $available_values);
}

// headers
fwrite($fh, "\"" . implode("\",\"", $headers) . "\"" . PHP_EOL);

$options = array(
	"type" => $type,
	"subtype" => $subtype,
	"limit" => false
);

$batch = new ElggBatch("elgg_get_entities", $options);

foreach ($batch as $entity) {
	
	$values = array();
	// params for hook
	$params = array(
		"type" => $type,
		"subtype" => $subtype,
		"entity" => $entity,
	);
	
	foreach ($exportable_values as $export_value) {
		$params["exportable_value"] = $export_value;
		
		$value = elgg_trigger_plugin_hook("export_value", "csv_exporter", $params);
		if ($value === null) {
			$value = $entity->$export_value;
		}
		
		$values[] = $value;
	}
	
	// row
	fwrite($fh, "\"" . implode("\",\"", $values) . "\"" . PHP_EOL);
}

// read the csv in to a var before output
$contents = "";
rewind($fh);
while (!feof($fh)) {
	$contents .= fread($fh, 2048);
}

// cleanup the temp file
fclose($fh);

// output the csv
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=\"export.csv\"");
header("Content-Length: " . strlen($contents));

echo $contents;
