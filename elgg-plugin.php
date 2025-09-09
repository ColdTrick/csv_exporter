<?php

require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'plugin' => [
		'name' => 'CSV Exporter',
		'version' => '13.2.2',
	],
	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'csv_export',
			'class' => \CSVExport::class,
			'capabilities' => [
				'commentable' => false,
			],
		],
	],
	'settings' => [
		'separator' => ';',
	],
	'actions' => [
		'csv_exporter/admin/restart' => [
			'access' => 'admin',
		],
		'csv_exporter/edit' => [
			'access' => 'admin',
		],
		'csv_exporter/group' => [],
	],
	'routes' => [
		'collection:object:csv_export:group' => [
			'path' => '/csv_exporter/group/{guid}/{filter?}',
			'resource' => 'csv_exporter/group',
			'defaults' => [
				'filter' => 'configure',
			],
			'required_plugins' => [
				'groups',
			],
			'middleware' => [
				\Elgg\Router\Middleware\Gatekeeper::class,
				\Elgg\Router\Middleware\GroupPageOwnerCanEditGatekeeper::class,
			],
		],
	],
	'events' => [
		'cron' => [
			'daily' => [
				'\ColdTrick\CSVExporter\Cron::cleanupExports' => [],
			],
			'minute' => [
				'\ColdTrick\CSVExporter\Cron::processExports' => [],
			],
		],
		'export_value' => [
			'csv_exporter' => [
				'\ColdTrick\CSVExporter\ExportableValues::exportEntityValue' => [],
				'\ColdTrick\CSVExporter\ExportableValues::exportObjectValue' => [],
				'\ColdTrick\CSVExporter\ExportableValues::exportUserValue' => [],
				'\ColdTrick\CSVExporter\ExportableValues::exportUserGroupValue' => [],
				'\ColdTrick\CSVExporter\ExportableValues::exportGroupValue' => [],
			],
		],
		'form:prepare:fields' => [
			'csv_exporter/edit' => [
				\ColdTrick\CSVExporter\Forms\PrepareFields::class => [],
			],
		],
		'get_exportable_values' => [
			'csv_exporter' => [
				'\ColdTrick\CSVExporter\ExportableValues::getExportableValues' => [],
			],
		],
		'prepare:exportable_columns' => [
			'csv_exporter' => [
				'\ColdTrick\CSVExporter\ExportableValues::exportableColumnGroupTools' => [],
				'\ColdTrick\CSVExporter\ExportableValues::exportableColumnGroupContentStats' => [],
				'\ColdTrick\CSVExporter\ExportableValues::exportableColumnLabels' => ['priority' => 9999],
			],
		],
		'register' => [
			'menu:admin_header' => [
				'\ColdTrick\CSVExporter\Menus\AdminHeader::register' => [],
			],
			'menu:csv_exporter' => [
				'\ColdTrick\CSVExporter\Menus\CSVExporter::register' => [],
			],
			'menu:entity' => [
				'\ColdTrick\CSVExporter\Menus\Entity::csvExport' => [],
			],
			'menu:filter:csv_exporter/group' => [
				\ColdTrick\CSVExporter\Menus\Filter\CSVExporterGroup::class => [],
			],
			'menu:page' => [
				'\ColdTrick\CSVExporter\Menus\Page::groupAdminMenu' => [],
			],
		],
		'setting' => [
			'plugin' => [
				'\ColdTrick\CSVExporter\Plugin::saveSettings' => [],
			],
		],
	],
	'notifications' => [
		'object' => [
			'csv_export' => [
				'complete' => \ColdTrick\CSVExporter\Notifications\CompleteExportHandler::class,
			],
		],
	],
];
