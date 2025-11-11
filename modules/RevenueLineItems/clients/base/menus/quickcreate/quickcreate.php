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

$module_name = 'RevenueLineItems';
$viewdefs[$module_name]['base']['menu']['quickcreate'] = [
    'layout' => 'create',
    'visible' => true,
    'order' => 11,
    'related' => [
        [
            'module' => 'Accounts',
            'link' => 'revenuelineitems',
        ],
        [
            'module' => 'Contacts',
            'link' => 'revenuelineitems',
        ],
        [
            'module' => 'Opportunities',
            'link' => 'revenuelineitems',
        ],
    ],
];
