<?php

namespace ColdTrick\CSVExporter;

class Cron {
	
	/**
	 * Process the scheduled exports
	 *
	 * @param \Elgg\Hook $hook 'cron', 'minute'
	 *
	 * @return void
	 */
	public static function processExports(\Elgg\Hook $hook) {
		
		echo 'Starting CSVExporter processing' . PHP_EOL;
		elgg_log('Starting CSVExporter processing', 'NOTICE');
		
		$time = (int) $hook->getParam('time', time());
		
		// ignore access
		elgg_call(ELGG_IGNORE_ACCESS, function () use ($time) {
			
			/* @var $batch \ElggBatch */
			$batch = elgg_get_entities([
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
				'batch' => true,
				'batch_inc_offset' => false,
			]);
			/* @var $csv_export \CSVExport */
			foreach ($batch as $csv_export) {
				if ($csv_export->isProcessing()) {
					elgg_log("CSV export '{$csv_export->getDisplayName()}' is already processing: {$csv_export->started}", 'NOTICE');
					continue;
				}
				
				$csv_export->process();
			}
		});
		
		echo 'Done with CSVExporter processing' . PHP_EOL;
		elgg_log('Done with CSVExporter processing', 'NOTICE');
	}
	
	/**
	 * Cleanup the old exports
	 *
	 * @param \Elgg\Hook $hook 'cron', 'daily'
	 *
	 * @return void
	 */
	public static function cleanupExports(\Elgg\Hook $hook) {
		
		$time = (int) $hook->getParam('time', time());
		$retention = (int) elgg_get_plugin_setting('retention', 'csv_exporter');
		if ($retention < 1) {
			// no cleanup
			return;
		}
		
		echo 'Starting CSVExporter cleanup' . PHP_EOL;
		elgg_log('Starrting CSVExporter cleanup', 'NOTICE');
		
		// ignore access
		elgg_call(ELGG_IGNORE_ACCESS, function() use($time, $retention) {
			/* @var $batch \ElggBatch */
			$batch = elgg_get_entities([
				'type' => 'object',
				'subtype' => \CSVExport::SUBTYPE,
				'limit' => false,
				'metadata_name_value_pairs' => [
					'name' => 'completed',
					'value' => strtotime("today -{$retention} days", $time),
					'operand' => '<',
				],
				'batch' => true,
				'batch_inc_offset' => false,
			]);
			/* @var $entity \CSVExport */
			foreach ($batch as $entity) {
				$entity->delete();
			}
		});
		
		echo 'Done with CSVExporter cleanup' . PHP_EOL;
		elgg_log('Done with CSVExporter cleanup', 'NOTICE');
	}
}
