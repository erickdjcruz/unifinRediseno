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
$viewdefs['KBContents']['base']['filter']['default'] = [
    'default_filter' => 'all_records',
    'fields' => [
        'name' => [],
        'status' => [],
        'language' => [
            'type' => 'enum-config',
            'key' => 'languages',
        ],
        'category_name' => [],
        'revision' => [],
        '$owner' => [
            'predefined_filter' => true,
            'vname' => 'LBL_CURRENT_USER_FILTER',
        ],
        'assigned_user_name' => [],
        'tag' => [],
        '$favorite' => [
            'predefined_filter' => true,
            'vname' => 'LBL_FAVORITES_FILTER',
        ],
        'active_date' => [],
        'exp_date' => [],
        'is_external' => [],
        'kbsapprover_name' => [],
    ],
];
