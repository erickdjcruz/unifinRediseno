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

$vardefs = [
    'fields' => [],
    'relationships' => [
        strtolower($module) . '_audit' => [
            'lhs_module' => $module,
            'lhs_table' => $table_name,
            'lhs_key' => 'id',
            'rhs_module' => 'Audit',
            'rhs_table' => $table_name . '_audit',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
        ],
    ],
    'indices' => [],
];
