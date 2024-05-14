CSV Exporter
============

![Elgg 6.0](https://img.shields.io/badge/Elgg-6.0-green.svg)
![Lint Checks](https://github.com/ColdTrick/csv_exporter/actions/workflows/lint.yml/badge.svg?event=push)
[![Latest Stable Version](https://poser.pugx.org/coldtrick/csv_exporter/v/stable.svg)](https://packagist.org/packages/coldtrick/csv_exporter)
[![License](https://poser.pugx.org/coldtrick/csv_exporter/license.svg)](https://packagist.org/packages/coldtrick/csv_exporter)

This plugin allows you to export all searchable entities to a CSV.

How to
------

As an administrator go to the Admin section. Under Administer -> Utilities you can find the CSV Exporter menu item.

If a few easy steps you can select the CSV content.

1.  Select what you wish to export (Users, Groups, Blogs etc.)
2.  Once you've selected what you wish to export you get a choice of which columns to include
3.  Now you can click on Preview to check if this is what you wish to export  
or you can click Download CSV to export all the content to a CSV file

Developers
----------

By default all searchable entities are exportable, if however you wish to change this list use the event

`elgg_register_event_handler("allowed_type_subtypes", "csv_exporter", "your function");`

and change the result array.

You can add your own values to the available list by registering a event like  

`elgg_register_event_handler("get_exportable_values", "csv_exporter", "your function");` 

This event gets the following params:

- __type__: the type of the entity to supply the values for
- __subtype__: the subtype of the entity to supply the values for
- __readable__: true|false, if __true__ the result must be an associative array in the format `array(label => value)`, 
this is used to display the checkboxes to the user.  
If __false__ just return the value as part of an array (eg array(value)).

In order to export the correct values you have to also register an event 

`elgg_register_event_handler("export_value", "csv_exporter", "your function");`

This event gets the following params:

- __type__: the type of the entity to supply the values for
- __subtype__: the subtype of the entity to supply the values for
- __entity__: the entity for which to export the value
- __exportable_value__: the value to export

If you return anything other than __null__ this value will be used, otherwise the system will try to get the 
__exportable_value__ as a metadata field.

In order to allow group admins to export basic information add the configuration with the following event:  

`elgg_register_event_handler("get_exportable_values:group", "csv_exporter", "your function");` 
 
This event gets the following params:

- __type__: the type of the entity to supply the values for
- __subtype__: the subtype of the entity to supply the values for

Return an array with `[value]` where:

- __value__: the same name as in the `get_exportable_values`, `csv_exporter` event
