<?php

/**
 * Used to fetch exportable data
 *
 * @internal DON'T USE THIS
 */
final class CSVExportSample extends \ElggObject {
	
	public const SUBTYPE = 'csvexport_sample';
	
	/**
	 * {@inheritdoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		
		$this->attributes['subtype'] = self::SUBTYPE;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function save(): bool {
		return false;
	}
}
