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
$searchFields['Contacts'] =
    [
        'first_name' => ['query_type' => 'default'],
        'last_name' => ['query_type' => 'default'],
        'search_name' => ['query_type' => 'default', 'db_field' => ['first_name', 'last_name'], 'force_unifiedsearch' => true],
        'account_name' => ['query_type' => 'default', 'db_field' => ['accounts.name']],
        'lead_source' => ['query_type' => 'default', 'operator' => '=', 'options' => 'lead_source_dom', 'template_var' => 'LEAD_SOURCE_OPTIONS'],
        'do_not_call' => ['query_type' => 'default', 'input_type' => 'checkbox', 'operator' => '='],
        'phone' => ['query_type' => 'default', 'db_field' => ['phone_mobile', 'phone_work', 'phone_other', 'phone_fax', 'assistant_phone']],
        'email' => [
            'query_type' => 'default',
            'operator' => 'subquery',
            'subquery' => 'SELECT eabr.bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (ea.id = eabr.email_address_id) WHERE eabr.deleted=0 AND ea.email_address LIKE',
            'db_field' => [
                'id',
            ],
        ],
        'assistant' => ['query_type' => 'default'],
        'address_street' => ['query_type' => 'default', 'db_field' => ['primary_address_street', 'alt_address_street']],
        'address_city' => ['query_type' => 'default', 'db_field' => ['primary_address_city', 'alt_address_city']],
        'address_state' => ['query_type' => 'default', 'db_field' => ['primary_address_state', 'alt_address_state']],
        'address_postalcode' => ['query_type' => 'default', 'db_field' => ['primary_address_postalcode', 'alt_address_postalcode']],
        'address_country' => ['query_type' => 'default', 'db_field' => ['primary_address_country', 'alt_address_country']],
        'current_user_only' => ['query_type' => 'default', 'db_field' => ['assigned_user_id'], 'my_items' => true, 'vname' => 'LBL_CURRENT_USER_FILTER', 'type' => 'bool'],
        'assigned_user_id' => ['query_type' => 'default'],
        'account_id' => ['query_type' => 'default', 'db_field' => ['accounts.id']],
        'campaign_name' => ['query_type' => 'default'],
        'favorites_only' => [
            'query_type' => 'format',
            'operator' => 'subquery',
            'subquery' => 'SELECT sugarfavorites.record_id FROM sugarfavorites 
			                    WHERE sugarfavorites.deleted=0 
			                        and sugarfavorites.module = \'Contacts\' 
			                        and sugarfavorites.assigned_user_id = \'{0}\'',
            'db_field' => ['id']],
        'sync_contact' => [
            'query_type' => 'format',
            'operator' => 'subquery',
            'subquery_in_clause' => ['0' => 'NOT IN', '1' => 'IN'],
            'subquery' => 'SELECT contacts_users.contact_id FROM contacts_users 
			                    WHERE contacts_users.deleted=0 
			                        and contacts_users.user_id = \'{1}\'',
            'db_field' => ['id']],
        //Range Search Support
        'range_date_entered' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'start_range_date_entered' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'end_range_date_entered' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'range_date_modified' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'start_range_date_modified' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        'end_range_date_modified' => ['query_type' => 'default', 'enable_range_search' => true, 'is_date_field' => true],
        //Range Search Support
    ];
