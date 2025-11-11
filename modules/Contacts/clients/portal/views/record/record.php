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

$viewdefs['Contacts']['portal']['view']['record'] = [
    'hashSync' => false,
    'panels' => [
        [
            'name' => 'panel_header',
            'header' => true,
            'fields' => [
                [
                    'name' => 'picture',
                    'type' => 'avatar',
                    'size' => 'large',
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'dismiss_label' => true,
                    'type' => 'fullname',
                    'fields' => ['salutation', 'first_name', 'last_name'],
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'title',
                    'displayParams' => [
                        'colspan' => 2,
                    ],
                ],
                [
                    'name' => 'email',
                    'displayParams' => [
                        'colspan' => 2,
                    ],
                ],
                [
                    'name' => 'portal_password',
                    'type' => 'change-my-password',
                    'label' => 'LBL_CONTACT_EDIT_PASSWORD',
                    'displayParams' => [
                        'colspan' => 2,
                    ],
                ],
                [
                    'name' => 'phone_work',
                    'displayParams' => [
                        'colspan' => 2,
                    ],
                ],
                [
                    'name' => 'primary_address_street',
                    'displayParams' => [
                        'colspan' => 2,
                    ],
                ],
                [
                    'name' => 'primary_address_city',
                    'displayParams' => [
                        'colspan' => 2,
                    ],
                ],
                [
                    'name' => 'primary_address_state',
                    'options' => 'state_dom',
                    'displayParams' => [
                        'colspan' => 2,
                    ],
                ],
                [
                    'name' => 'primary_address_postalcode',
                    'displayParams' => [
                        'colspan' => 2,
                    ],
                ],
                [
                    'name' => 'primary_address_country',
                    'options' => 'countries_dom',
                    'displayParams' => [
                        'colspan' => 2,
                    ],
                ],
                [
                    'name' => 'preferred_language',
                    'type' => 'language',
                    'options' => 'available_language_dom',
                ],
            ],
        ],
    ],
];
