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

$dictionary['Comment'] = [
    'table' => 'comments',
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

        // Add relationship fields.
        'activities' => [
            'name' => 'activities',
            'type' => 'link',
            'relationship' => 'comments',
            'link_type' => 'one',
            'module' => 'Activities',
            'bean_name' => 'Activity',
            'source' => 'non-db',
        ],

        // Add table columns.
        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'id',
            'required' => true,
        ],

        'data' => [
            'name' => 'data',
            'type' => 'json',
            'dbType' => 'longtext',
            'required' => true,
        ],
    ],

    'indices' => [
        [
            'name' => 'comment_activities',
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

VardefManager::createVardef('ActivityStream/Comments', 'Comment', ['basic']);
