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

$moduleName = 'TeamNotices';
$viewdefs[$moduleName]['base']['menu']['header'] = [
    [
        'route' => '#bwc/index.php?module=Teams&action=EditView&return_module=Teams&return_action=index',
        'label' => 'LNK_NEW_TEAM',
        'acl_action' => 'create',
        'acl_module' => 'Teams',
        'icon' => 'sicon-plus',
    ],
    [
        'route' => '#Teams',
        'label' => 'LNK_LIST_TEAM',
        'acl_action' => 'list',
        'acl_module' => 'Teams',
        'icon' => 'sicon-list-view',
    ],
    [
        'route' => "#bwc/index.php?module=$moduleName&action=EditView",
        'label' => 'LNK_NEW_TEAM_NOTICE',
        'acl_action' => 'create',
        'acl_module' => 'TeamNotices',
        'icon' => 'sicon-plus',
    ],
    [
        'route' => "#bwc/index.php?module=$moduleName&action=index",
        'label' => 'LNK_LIST_TEAMNOTICE',
        'acl_action' => '',
        'acl_module' => '',
        'icon' => 'sicon-list-view',
    ],
];
