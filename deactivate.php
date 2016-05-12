<?php
/**
 * This is run when the plugin gets deactivated
 */

update_subtype('object', CSVExport::SUBTYPE);
