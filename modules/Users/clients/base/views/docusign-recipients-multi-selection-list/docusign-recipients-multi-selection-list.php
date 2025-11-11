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
$viewdefs['Users']['base']['view']['docusign-recipients-multi-selection-list'] = [
    'template' => 'flex-list',
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => true,
                ],
                [
                    'name' => 'email',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => '_module',
                    'type' => 'base',
                    'label' => 'LBL_MODULE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'role',
                    'type' => 'docusign-recipient-role',
                    'label' => 'LBL_RECIPIENT_ROLE',
                    'enabled' => true,
                    'default' => true,
                    'sortable' => false,
                    'options' => [],
                ],
            ],
        ],
    ],
];
