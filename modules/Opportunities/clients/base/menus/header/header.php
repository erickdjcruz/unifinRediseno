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
$module_name = 'Opportunities';
$viewdefs[$module_name]['base']['menu']['header'] = [
    [
        'route' => '#' . $module_name . '/create',
        'label' => 'LNK_NEW_OPPORTUNITY',
        'acl_action' => 'create',
        'acl_module' => $module_name,
        'icon' => 'sicon-plus',
    ],
    [
        'route' => '#' . $module_name,
        'label' => 'LNK_OPPORTUNITY_LIST',
        'acl_action' => 'list',
        'acl_module' => $module_name,
        'icon' => 'sicon-list-view',
    ],
    [
        'route' => '#Reports?filterModule=' . $module_name,
        'label' => 'LNK_OPPORTUNITY_REPORTS',
        'acl_action' => 'list',
        'acl_module' => 'Reports',
        'icon' => 'sicon-reports',
    ],
    [
        'route' => '#bwc/index.php?module=Import&action=Step1&import_module=Opportunities&return_module=Opportunities&return_action=index',
        'label' => 'LNK_IMPORT_OPPORTUNITIES',
        'acl_action' => 'import',
        'acl_module' => $module_name,
        'icon' => 'sicon-upload',
    ],
];
