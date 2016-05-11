CSV Exporter
============

[![Build Status](https://scrutinizer-ci.com/g/ColdTrick/csv_exporter/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ColdTrick/csv_exporter/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ColdTrick/csv_exporter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ColdTrick/csv_exporter/?branch=master)
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

You can add your own values to the available list by registering a plugin hook like  

`elgg_register_plugin_hook_handler("get_exportable_values", "csv_exporter", "your function");` 
 
This hook get the following params:

- __type__: the type of the entity to supply the values for
- __subtype__: the subtype of the entity to supply the values for
- __readable__: true|false, if __true__ the result must be an associative array in the format array(label => value), 
this is used to display the checkboxes to the user.  
If __false__ just return the value as part of an array (eg array(value)).

In order to export the correct values you have to also register a plugin hook  

`elgg_register_plugin_hook_handler("export_value", "csv_exporter", "your function");`

This hook get the following params:

- __type__: the type of the entity to supply the values for
- __subtype__: the subtype of the entity to supply the values for
- __entity__: the entity for which to export the value
- __exportable_value__: the value to export

If you return anything other than __null__ this value will be used, otherwise the system will try to get the __exportable_value__ as a metadata field.