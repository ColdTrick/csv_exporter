<?php

// add tab menu
echo elgg_view_menu('csv_exporter', [
	'class' => 'elgg-menu-hz elgg-tabs',
	'sort_by' => 'priority',
]);

echo elgg_list_entities([
	'type' => 'object',
	'subtype' => \CSVExport::SUBTYPE,
	'metadata_name' => 'completed',
	'sort_by' => [
		'property' => 'completed',
		'direction' => 'desc',
		'signed' => true,
	],
	'wheres' => [
		function(\Elgg\Database\QueryBuilder $qb, $main_alias) {
			$groups = $qb->subquery('entities');
			$groups->select('guid')
				->where($qb->compare('type', '=', 'group', ELGG_VALUE_STRING));
			
			return $qb->compare("{$main_alias}.container_guid", 'not in', $groups->getSQL());
		},
	],
	'no_results' => elgg_echo('csv_exporter:download:none'),
]);
