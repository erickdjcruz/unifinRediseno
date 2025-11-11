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

$dictionary['business_centers_holidays'] = [
    'table' => 'business_centers_holidays',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'business_center_id' => [
            'name' => 'business_center_id',
            'type' => 'id',
        ],
        'holiday_id' => [
            'name' => 'holiday_id',
            'type' => 'id',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
        ],
        'deleted' => [
            'name' => 'deleted',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
            'required' => false,
        ],
    ],
    'indices' => [
        [
            'name' => 'business_centers_holidayspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
        [
            'name' => 'idx_business_center_holiday',
            'type' => 'alternate_key',
            'fields' => [
                'business_center_id',
                'holiday_id',
            ],
        ],
        [
            'name' => 'idx_bcid_del_holid',
            'type' => 'index',
            'fields' => [
                'business_center_id',
                'deleted',
                'holiday_id',
            ],
        ],
    ],
    'relationships' => [
        'business_centers_holidays' => [
            'lhs_module' => 'BusinessCenters',
            'lhs_table' => 'business_centers',
            'lhs_key' => 'id',
            'rhs_module' => 'Holidays',
            'rhs_table' => 'holidays',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'business_centers_holidays',
            'join_key_lhs' => 'business_center_id',
            'join_key_rhs' => 'holiday_id',
        ],
    ],
];
