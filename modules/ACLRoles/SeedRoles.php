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

function getMLARoles()
{
    return [
        'Sales Administrator' => [
            'Accounts' => ['admin' => 100, 'access' => 89],
            'Contacts' => ['admin' => 100, 'access' => 89],
            'Forecasts' => ['admin' => 100, 'access' => 89],
            'Leads' => ['admin' => 100, 'access' => 89],
            'Quotes' => ['admin' => 100, 'access' => 89],
            'Opportunities' => ['admin' => 100, 'access' => 89],
        ],
        'Marketing Administrator' => [
            'Accounts' => ['admin' => 100, 'access' => 89],
            'Contacts' => ['admin' => 100, 'access' => 89],
            'Campaigns' => ['admin' => 100, 'access' => 89],
            'ProspectLists' => ['admin' => 100, 'access' => 89],
            'Leads' => ['admin' => 100, 'access' => 89],
            'Prospects' => ['admin' => 100, 'access' => 89],
        ],
        'Customer Support Administrator' => [
            'Accounts' => ['admin' => 100, 'access' => 89],
            'Contacts' => ['admin' => 100, 'access' => 89],
            'Bugs' => ['admin' => 100, 'access' => 89],
            'Cases' => ['admin' => 100, 'access' => 89],
            'KBContents' => ['admin' => 100, 'access' => 89],
        ],
        'Data Privacy Manager' => [
            'DataPrivacy' => ['admin' => 99, 'access' => 89],
            'Accounts' => ['admin' => 99, 'access' => 89],
            'Contacts' => ['admin' => 99, 'access' => 89],
            'Leads' => ['admin' => 99, 'access' => 89],
            'Prospects' => ['admin' => 99, 'access' => 89],
        ],
    ];
}

function create_default_roles()
{
    // Adding MLA Roles
    global $db;
    addDefaultRoles(getMLARoles());
}
