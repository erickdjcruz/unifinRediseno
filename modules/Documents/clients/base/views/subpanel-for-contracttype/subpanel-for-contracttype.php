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
$viewdefs['Documents']['base']['view']['subpanel-for-contracttype'] = [
    'type' => 'subpanel-list',
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'document_name',
                    'label' => 'LBL_LIST_DOCUMENT_NAME',
                    'enabled' => true,
                    'default' => true,
                    'link' => true,
                ],
                [
                    'name' => 'is_template',
                    'label' => 'LBL_LIST_IS_TEMPLATE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'template_type',
                    'label' => 'LBL_LIST_TEMPLATE_TYPE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'latest_revision',
                    'label' => 'LBL_LATEST_REVISION',
                    'enabled' => true,
                    'default' => true,
                ],
            ],
        ],
    ],
];
