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

$viewdefs['Documents']['base']['layout']['tabbed-layout'] = [
    'components' => [
        [
            'view' => 'activitystream',
            'label' => 'Activity Stream',
        ],
        [
            'layout' => 'list-cluster',
            'label' => 'Document Revisions',
            'context' => [
                'link' => 'revisions',
            ],
        ],
        [
            'layout' => 'list-cluster',
            'label' => 'Contracts',
            'context' => [
                'link' => 'contracts',
            ],
        ],
        [
            'layout' => 'list-cluster',
            'label' => 'Accounts',
            'context' => [
                'link' => 'accounts',
            ],
        ],
        [
            'layout' => 'list-cluster',
            'label' => 'Contacts',
            'context' => [
                'link' => 'contacts',
            ],
        ],
        [
            'layout' => 'list-cluster',
            'label' => 'Opportunities',
            'context' => [
                'link' => 'opportunities',
            ],
        ],
        [
            'layout' => 'list-cluster',
            'label' => 'Cases',
            'context' => [
                'link' => 'cases',
            ],
        ],
        [
            'layout' => 'list-cluster',
            'label' => 'Bugs',
            'context' => [
                'link' => 'bugs',
            ],
        ],
        [
            'layout' => 'list-cluster',
            'label' => 'Quotes',
            'context' => [
                'link' => 'quotes',
            ],
        ],
        [
            'layout' => 'list-cluster',
            'label' => 'Products',
            'context' => [
                'link' => 'products',
            ],
        ],
    ],
];
