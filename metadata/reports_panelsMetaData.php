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

$dictionary['reports_panels'] = [
    'table' => 'reports_panels',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'report_id' => [
            'name' => 'report_id',
            'type' => 'id',
        ],
        'user_id' => [
            'name' => 'user_id',
            'type' => 'id',
        ],
        'contents' => [
            'name' => 'contents',
            'type' => 'text',
            'default' => null,
        ],
        'default_panel' => [
            'name' => 'default_panel',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
        ],
        'report_type' => [
            'name' => 'report_type',
            'type' => 'varchar',
            'len' => '25',
            'default' => null,
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date record created',
            'readonly' => true,
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date record last modified',
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
        ],
    ],
    'indices' => [
        [
            'name' => 'reports_panelspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_report_id_user_id',
            'type' => 'index',
            'fields' => [
                'report_id',
                'user_id',
            ],
        ],
    ],
];
