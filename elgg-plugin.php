<?php

use ColdTrick\CSVExporter\Bootstrap;
use Elgg\Router\Middleware\Gatekeeper;

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
	'routes' => [
		'collection:object:csv_export:group' => [
			'path' => '/csv_exporter/group/{guid}/{filter?}',
			'defaults' => [
				'filter' => 'configure',
			],
			'middleware' => [
				Gatekeeper::class,
			],
			'resource' => 'csv_exporter/group',
		],
	],
	'settings' => [
		'separator' => ';',
	],
	'actions' => [
		'csv_exporter/edit' => [
			'access' => 'admin',
		],
		'csv_exporter/group' => [],
	],
];
