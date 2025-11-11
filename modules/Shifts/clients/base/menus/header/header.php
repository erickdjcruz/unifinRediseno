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

$viewdefs['Shifts']['base']['menu']['header'] = [
    [
        'route' => '#Shifts/create',
        'label' => 'LNK_NEW_SHIFT',
        'acl_action' => 'create',
        'acl_module' => 'Shifts',
        'icon' => 'sicon-plus',
    ],
    [
        'route' => '#Shifts',
        'label' => 'LNK_VIEW_SHIFTS',
        'acl_action' => 'list',
        'acl_module' => 'Shifts',
        'icon' => 'sicon-list-view',
    ],
    [
        'route' => '#ShiftExceptions/create',
        'label' => 'LNK_NEW_SHIFT_EXCEPTION',
        'acl_action' => 'create',
        'acl_module' => 'ShiftExceptions',
        'icon' => 'sicon-plus',
    ],
    [
        'route' => '#ShiftExceptions',
        'label' => 'LNK_VIEW_SHIFT_EXCEPTIONS',
        'acl_action' => 'list',
        'acl_module' => 'ShiftExceptions',
        'icon' => 'sicon-list-view',
    ],
];
