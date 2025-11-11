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
/*********************************************************************************
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$viewdefs['Cases']['mobile']['view']['edit'] = [
    'templateMeta' => [
        'maxColumns' => '1',
        'widths' => [
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => 'case_number',
                    'displayParams' => [
                        'required' => false,
                        'wireless_detail_only' => true,
                    ],
                ],
                ['name' => 'name',
                    'displayParams' => [
                        'required' => true,
                        'wireless_edit_only' => true,
                    ],
                ],
                'account_name',
                'priority',
                'status',
                'description',
                'resolution',
                'tag',
                'assigned_user_name',

                'team_name',
            ],
        ],
    ],
];
