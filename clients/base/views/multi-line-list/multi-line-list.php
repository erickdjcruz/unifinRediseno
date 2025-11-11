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
$viewdefs['base']['view']['multi-line-list'] = [
    'rowactions' => [
        'actions' => [
            [
                'type' => 'rowaction',
                'label' => 'LBL_EDIT_IN_NEW_TAB',
                'tooltip' => 'LBL_EDIT_IN_NEW_TAB',
                'event' => 'list:editrow:fire',
                'icon' => 'sicon-edit',
                'acl_action' => 'edit',
            ],
            [
                'type' => 'rowaction',
                'label' => 'LBL_COPY_RECORD_URL',
                'tooltip' => 'LBL_COPY_RECORD_URL',
                'event' => 'list:copyrow:fire',
                'icon' => 'sicon-copy',
                'acl_action' => 'view',
            ],
            [
                'type' => 'rowaction',
                'label' => 'LBL_OPEN_IN_NEW_TAB',
                'tooltip' => 'LBL_OPEN_IN_NEW_TAB',
                'event' => 'list:openrow:fire',
                'icon' => 'sicon-launch',
                'acl_action' => 'view',
            ],
        ],
    ],
];
