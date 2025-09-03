<?php

namespace ColdTrick\CSVExporter\Notifications;

use Elgg\Notifications\InstantNotificationEventHandler;

/**
 * Send a notification to the owner of the csv_export that it's ready for download
 */
class CompleteExportHandler extends InstantNotificationEventHandler {
	
	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationSubject(\ElggUser $recipient, string $method): string {
		$entity = $this->getEventEntity();
		if (!$entity instanceof \CSVExport) {
			return parent::getNotificationSubject($recipient, $method);
		}
		
		return elgg_echo('csv_exporter:notify:complete:subject', [$entity->getDisplayName()]);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationSummary(\ElggUser $recipient, string $method): string {
		$entity = $this->getEventEntity();
		if (!$entity instanceof \CSVExport) {
			return parent::getNotificationSummary($recipient, $method);
		}
		
		return elgg_echo('csv_exporter:notify:complete:subject', [$entity->getDisplayName()]);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationBody(\ElggUser $recipient, string $method): string {
		$entity = $this->getEventEntity();
		if (!$entity instanceof \CSVExport) {
			return parent::getNotificationBody($recipient, $method);
		}
		
		return elgg_echo('csv_exporter:notify:complete:message', [
			$entity->getDisplayName(),
			$this->getNotificationURL($recipient, $method),
		]);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationURL(\ElggUser $recipient, string $method): string {
		$entity = $this->getEventEntity();
		if (!$entity instanceof \CSVExport) {
			return parent::getNotificationURL($recipient, $method);
		}
		
		$container = $entity->getContainerEntity();
		if ($container instanceof \ElggGroup) {
			return elgg_generate_url('collection:object:csv_export:group', [
				'guid' => $container->guid,
				'filter' => 'download',
			]);
		}
		
		return elgg_generate_url('admin', [
			'segments' => 'administer_utilities/csv_exporter/download',
		]);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function getNotificationMethods(): array {
		return ['email'];
	}
}
