<?php

namespace ColdTrick\CSVExporter;

class Cron {
	
	/**
	 * Process the scheduled exports
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param string $return_value current return value
	 * @param array  $params       supplied params
	 *
	 * @return void
	 */
	public static function processExports($hook, $type, $return_value, $params) {
		
		$time = (int) elgg_extract('time', $params, time());
		
		$options = [
			'type' => 'object',
			'subtype' => \CSVExport::SUBTYPE,
			'limit' => 10,
			'metadata_name_value_pairs' => [
				'name' => 'scheduled',
				'value' => $time,
				'operand' => '<',
			],
			'order_by_metadata' => [
				'name' => 'scheduled',
				'direction' => 'asc',
				'as' => 'integer',
			],
		];
		// ignore access
		$ia = elgg_set_ignore_access(true);
		
		$batch = new \ElggBatch('elgg_get_entities_from_metadata', $options);
		$batch->setIncrementOffset(false);
		/* @var $csv_export \CSVExport */
		foreach ($batch as $csv_export) {
			if ($csv_export->isProcessing()) {
				elgg_log("CSV export '{$csv_export->getDisplayName()}' is already processing: {$csv_export->started}", 'NOTICE');
				continue;
			}
			
			$csv_export->process();
		}
		
		// restore access
		elgg_set_ignore_access($ia);
	}
}
