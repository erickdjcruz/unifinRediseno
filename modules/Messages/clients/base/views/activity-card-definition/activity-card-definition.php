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

$viewdefs['Messages']['base']['view']['activity-card-definition'] = [
    'module' => 'Messages',
    'record_date' => 'date_start',
    'fields' => [
        'name',
        'contact_name',
        'description',
        'direction',
        'date_start',
        'date_end',
        'conversation_link',
        'conversation',
        'assigned_user_name',
    ],
    'link' => 'message_invites',
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
