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

$viewdefs = [
    'Opportunities' => [
        'QuickCreate' => [
            'templateMeta' => [
                'maxColumns' => '2',
                'widths' => [
                    0 =>
                        [
                            'label' => '10',
                            'field' => '30',
                        ],
                    1 =>
                        [
                            'label' => '10',
                            'field' => '30',
                        ],
                ],
                'javascript' => '{$PROBABILITY_SCRIPT}',
            ],
            'panels' => [
                'DEFAULT' => [
                    [
                        [
                            'name' => 'name',
                            'displayParams' => ['required' => true],
                        ],
                        [
                            'name' => 'account_name',
                        ],
                    ],
                    [
                        [
                            'name' => 'currency_id',
                        ],
                        [
                            'name' => 'opportunity_type',
                        ],
                    ],
                    [
                        'amount',
                        'date_closed',
                    ],
                    [
                        'next_step',
                        'sales_stage',
                    ],
                    [
                        'lead_source',
                        'probability',
                    ],
                    [
                        [
                            'name' => 'assigned_user_name',
                        ],
                        [
                            'name' => 'team_name',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
