<?php

use ColdTrick\CSVExporter\Bootstrap;

require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'bootstrap' => Bootstrap::class,
	'entities' => [
		[
			'type' => 'object',
			'subtype' => CSVExport::SUBTYPE,
			'class' => CSVExport::class,
		],
	],
	'actions' => [
		'csv_exporter/edit' => [
			'access' => 'admin',
		],
	],
];
