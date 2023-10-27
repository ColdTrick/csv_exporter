<?php

namespace ColdTrick\CSVExporter;

/**
 * Cron handler
 */
class Cron {
	
	/**
	 * Process the scheduled exports
	 *
	 * @param \Elgg\Event $event 'cron', 'minute'
	 *
	 * @return void
	 */
	public static function processExports(\Elgg\Event $event): void {
		$time = (int) $event->getParam('time', time());
		
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
				'sort_by' => [
					'property' => 'scheduled',
					'direction' => 'asc',
					'signed' => true,
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
	}
	
	/**
	 * Cleanup the old exports
	 *
	 * @param \Elgg\Event $event 'cron', 'daily'
	 *
	 * @return void
	 */
	public static function cleanupExports(\Elgg\Event $event): void {
		$time = (int) $event->getParam('time', time());
		$retention = (int) elgg_get_plugin_setting('retention', 'csv_exporter');
		if ($retention < 1) {
			// no cleanup
			return;
		}
		
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
	}
}
