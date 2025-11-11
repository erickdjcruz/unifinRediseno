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


$extensions = [
    'actionviewmap' => ['section' => 'action_view_map', 'extdir' => 'ActionViewMap', 'file' => 'action_view_map.ext.php'],
    'actionfilemap' => ['section' => 'action_file_map', 'extdir' => 'ActionFileMap', 'file' => 'action_file_map.ext.php'],
    'actionremap' => ['section' => 'action_remap', 'extdir' => 'ActionReMap', 'file' => 'action_remap.ext.php'],
    'administration' => ['section' => 'administration', 'extdir' => 'Administration', 'file' => 'administration.ext.php', 'module' => 'Administration'],
    'dependencies' => ['section' => 'dependencies', 'extdir' => 'Dependencies', 'file' => 'deps.ext.php'],
    'entrypoints' => ['section' => 'entrypoints', 'extdir' => 'EntryPointRegistry', 'file' => 'entry_point_registry.ext.php', 'module' => 'application'],
    'exts' => ['section' => 'extensions', 'extdir' => 'Extensions', 'file' => 'extensions.ext.php', 'module' => 'application'],
    'file_access' => ['section' => 'file_access', 'extdir' => 'FileAccessControlMap', 'file' => 'file_access_control_map.ext.php'],
    'languages' => ['section' => 'language', 'extdir' => 'Language', 'file' => '' /* custom rebuild */],
    'dropdown_filters' => [
        'section' => 'dropdown_filters',
        'extdir' => 'DropdownFilters',
        'file' => 'dropdownfilters.ext.php',
    ],
    'layoutdefs' => ['section' => 'layoutdefs', 'extdir' => 'Layoutdefs', 'file' => 'layoutdefs.ext.php'],
    'links' => ['section' => 'linkdefs', 'extdir' => 'GlobalLinks', 'file' => 'links.ext.php', 'module' => 'application'],
    'logichooks' => ['section' => 'hookdefs', 'extdir' => 'LogicHooks', 'file' => 'logichooks.ext.php'],
    'tinymce' => ['section' => 'tinymce', 'extdir' => 'TinyMCE', 'file' => 'tinymce.ext.php'],
    'menus' => ['section' => 'menu', 'extdir' => 'Menus', 'file' => 'menu.ext.php'],
    'modules' => ['section' => 'beans', 'extdir' => 'Include', 'file' => 'modules.ext.php', 'module' => 'application'],
    'schedulers' => ['section' => 'scheduledefs', 'extdir' => 'ScheduledTasks', 'file' => 'scheduledtasks.ext.php', 'module' => 'Schedulers'],
    'app_schedulers' => ['section' => 'appscheduledefs', 'extdir' => 'ScheduledTasks', 'file' => 'scheduledtasks.ext.php', 'module' => 'application'],
    'userpage' => ['section' => 'user_page', 'extdir' => 'UserPage', 'file' => 'userpage.ext.php', 'module' => 'Users'],
    'utils' => ['section' => 'utils', 'extdir' => 'Utils', 'file' => 'custom_utils.ext.php', 'module' => 'application'],
    'vardefs' => ['section' => 'vardefs', 'extdir' => 'Vardefs', 'file' => 'vardefs.ext.php'],
    'jsgroupings' => ['section' => 'jsgroups', 'extdir' => 'JSGroupings', 'file' => 'jsgroups.ext.php'],
    'wireless_modules' => ['section' => 'wireless_modules', 'extdir' => 'WirelessModuleRegistry', 'file' => 'wireless_module_registry.ext.php'],
    'wireless_subpanels' => ['section' => 'wireless_subpanels', 'extdir' => 'WirelessLayoutdefs', 'file' => 'wireless.subpaneldefs.ext.php'],
    'tabledictionary' => ['section' => '', 'extdir' => 'TableDictionary', 'file' => 'tabledictionary.ext.php', 'module' => 'application'],

    'sidecar' => ['section' => 'sidecar', 'extdir' => 'clients/__PH_PLATFORM__/__PH_TYPE__/__PH_SUBTYPE__', 'file' => '__PH_SUBTYPE__.ext.php'],

    // Extention framework support for console commands
    'console' => [
        'section' => 'console',
        'extdir' => 'Console',
        'file' => 'console.ext.php',
        'module' => 'application',
    ],
    'platforms' => [
        'section' => 'platforms',
        'extdir' => 'Platforms',
        'file' => 'platforms.ext.php',
        'module' => 'application',
    ],
    'platformoptions' => [
        'section' => 'platforms',
        'extdir' => 'Platforms',
        'file' => 'platformoptions.ext.php',
        'module' => 'application',
    ],
];
if (SugarAutoLoader::existing('custom/application/Ext/Extensions/extensions.ext.php')) {
    include 'custom/application/Ext/Extensions/extensions.ext.php';
}
