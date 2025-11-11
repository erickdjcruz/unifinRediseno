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

$viewdefs['Cases']['portal']['filter']['default'] = [
    'default_filter' => 'all_records',
    'quicksearch_field' => ['name', 'case_number',],
    'quicksearch_priority' => 2,
    'fields' => [
        'name' => [],
        'status' => [],
        'priority' => [],
        'case_number' => [],
        'date_entered' => [],
        'date_modified' => [],
    ],
];
