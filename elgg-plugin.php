<?php

return [
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
