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

$viewdefs['Notes']['base']['view']['activity-card-definition'] = [
    'module' => 'Notes',
    'record_date' => 'date_entered',
    'fields' => [
        'name',
        'contact_name',
        'description',
        'filename',
        'date_entered_by',
        'created_by_name',
        'date_modified_by',
        'assigned_user_name',
        'modified_by_name',
        'attachment_list',
        'portal_flag',
    ],
    'card_menu' => [
        [
            'type' => 'cab_actiondropdown',
            'buttons' => [
                [
                    'type' => 'unlinkcab',
                    'icon' => 'sicon-unlink',
                    'label' => 'LBL_UNLINK_BUTTON',
                ],
            ],
        ],
    ],
];
