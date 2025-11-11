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

$dictionary['Subscription'] = [
    'table' => 'subscriptions',
    'fields' => [
        // Set unnecessary fields from Basic to non-required/non-db.
        'name' => [
            'name' => 'name',
            'type' => 'varchar',
            'required' => false,
            'source' => 'non-db',
        ],

        'description' => [
            'name' => 'description',
            'type' => 'varchar',
            'required' => false,
            'source' => 'non-db',
        ],

        // Add table columns.
        'parent_type' => [
            'name' => 'parent_type',
            'type' => 'varchar',
            'len' => 100,
            'required' => true,
        ],

        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'id',
            'required' => true,
        ],
    ],
    'indices' => [
        [
            'name' => 'subscription_parent',
            'type' => 'index',
            'fields' => ['parent_id'],
        ],
    ],
    // @TODO Fix the Default and Basic SugarObject templates so that Basic
    // implements Default. This would allow the application of various
    // implementations on Basic without forcing Default to have those so that
    // situations like this - implementing taggable - doesn't have to apply to
    // EVERYTHING. Since there is no distinction between basic and default for
    // sugar objects templates yet, we need to forecefully remove the taggable
    // implementation fields. Once there is a separation of default and basic
    // templates we can safely remove these as this module will implement
    // default instead of basic.
    'ignore_templates' => [
        'taggable',
        'commentlog',
    ],
];

VardefManager::createVardef('ActivityStream/Subscriptions', 'Subscription', ['basic']);
