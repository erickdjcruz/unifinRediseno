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
$viewdefs['base']['view']['audit'] = [
    'template' => 'audit',
    'panels' => [
        [
            'fields' => [
                [
                    'type' => 'fieldtype',
                    'name' => 'field_name',
                    'label' => 'LBL_FIELD_NAME',
                    'sortable' => true,
                    'filter' => 'startsWith',
                ],
                [
                    'type' => 'base',
                    'name' => 'before',
                    'label' => 'LBL_OLD_NAME',
                    'link' => false,
                    'sortable' => false,
                    'filter' => 'contains',
                ],
                [
                    'type' => 'base',
                    'name' => 'after',
                    'label' => 'LBL_NEW_VALUE',
                    'link' => false,
                    'sortable' => false,
                    'filter' => 'contains',
                ],
                [
                    'type' => 'base',
                    'name' => 'created_by_username',
                    'label' => 'LBL_CREATED_BY',
                    'sortable' => true,
                ],
                [
                    'type' => 'source',
                    'name' => 'source',
                    'label' => 'LBL_SOURCE_FIELD',
                    'sortable' => false,
                ],
                [
                    'type' => 'datetimecombo',
                    'name' => 'date_created',
                    'label' => 'LBL_LIST_DATE',
                    'options' => 'date_range_search_dom',
                    'sortable' => true,
                ],
            ],
        ],
    ],
];
