<?php
/**
 * This is run when the plugin gets activated
 */

if (get_subtype_id('object', CSVExport::SUBTYPE)) {
	update_subtype('object', CSVExport::SUBTYPE, 'CSVExport');
} else {
	add_subtype('object', CSVExport::SUBTYPE, 'CSVExport');
}
