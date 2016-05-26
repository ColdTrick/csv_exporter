<?php

namespace ColdTrick\CSVExporter;

class Upgrade {
	
	/**
	 * Listen to the upgrade event to set the correct class handler
	 *
	 * @param string $event  the name of the event
	 * @param string $type   the type of the event
	 * @param null   $object supplied param
	 *
	 * @return void
	 */
	public static function setClassHandler($event, $type, $object) {
		
		if (get_subtype_id('object', \CSVExport::SUBTYPE)) {
			update_subtype('object', \CSVExport::SUBTYPE, 'CSVExport');
		} else {
			add_subtype('object', \CSVExport::SUBTYPE, 'CSVExport');
		}
	}
}
