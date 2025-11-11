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

$viewdefs['Cases']['AccountsQuickCreate'] = [
    'templateMeta' => ['form' => [
        'hidden' => [
            0 => '<input type="hidden" name="account_id" value="{$smarty.request.account_id}">',
            1 => '<input type="hidden" name="account_name" value="{$smarty.request.account_name}">',
        ],
    ],
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        [
            ['name' => 'name', 'displayParams' => ['size' => 65, 'required' => true]],
            'priority',
        ],
        [
            'status',
            ['name' => 'account_name', 'type' => 'readonly'],
        ],
        [
            [
                'name' => 'description',
                'displayParams' => ['rows' => '4', 'cols' => '60'],
                'nl2br' => true,
            ],
        ],
    ],
];
