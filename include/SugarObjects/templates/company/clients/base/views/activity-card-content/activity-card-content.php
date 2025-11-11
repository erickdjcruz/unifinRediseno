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

$viewdefs['<module_name>']['base']['view']['activity-card-content'] = [
    'panels' => [
        [
            'name' => 'panel_body_1',
            'label' => 'LBL_PANEL_1',
            'css_class' => 'panel-group flex',
            'fields' => [
                'billing_address_city',
                'billing_address_state',
                'billing_address_country',
            ],
        ],
        [
            'name' => 'panel_body_2',
            'label' => 'LBL_PANEL_2',
            'css_class' => 'panel-group flex',
            'fields' => [
                'phone_office',
                'email',
            ],
        ],
    ],
];
