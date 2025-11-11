<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

global $mod_strings;

// We suggest that if you wish to modify an existing layout, copy & paste the existing layout files to new files
// this will prevent conflicts with future upgrades.

// To add a layout, you will need to register the new file below and in the $layouts_dom array
// in modules/Quotes/language/<lang>.lang.php
global $layouts;
$layouts = [];

if (file_exists('custom/modules/Quotes/sugarpdf/sugarpdf.standard.php')) {
    $layouts['Standard'] = 'custom/modules/Quotes/sugarpdf/sugarpdf.standard.php';
}
if (file_exists('custom/modules/Quotes/sugarpdf/sugarpdf.invoice.php')) {
    $layouts['Invoice'] = 'custom/modules/Quotes/sugarpdf/sugarpdf.invoice.php';
}

/**
 * a kind of silly getter...
 * @returns array layout array
 */
function get_layouts()
{
    global $mod_strings;
    global $app_list_strings;
    global $layouts;
    $list = [];
    if (isset($layouts)) {
        foreach ($layouts as $key => $value) {
            if (array_key_exists($key, $app_list_strings['layouts_dom'])) { //bug 49954
                $list[$key] = $app_list_strings['layouts_dom'][$key];
            }
        }
    }
    return $list;
}

/**
 * gets a layout and "prints" it
 * @param array array of layouts
 */
function print_layout($layout)
{
    global $mod_strings;
    global $layouts;

    if (!isset($layouts[$layout])) {
        $GLOBALS['log']->fatal('quote layout is not registered in modules/Quotes/Layouts.php');
        sugar_die('quote layout is not registered in modules/Quotes/Layouts.php');
    } elseif (!is_file($layouts[$layout])) {
        $GLOBALS['log']->fatal('quote layout file does not exist: ' . $layouts[$layout]);
        sugar_die('quote layout file does not exist: ' . $layouts[$layout]);
    } else {
        include_once $layouts[$layout];
    }
}


foreach (SugarAutoLoader::existing('modules/Quotes/Layouts.override.php', 'custom/modules/Quotes/Layouts.php') as $file) {
    include_once $file;
}

if (isset($_REQUEST['layout'])) {
    print_layout($_REQUEST['layout']);
}//end if else traditional print layout
