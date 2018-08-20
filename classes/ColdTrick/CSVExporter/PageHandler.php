<?php

namespace ColdTrick\CSVExporter;

class PageHandler {
	
	/**
	 * Handle /csv_exporter urls
	 *
	 * @param array $page URL segments
	 *
	 * @return bool
	 */
	public static function csvExporter($page) {
		
		$vars = [];
		
		switch (elgg_extract(0, $page)) {
			case 'group':
				$vars['guid'] = (int) elgg_extract(1, $page);
				$vars['filter'] = elgg_extract(2, $page, 'configure');
				
				echo elgg_view_resource('csv_exporter/group', $vars);
				return true;
		}
		
		return false;
	}
}
