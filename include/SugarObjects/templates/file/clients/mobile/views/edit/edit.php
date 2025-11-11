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
$module_name = '<module_name>';
$viewdefs[$module_name]['mobile']['view']['edit'] = [
    'templateMeta' => ['maxColumns' => '1',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],


    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => 'document_name',
                    'label' => 'LBL_DOC_NAME',
                ],
                [
                    'name' => 'uploadfile',
                    'displayParams' => ['link' => 'uploadfile', 'id' => 'id'],
                ],
                'active_date',
                'exp_date',
                'tag',
                'assigned_user_name',
                'team_name',
            ],
        ],
    ],
];
