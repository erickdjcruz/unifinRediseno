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

$viewdefs['<module_name>']['base']['view']['activity-card-definition'] = [
    'module' => '<module_name>',
    'record_date' => 'date_linked',
    'date_tooltip' => 'LBL_DATE_LINKED',
    'fields' => [
        'name',
        'date_linked',
        'billing_address_city',
        'billing_address_state',
        'billing_address_country',
        'assigned_user_name',
        'phone_office',
        'email',
        'assigned_user_name',
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
