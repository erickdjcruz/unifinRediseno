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

$searchdefs = [
    'ext_rest_zoominfocompany' => [
        'Leads' => [
            'companyname',
            'state',
            'countrycode',
        ],
        'Accounts' => [
            'companyname',
            'state',
            'countrycode',
        ],
        'Contacts' => [
            'companyname',
            'state',
            'countrycode',
        ],
    ],

    'ext_rest_zoominfoperson' => [
        'Leads' => [
            'firstname',
            'lastname',
            'email',
            'companyname',
        ],
        'Accounts' => [
            'email',
            'companyname',
        ],
        'Contacts' => [
            'firstname',
            'lastname',
            'email',
            'companyname',
        ],
    ],
];
