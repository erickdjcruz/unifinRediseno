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
$searchFields['Employees'] =
    [
        'first_name' => ['query_type' => 'default'],
        'last_name' => ['query_type' => 'default'],
        'search_name' => ['query_type' => 'default', 'db_field' => ['first_name', 'last_name'], 'force_unifiedsearch' => true],
        'email' => [
            'query_type' => 'default',
            'operator' => 'subquery',
            'subquery' => 'SELECT eabr.bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (ea.id = eabr.email_address_id) WHERE eabr.deleted=0 and ea.email_address LIKE',
            'db_field' => [
                'id',
            ],
        ],
        'phone' => [
            'query_type' => 'default',
            'operator' => 'subquery',
            'subquery' => ['SELECT id FROM users where phone_home LIKE ',
                'SELECT id FROM users where phone_fax LIKE',
                'SELECT id FROM users where phone_other LIKE',
                'SELECT id FROM users where phone_work LIKE',
                'SELECT id FROM users where phone_mobile LIKE',
                'OR' => true,
            ],
            'db_field' => [
                'id',
            ],
        ],
        // This is named so awkwardly because it's the only way we could get it to be a "proper" checkbox and not throw the basic search all out of wack.
        'open_only_active_users' => ['query_type' => 'default', 'db_field' => ['employee_status'], 'vname' => 'LBL_ONLY_ACTIVE', 'type' => 'bool'],


        'employee_status' => ['query_type' => 'default', 'options' => 'employee_status_dom', 'template_var' => 'STATUS_OPTIONS', 'options_add_blank' => true],
    ];
