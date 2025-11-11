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
$viewdefs['DRI_SubWorkflows']['base']['view']['subpanel-list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'label',
                    'label' => 'LBL_LABEL',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                ],
                [
                    'name' => 'sort_order',
                    'label' => 'LBL_SORT_ORDER',
                    'default' => true,
                    'enabled' => true,
                    'type' => 'int',
                ],
                [
                    'name' => 'state',
                    'label' => 'LBL_STATE',
                    'default' => true,
                    'enabled' => true,
                    'type' => 'enum',
                ],
                [
                    'name' => 'progress',
                    'label' => 'LBL_PROGRESS',
                    'default' => true,
                    'enabled' => true,
                    'type' => 'cj-progress-bar',
                ],
                [
                    'name' => 'momentum_ratio',
                    'label' => 'LBL_MOMENTUM_RATIO',
                    'default' => true,
                    'enabled' => true,
                    'type' => 'cj-momentum-bar',
                ],
                [
                    'label' => 'LBL_DATE_ENTERED',
                    'default' => true,
                    'enabled' => true,
                    'name' => 'date_entered',
                    'type' => 'datetime',
                ],
                [
                    'label' => 'LBL_DATE_MODIFIED',
                    'default' => true,
                    'enabled' => true,
                    'name' => 'date_modified',
                    'type' => 'datetime',
                ],
            ],
        ],
    ],
    'orderBy' => [
        'field' => 'sort_order',
        'direction' => 'desc',
    ],
    'type' => 'subpanel-list',
];
