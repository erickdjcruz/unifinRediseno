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

$viewdefs['RevenueLineItems']['base']['layout']['convert-main-for-leads'] =
    [
        'module' => 'RevenueLineItems',
        'required' => false,
        'copyData' => true,
        'duplicateCheckOnStart' => false,
        'duplicateCheckRequiredFields' => [
            'opportunity_id',
        ],
        'dependentModules' => [
            'Opportunities' => [
                'fieldMapping' => [
                    'opportunity_id' => 'id',
                    'account_id' => 'account_id',
                ],
            ],

        ],
        'hiddenFields' => [
            'account_name' => 'Opportunities',
            'opportunity_name' => 'Opportunities',
        ],
    ];
