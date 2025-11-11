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

$viewdefs['Escalations']['base']['view']['activity-card-definition'] = [
    'module' => 'Escalations',
    'record_date' => 'date_entered',
    'fields' => [
        'name',
        'date_entered',
        'escalation_number',
        'assigned_user_name',
        'parent_name',
        'parent_type',
        'status',
        'source',
        'reason',
        'description',
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
