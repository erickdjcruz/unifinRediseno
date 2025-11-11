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
$module_name = 'Employees';
$newEmployeeLink = [
    'route' => '#bwc/index.php?' . http_build_query([
            'module' => $module_name,
            'action' => 'EditView',
        ]),
    'label' => 'LNK_NEW_EMPLOYEE',
    'acl_action' => 'admin',
    'acl_module' => $module_name,
    'icon' => 'sicon-plus',
];
$viewdefs[$module_name]['base']['menu']['header'] = [
    $newEmployeeLink,
    [
        'route' => '#' . $module_name,
        'label' => 'LNK_EMPLOYEE_LIST',
        'acl_action' => 'list',
        'acl_module' => $module_name,
        'icon' => 'sicon-list-view',
    ],
];
