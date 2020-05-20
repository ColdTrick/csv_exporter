<?php

return array(
	
	'item:object:csv_export' => "CSV Export configuration",
	'collection:object:csv_export' => "CSV Export configurations",
	'admin:administer_utilities:csv_exporter' => "CSV Exporter",
	'admin:administer_utilities:csv_exporter:download' => "All CSV exports",
	'admin:administer_utilities:csv_exporter:downloads' => "Download CSV exports",
	'admin:administer_utilities:csv_exporter:scheduled' => "Scheduled CSV exports",
	'csv_exporter:menu:group_profile' => "Group CSV Exports",
	
	'csv_exporter:menu:csv_exporter:configure' => "Configure",
	'csv_exporter:menu:csv_exporter:downloads' => "Downloads",
	'csv_exporter:menu:csv_exporter:download' => "My downloads",
	'csv_exporter:menu:csv_exporter:download:all' => "All downloads",
	'csv_exporter:menu:csv_exporter:scheduled' => "Scheduled",
	
	'csv_exporter:object:csv_export:title' => "CSV export for: %s",
	'csv_exporter:object:csv_export:title:custom' => "CSV export for: %s - %s",
	'csv_exporter:object:csv_export:processing' => "Processing started: %s",
	'csv_exporter:object:csv_export:scheduled' => "Scheduled for processing",
	'csv_exporter:object:csv_export:completed' => "Ready for download: %s",
	
	'csv_exporter:settings:separator' => "The separator to use in the CSV file",
	'csv_exporter:settings:retention' => "Cleanup finished exports after x days (optional)",
	'csv_exporter:settings:retention:description' => "If you create many exports they can take up some diskspace. Here you can configure after how many days the finished exports will be removed automaticly. Leave it empty (or 0) to never cleanup any exports.",
	
	'csv_exporter:settings:group:title' => "Group admin settings",
	'csv_exporter:settings:group:subtypes' => "Allow group admins to export basic information about the following content",
	'csv_exporter:settings:group:subtypes:help' => "Group admins don't get to choose which columns to export, but get a predefined set of columns.
Plugins can extend this list of columns",
	
	'csv_exporter:exportable_value:guid' => "GUID",
	'csv_exporter:exportable_value:type' => "Type",
	'csv_exporter:exportable_value:subtype' => "Subtype",
	'csv_exporter:exportable_value:time_created' => "Time created",
	'csv_exporter:exportable_value:time_updated' => "Time updated",
	'csv_exporter:exportable_value:container_guid' => "Container GUID",
	'csv_exporter:exportable_value:owner_guid' => "Owner GUID",
	'csv_exporter:exportable_value:language' => "Language",
	'csv_exporter:exportable_value:owner_name' => "Owner name",
	'csv_exporter:exportable_value:owner_username' => "Owner username",
	'csv_exporter:exportable_value:owner_email' => "Owner email address",
	'csv_exporter:exportable_value:owner_url' => "Owner profile URL",
	'csv_exporter:exportable_value:container_name' => "Container name",
	'csv_exporter:exportable_value:container_username' => "Container username",
	'csv_exporter:exportable_value:container_email' => "Container email address",
	'csv_exporter:exportable_value:container_url' => "Container profile URL",
	'csv_exporter:exportable_value:time_created_readable' => "Time created (readable)",
	'csv_exporter:exportable_value:time_updated_readable' => "Time updated (readable)",
	'csv_exporter:exportable_value:url' => "Entity URL",
	'csv_exporter:exportable_value:access_id' => "Access",
	'csv_exporter:exportable_value:access_id_readable' => "Access (readable)",
	'csv_exporter:exportable_value:icontime' => "Icon upload time",
	'csv_exporter:exportable_value:icontime_readable' => "Icon upload time (readable)",
	'csv_exporter:exportable_value:icon_url:master' => "Icon url",
	
	'csv_exporter:exportable_value:user:last_action' => "Last action",
	'csv_exporter:exportable_value:user:last_action_readable' => "Last action (readable)",
	'csv_exporter:exportable_value:user:groups_owned_name' => "Groups owned name",
	'csv_exporter:exportable_value:user:groups_owned_url' => "Groups owned url",
	'csv_exporter:exportable_value:user:banned' => "Banned",
	'csv_exporter:exportable_value:user:friends' => "Number of users followed",
	'csv_exporter:exportable_value:user:friends:of' => "Number of followers",
	
	'csv_exporter:exportable_value:group:membership' => "Membership",
	'csv_exporter:exportable_value:group:visibility' => "Visibility",
	'csv_exporter:exportable_value:group:member_count' => "Member count",
	'csv_exporter:exportable_value:group:last_activity' => "Last activity",
	'csv_exporter:exportable_value:group:last_activity_readable' => "Last activity (readable)",
	'csv_exporter:exportable_value:group:tools' => "Tools",
	'csv_exporter:exportable_value:group:tool' => "Tool: %s",
	'csv_exporter:exportable_value:group:content_stats' => "Content statistics",
	
	'csv_exporter:admin:type_subtype' => "What do you wish to export",
	'csv_exporter:admin:type_subtype:choose' => "Please choose from the list",
	
	'csv_exporter:admin:exportable_values' => "Which attributes do you wish to export",
	'csv_exporter:admin:exportable_values:choose' => "First select what you wish to export",
	
	'csv_exporter:admin:preview' => "Preview",
	'csv_exporter:admin:preview:title' => "This will be in the CSV",
	
	'csv_exporter:admin:schedule' => "Schedule",
	'csv_exporter:admin:schedule:description' => "Schedule the CSV to be created. You'll receive an e-mail when the CSV is available for download",
	
	'csv_exporter:admin:time' => "Limit export period",
	'csv_exporter:admin:time:description' => "This will limit the content being exported on the creation date of the content",
	'csv_exporter:admin:time:select' => "Select a period (optional)",
	'csv_exporter:admin:time:today' => "Today",
	'csv_exporter:admin:time:yesterday' => "Yesterday",
	'csv_exporter:admin:time:this_week' => "This week",
	'csv_exporter:admin:time:last_week' => "Last week",
	'csv_exporter:admin:time:this_month' => "This month",
	'csv_exporter:admin:time:last_month' => "Last month",
	'csv_exporter:admin:time:range' => "Date range",
	'csv_exporter:admin:time:range:created_time_lower' => "Start day",
	'csv_exporter:admin:time:range:created_time_upper' => "End day",
	
	'csv_exporter:admin:title' => "Title for export (optional)",
	
	// download page
	'csv_exporter:download:none' => "No CSV exports are ready for download",
	
	// scheduled page
	'csv_exporter:scheduled:none' => "No CSV exports are scheduled for processing",
	
	// notifications
	// on complete
	'csv_exporter:notify:complete:subject' => "Your CSV export '%s' is ready for download",
	'csv_exporter:notify:complete:message' => "Hi %s,
	
Your CSV export '%s' is ready for download.

You can find it in the overview here:
%s",
	
	// group config
	'csv_exporter:group:title' => "CSV export for: %s",
	'csv_exporter:forms:group:description' => "Below you can select a content type which you wish to export.
An export will be scheduled and you'll be notified once the export is ready for download.",
	'csv_exporter:forms:group:subtype' => "Which content type do you wish to export",
	
	// actions
	// edit
	'csv_exporter:action:edit:success' => "The export configuration was saved, you'll receive an e-mail when the download is available",
);
