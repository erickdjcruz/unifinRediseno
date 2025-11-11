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

$viewdefs['Meetings']['base']['view']['activity-card-definition'] = [
    'module' => 'Meetings',
    'record_date' => 'date_start',
    'fields' => [
        'name',
        'status',
        'duration',
        'type',
        'description',
        'invitees',
        'data_entered_by',
        'date_modified_by',
        'assigned_user_name',
        'date_start',
        'date_end',
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
