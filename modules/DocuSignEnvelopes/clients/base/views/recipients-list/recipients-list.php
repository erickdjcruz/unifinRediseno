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
$viewdefs['DocuSignEnvelopes']['base']['view']['recipients-list'] = [
    'panels' => [
        [
            'name' => 'recipients-list-panel',
            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'base',
                    'enabled' => true,
                    'default' => true,
                    'label' => 'LBL_RECIPIENT_NAME',
                    'sortable' => false,
                ],
                [
                    'name' => 'email',
                    'type' => 'base',
                    'enabled' => true,
                    'default' => true,
                    'label' => 'LBL_RECIPIENT_EMAIL',
                    'sortable' => false,
                ],
                [
                    'name' => 'type',
                    'type' => 'enum',
                    'enabled' => true,
                    'default' => true,
                    'label' => 'LBL_RECIPIENT_TYPE',
                    'sortable' => false,
                    'options' => 'docusign_recipient_type_list',
                ],
                [
                    'name' => 'role',
                    'type' => 'docusign-recipient-role',
                    'enabled' => true,
                    'default' => true,
                    'label' => 'LBL_RECIPIENT_ROLE',
                    'sortable' => false,
                    'options' => [],
                ],
            ],
        ],
    ],
];
