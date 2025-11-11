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

$viewdefs['pmse_Inbox']['base']['view']['process-users-chart'] = [
    'dashlets' => [
        [
            'label' => 'LBL_PMSE_PROCESS_USERS_CHART_NAME',
            'description' => 'LBL_PMSE_PROCESS_USERS_CHART_DESCRIPTION',
            'filter' => [
                'module' => [
                    'Home',
                    'pmse_Project',
                ],
                'view' => [
                    'records',
                ],
            ],
            'config' => [
                'isRecord' => '0',
            ],
            'preview' => [
                'isRecord' => '0',
            ],
        ],
        [
            'label' => 'LBL_PMSE_PROCESS_USERS_CHART_NAME_RECORD',
            'description' => 'LBL_PMSE_PROCESS_USERS_CHART_DESCRIPTION',
            'filter' => [
                'module' => [
                    'pmse_Project',
                ],
                'view' => [
                    'record',
                ],
            ],
            'config' => [
                'isRecord' => '1',
            ],
            'preview' => [
                'isRecord' => '1',
            ],
        ],
    ],
    'processes_selector' => [
        [
            'name' => 'processes_selector',
            'label' => 'Process Selector',
            'type' => 'enum',
            'options' => [],
        ],
    ],
    'config' => [
        'fields' => [
            [
                'name' => 'isRecord',
                'label' => 'isRecord',
                'desc' => 'LBL_DNB_PRIM_NAME_DESC',
                'type' => 'text',
            ],
        ],
    ],
];
