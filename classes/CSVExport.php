<?php

class CSVExport extends ElggObject {
	
	const SUBTYPE = 'csv_export';
	
	/* @var $form_data array */
	protected $form_data;
	
	/**
	 * (non-PHPdoc)
	 * @see ElggObject::initializeAttributes()
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		
		$this->attributes['subtype'] = self::SUBTYPE;
		$this->attributes['access_id'] = ACCESS_PRIVATE;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ElggObject::getDisplayName()
	 */
	public function getDisplayName() {
		$type = $this->getFormData('type');
		$subtype = $this->getFormData('subtype');
		
		$lan_keys = [
			"collection:{$type}:{$subtype}",
			"item:{$type}:{$subtype}",
			"item:{$type}",
		];
		$content_type = '';
		foreach ($lan_keys as $lan_key) {
			if (elgg_language_key_exists($lan_key)) {
				$content_type = elgg_echo($lan_key);
				break;
			}
		}
		
		if ($this->title) {
			return elgg_echo('csv_exporter:object:csv_export:title:custom', [$content_type, $this->title]);
		}
		
		return elgg_echo('csv_exporter:object:csv_export:title', [$content_type]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ElggObject::canComment()
	 */
	public function canComment($user_guid = 0, $default = null) {
		return false;
	}
	
	/**
	 * Get data from the csv configuration
	 *
	 * @param string $field (optional) the field to get, leave empty for all fields
	 *
	 * @return void|string|array
	 */
	public function getFormData($field = '') {
		
		if (!isset($this->form_data)) {
			$this->form_data = json_decode($this->description, true);
		}
		if (empty($this->form_data) || !is_array($this->form_data)) {
			return;
		}
		
		if (empty($field)) {
			return $this->form_data;
		}
		
		if (($field === 'type') || ($field === 'subtype')) {
			$type_subtype = $this->getFormData('type_subtype');
			if (!is_string($type_subtype)) {
				return;
			}
			list($type, $subtype) = explode(':', $type_subtype);
			if ($field == 'type') {
				return $type;
			} else {
				return $subtype;
			}
		}
		
		return elgg_extract($field, $this->form_data);
	}
	
	/**
	 * Check if this export is scheduled for processing
	 *
	 * @return bool
	 */
	public function isProcessing() {
		return isset($this->started);
	}
	
	/**
	 * Check if this export is scheduled for processing
	 *
	 * @return bool
	 */
	public function isScheduled() {
		return isset($this->scheduled);
	}
	
	/**
	 * Check if this export is completed and ready for download
	 *
	 * @return bool
	 */
	public function isCompleted() {
		return isset($this->completed);
	}
	
	/**
	 * Get the download url for this export
	 *
	 * @return false|string
	 */
	public function getDownloadURL() {
		
		$fo = $this->getFileObject();
		if (empty($fo) || !$fo->exists()) {
			return false;
		}
		
		return elgg_get_download_url($fo, true);
	}
	
	/**
	 * Process the export to generate the downloadable file
	 *
	 * @return void
	 */
	public function process() {
		
		if ($this->isProcessing()) {
			return;
		}
		
		// lock this entity in order to prevent duplicate processing
		$this->lockProcessing();
		
		// get content type/subtype
		$type = $this->getFormData('type');
		$subtype = $this->getFormData('subtype');
		
		// get configured export fields
		$exportable_values = $this->getFormData('exportable_values');
		
		// prepare values for export
		$column_config = csv_exporter_prepare_exportable_columns($exportable_values, $type, $subtype);
		
		// check hook results
		if (empty($column_config) || !is_array($column_config)) {
			$this->unlockProcessing();
			return;
		}
		
		// prepare for exporting
		$fo = $this->getFileObject();
		$seperator = csv_exporter_get_separator();
		$exportable_values = array_keys($column_config);
		
		// make csv header row
		$headers = array_values($column_config);
		
		// create the new file with the headers
		$fh = $fo->open('write');
		fputcsv($fh, $headers, $seperator);
		
		// append the rest of the data
		$fh = $fo->open('append');
		
		// set entity options
		$entity_options = [
			'type' => $type,
			'subtype' => $subtype,
			'limit' => false,
			'batch' => true,
		];
		
		// limit to group content?
		if ($this->getContainerEntity() instanceof ElggGroup) {
			$entity_options['container_guid'] = $this->container_guid;
		}
		
		// add time constraints
		$this->addTimeContraints($entity_options);
		
		// this could take a while
		set_time_limit(0);
		
		$batch_processing = 0;
		/* @var $entities ElggBatch */
		$entities = elgg_get_entities($entity_options);
		/* @var $entity ElggEntity */
		foreach ($entities as $entity) {
			$batch_processing++;
			$values = [];
			
			// params for hook
			$params = [
				'type' => $type,
				'subtype' => $subtype,
				'entity' => $entity,
			];
			
			foreach ($exportable_values as $export_value) {
				$params['exportable_value'] = $export_value;
				
				$value = elgg_trigger_plugin_hook('export_value', 'csv_exporter', $params);
				if ($value === null) {
					$value = $entity->$export_value;
				}
				
				if (is_array($value)) {
					$value = implode(', ', $value);
				}
				
				$values[] = $value;
			}
			
			// write row
			fputcsv($fh, $values, $seperator);
			
			// clean up some memory
			if ($batch_processing >= 100) {
				$batch_processing = 0;
				
				$this->clearCaches();
			}
		}
		
		// cleanup
		$this->complete();
		$this->unlockProcessing();
		
		$fo->close();
	}
	
	/**
	 * Lock the entity to prevent duplicate processing
	 *
	 * @return void
	 */
	protected function lockProcessing() {
		$this->started = time();
	}
	
	/**
	 * Unlock the processing mutex
	 *
	 * @return void
	 */
	public function unlockProcessing() {
		unset($this->started);
	}
	
	/**
	 * Get a file handler to work with
	 *
	 * @return false|ElggFile
	 */
	protected function getFileObject() {
		
		if (!$this->guid) {
			return false;
		}
		
		$filename = $this->filename;
		if (empty($filename)) {
			$filename = elgg_get_friendly_title($this->getDisplayName());
			$this->filename = $filename;
		}
		
		$fh = new ElggFile();
		$fh->owner_guid = $this->guid;
		
		$fh->setFilename("{$filename}.csv");
		
		return $fh;
	}
	
	/**
	 * Add the time contraints to the entity options
	 *
	 * @param array $options the current entity options
	 *
	 * @return void
	 */
	protected function addTimeContraints(&$options) {
		
		// add time constraints
		$time = $this->getFormData('time');
		if (empty($time)) {
			return;
		}
		
		switch ($time) {
			case 'today':
				$options['created_time_lower'] = strtotime('today');
				break;
			case 'yesterday':
				$options['created_time_lower'] = strtotime('yesterday');
				$options['created_time_upper'] = strtotime('today');
				break;
			case 'this_week':
				if (date('w') == 1) {
					// today is monday
					$options['created_time_lower'] = strtotime('today');
				} else {
					$options['created_time_lower'] = strtotime('last monday');
				}
				break;
			case 'last_week':
				if (date('w') == 1) {
					// today is monday
					$options['created_time_lower'] = strtotime('today -1 week');
					$options['created_time_upper'] = strtotime('today');
				} else {
					$options['created_time_lower'] = strtotime('last monday -1 week');
					$options['created_time_upper'] = strtotime('last monday');
				}
				break;
			case 'this_month':
				$options['created_time_lower'] = strtotime('first day of this month 00:00:00');
				break;
			case 'last_month':
				$options['created_time_lower'] = strtotime('first day of last month 00:00:00');
				$options['created_time_upper'] = strtotime('first day of this month 00:00:00');
				break;
			case 'range':
				$options['created_time_lower'] = $this->getFormData('created_time_lower');
				$options['created_time_upper'] = $this->getFormData('created_time_upper');
				break;
		}
	}
	
	/**
	 * Mark the processing as complete
	 * - notify the user
	 * - set correct timestamps
	 *
	 * @return void
	 */
	protected function complete() {
		
		unset($this->scheduled);
		$this->completed = time();
		
		$title = $this->getDisplayName();
		if ($this->getContainerEntity() instanceof ElggGroup) {
			// group export
			$download_link = elgg_generate_url('collection:object:csv_export:group', [
				'guid' => $this->container_guid,
				'filter' => 'download',
			]);
		} else {
			// admin export
			$download_link = 'admin/administer_utilities/csv_exporter/download';
		}
		$owner = $this->getOwnerEntity();
		
		$subject = elgg_echo('csv_exporter:notify:complete:subject', [$title]);
		$message = elgg_echo('csv_exporter:notify:complete:message', [
			$owner->getDisplayName(),
			$title,
			elgg_normalize_url($download_link),
		]);
		
		$params = [
			'object' => $this,
			'action' => 'complete',
		];
		
		notify_user($owner->guid, $owner->guid, $subject, $message, $params, ['email']);
	}
	
	/**
	 * Clear caches to save memory
	 *
	 * @return void
	 */
	protected function clearCaches() {
		_elgg_services()->accessCache->clear();
		_elgg_services()->dataCache->clear();
		_elgg_services()->entityCache->clear();
		_elgg_services()->sessionCache->clear();
		_elgg_services()->queryCache->clear();
	}
}
