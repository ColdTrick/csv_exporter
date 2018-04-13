<?php

use ColdTrick\CSVExporter\Bootstrap;

require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'bootstrap' => Bootstrap::class,
	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'csv_export',
			'class' => CSVExport::class,
		],
	],
	'settings' => [
		'separator' => ';',
	],
	'actions' => [
		'csv_exporter/edit' => [
			'access' => 'admin',
		],
	],
];
