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

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config;

global $sugar_config;

$moduleName = 'Users';
$idpConfig = new Config(\SugarConfig::getInstance());
$isIDMModeEnabled = $idpConfig->isIDMModeEnabled();
if ($isIDMModeEnabled) {
    $newUserLink = [
        'route' => $idpConfig->buildCloudConsoleUrl('userCreate'),
        'openwindow' => true,
        'label' => 'LNK_NEW_USER',
        'acl_action' => 'admin',
        'acl_module' => $moduleName,
        'icon' => 'sicon-plus',
    ];
} else {
    $newUserLink = [
        'route' => '#' . $moduleName . '/create',
        'label' => 'LNK_NEW_USER',
        'acl_action' => 'admin',
        'acl_module' => $moduleName,
        'icon' => 'sicon-plus',
    ];
}

$viewdefs[$moduleName]['base']['menu']['header'] = [
    $newUserLink,
    [
        'route' => '#' . $moduleName . '/create/group',
        'label' => 'LNK_NEW_GROUP_USER',
        'acl_action' => 'admin',
        'acl_module' => $moduleName,
        'icon' => 'sicon-plus',
    ],
];
if (!empty($sugar_config['enable_web_services_user_creation'])) {
    $viewdefs[$moduleName]['base']['menu']['header'][] =
        [
            'route' => '#' . $moduleName . '/create/portalapi',
            'label' => 'LNK_NEW_PORTAL_USER',
            'acl_action' => 'admin',
            'acl_module' => $moduleName,
            'icon' => 'sicon-plus',
        ];
}
$viewdefs[$moduleName]['base']['menu']['header'][] =
    [
        'route' => '#' . $moduleName . '/create/portalapi',
        'label' => 'LNK_NEW_PORTAL_USER',
        'acl_action' => 'admin',
        'acl_module' => $moduleName,
        'icon' => 'sicon-plus',
    ];

$viewdefs[$moduleName]['base']['menu']['header'][] = [
    'route' => '#Users/copy-content',
    'label' => 'LNK_COPY_CONTENT',
    'acl_action' => 'admin',
    'acl_module' => $moduleName,
    'icon' => 'sicon-copy',
];
$viewdefs[$moduleName]['base']['menu']['header'][] = [
    'route' => '#Users/update-locale',
    'label' => 'LNK_COPY_USER_SETTINGS',
    'acl_action' => 'admin',
    'acl_module' => $moduleName,
    'icon' => 'sicon-copy',
];

$viewdefs[$moduleName]['base']['menu']['header'][] = [
    'route' => '#bwc/index.php?' . http_build_query(
        [
                'module' => $moduleName,
                'action' => 'reassignUserRecords',
            ]
    ),
    'label' => 'LNK_REASSIGN_RECORDS',
    'acl_action' => 'admin',
    'acl_module' => $moduleName,
    'icon' => 'sicon-edit',
];

$viewdefs[$moduleName]['base']['menu']['header'][] = [
    'route' => '#bwc/index.php?' . http_build_query([
            'module' => 'Import',
            'action' => 'Step1',
            'import_module' => $moduleName,
            'return_module' => $moduleName,
            'return_action' => 'index',
        ]),
    'label' => 'LNK_IMPORT_USERS',
    'acl_action' => 'admin',
    'acl_module' => $moduleName,
    'icon' => 'sicon-upload',
];
