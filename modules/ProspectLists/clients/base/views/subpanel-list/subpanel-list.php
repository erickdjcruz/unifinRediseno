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
$viewdefs['ProspectLists']['base']['view']['subpanel-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'label' => 'LBL_LIST_PROSPECT_LIST_NAME',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'name',
                    'link' => true,
                ],
                [
                    'label' => 'LBL_LIST_DESCRIPTION',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'description',
                ],
                [
                    'label' => 'LBL_LIST_TYPE_NO',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'list_type',
                ],
                [
                    'label' => 'LBL_LIST_ENTRIES',
                    'enabled' => true,
                    'default' => true,
                    'name' => 'entry_count',
                ],
            ],
        ],
    ],
];
