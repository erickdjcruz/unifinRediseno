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

$module_name = 'pmse_Inbox';
$viewdefs[$module_name]['base']['view']['reassignCases-list'] = [
    'template' => 'flex-list',
    'selection' => [
    ],
    'rowactions' => [
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'act_name',
                    'label' => 'LBL_PMSE_LABEL_CURRENT_ACTIVITY',
                    'default' => true,
                    'enabled' => true,
                    'link' => false,
                ],
                [
                    'name' => 'cas_delegate_date',
                    'label' => 'LBL_PMSE_LABEL_ACTIVITY_START_DATE',
                    'default' => true,
                    'enabled' => true,
                    'type' => 'datetimecombo',
                ],
                [
                    'name' => 'cas_expected_time',
                    'label' => 'LBL_PMSE_LABEL_EXPECTED_TIME',
                    'default' => true,
                    'enabled' => true,
                    'link' => false,
                ],
                [
                    'name' => 'cas_due_date',
                    'label' => 'LBL_PMSE_LABEL_DUE_DATE',
                    'default' => true,
                    'enabled' => true,
                    'type' => 'datetimecombo-colorcoded',
                    'css_class' => 'overflow-visible',
                ],
                [
                    'name' => 'assigned_user',
                    'label' => 'LBL_ACTIVITY_OWNER',
                    'link' => 'assigned_user_link',
                    'vname' => 'LBL_ASSIGNED_TO',
                    'rname' => 'full_name',
                    'type' => 'relate',
                    'reportable' => false,
                    'source' => 'non-db',
                    'table' => 'users',
                    'id_name' => 'id',
                    'module' => 'Users',
                    'duplicate_merge' => 'disabled',
                    'duplicate_on_record_copy' => 'always',
                    'sort_on' => [
                        0 => 'last_name',
                    ],
                    'view' => 'edit',
                ],

            ],
        ],
    ],
    'orderBy' => [
        'field' => 'act_name',
        'direction' => 'desc',
    ],
];
