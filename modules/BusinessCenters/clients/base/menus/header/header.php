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

$moduleName = 'BusinessCenters';
$viewdefs[$moduleName]['base']['menu']['header'] = [
    [
        'label' => 'LNK_NEW_BUSINESS_CENTER',
        'acl_action' => 'create',
        'acl_module' => $moduleName,
        'icon' => 'sicon-plus',
        'route' => '#' . $moduleName . '/create',
    ],
    [
        'route' => '#' . $moduleName,
        'label' => 'LNK_BUSINESS_CENTERS_LIST',
        'acl_action' => 'list',
        'acl_module' => $moduleName,
        'icon' => 'sicon-list-view',
    ],
    [
        'route' => '#bwc/index.php?' . http_build_query(
            [
                    'module' => 'Import',
                    'action' => 'Step1',
                    'import_module' => $moduleName,
                ]
        ),
        'label' => 'LNK_IMPORT_BUSINESS_CENTERS',
        'acl_action' => 'import',
        'acl_module' => $moduleName,
        'icon' => 'sicon-upload',
    ],
];
