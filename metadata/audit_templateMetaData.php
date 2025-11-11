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
/* this table should never get created, it should only be used as a template for the acutal audit tables
 * for each moudule.
 */
$dictionary['audit'] =
    ['table' => 'audit',
        'fields' => [
            'id' => ['name' => 'id', 'type' => 'id', 'len' => '36', 'required' => true],
            'parent_id' => ['name' => 'parent_id', 'type' => 'id', 'len' => '36', 'required' => true],
            'event_id' => ['name' => 'event_id', 'type' => 'id', 'required' => true],
            'date_created' => ['name' => 'date_created', 'type' => 'datetime'],
            'created_by' => ['name' => 'created_by', 'type' => 'id', 'len' => 36],
            'date_updated' => ['name' => 'date_updated', 'type' => 'datetime'],
            'field_name' => ['name' => 'field_name', 'type' => 'varchar', 'len' => 100],
            'data_type' => ['name' => 'data_type', 'type' => 'varchar', 'len' => 100],
            'before_value_string' => ['name' => 'before_value_string', 'type' => 'varchar'],
            'after_value_string' => ['name' => 'after_value_string', 'type' => 'varchar'],
            'before_value_text' => ['name' => 'before_value_text', 'type' => 'text'],
            'after_value_text' => ['name' => 'after_value_text', 'type' => 'text'],
        ],
        'indices' => [
            //name will be re-constructed adding idx_ and table name as the prefix like 'idx_accounts_'
            ['name' => 'pk', 'type' => 'primary', 'fields' => ['id']],
            ['name' => 'event_id', 'type' => 'index', 'fields' => ['event_id']],
            ['name' => 'pa_ev_id', 'type' => 'index', 'fields' => ['parent_id', 'event_id']],
            ['name' => 'after_value', 'type' => 'index', 'fields' => ['after_value_string']],
        ],
    ];
