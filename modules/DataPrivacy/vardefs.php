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

$dictionary['DataPrivacy'] = [
    'table' => 'data_privacy',
    'audited' => true,
    'activity_enabled' => false,
    'color' => 'teal',
    'icon' => 'sicon-data-privacy-lg',
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => false,
    'comment' => 'Requests regarding the data we have collected on our customers',
    'fields' => [
        'type' => [
            'name' => 'type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'options' => 'dataprivacy_type_dom',
            'len' => 255,
            'unified_search' => true,
            'comment' => 'The type of request',
            'sortable' => true,
            'duplicate_on_record_copy' => 'always',
            'required' => true,
            'audited' => true,
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'dataprivacy_status_dom',
            'len' => 100,
            'default' => 'Open',
            'comment' => 'The status of the request',
            'sortable' => true,
            'duplicate_on_record_copy' => 'always',
            'audited' => true,
            'massupdate' => false,
        ],
        'business_purpose' => [
            'name' => 'business_purpose',
            'vname' => 'LBL_BUSINESS_PURPOSE',
            'type' => 'multienum',
            'options' => 'dataprivacy_business_purpose_dom',
            'default' => '',
            'len' => 255,
            'comment' => 'Business purpose',
            'sortable' => true,
            'duplicate_on_record_copy' => 'always',
            'required' => false,
            'isMultiSelect' => true,
            'visibility_grid' => [
                'trigger' => 'type',
                'values' => [
                    '' => [],
                    'Request for Data Privacy Policy' => [],
                    'Send Personal Information being processed' => [],
                    'Rectify Information' => [],
                    'Request to Erase Information' => [],
                    'Export Information' => [],
                    'Restrict Processing' => [],
                    'Object to Processing' => [],
                    'Consent to Process' => [
                        'Business Communications',
                        'Marketing Communications by company',
                        'Marketing Communications by partners',
                    ],
                    'Withdraw Consent' => [
                        'Business Communications',
                        'Marketing Communications by company',
                        'Marketing Communications by partners',
                    ],
                    'Other' => [],
                ],
            ],
            'audited' => true,
        ],
        'source' => [
            'name' => 'source',
            'vname' => 'LBL_SOURCE',
            'type' => 'varchar',
            'len' => 255,
            'required' => false,
            'full_text_search' => ['enabled' => true, 'searchable' => true],
            'comment' => 'The source of the request',
            'audited' => true,
        ],
        'requested_by' => [
            'name' => 'requested_by',
            'vname' => 'LBL_REQUESTED_BY',
            'type' => 'varchar',
            'len' => 255,
            'full_text_search' => [
                'enabled' => true,
                'searchable' => true,
            ],
            'comment' => 'Requested by',
            'audited' => true,
        ],
        'date_opened' => [
            'name' => 'date_opened',
            'vname' => 'LBL_DATE_OPENED',
            'type' => 'date',
            'display_default' => 'now',
            'options' => 'date_range_search_dom',
            'enable_range_search' => true,
            'comment' => 'Date opened',
        ],
        'date_due' => [
            'name' => 'date_due',
            'vname' => 'LBL_DATE_DUE',
            'type' => 'date',
            'options' => 'date_range_search_dom',
            'enable_range_search' => true,
            'comment' => 'Due date',
            'audited' => true,
        ],
        'resolution' => [
            'name' => 'resolution',
            'vname' => 'LBL_RESOLUTION',
            'type' => 'text',
            'full_text_search' => ['enabled' => true, 'searchable' => true],
            'comment' => 'The resolution of the request',
            'audited' => true,
        ],
        'date_closed' => [
            'name' => 'date_closed',
            'vname' => 'LBL_DATE_CLOSED',
            'type' => 'date',
            'options' => 'date_range_search_dom',
            'enable_range_search' => true,
            'comment' => 'Date closed',
            'audited' => true,
        ],
        'fields_to_erase' => [
            'name' => 'fields_to_erase',
            'type' => 'json',
            'dbType' => 'text',
            'studio' => false,
            'hideacl' => true,
            'reportable' => false,
        ],
        'leads' => [
            'name' => 'leads',
            'type' => 'link',
            'relationship' => 'leads_dataprivacy',
            'source' => 'non-db',
            'vname' => 'LBL_LEADS',
        ],
        'accounts' => [
            'name' => 'accounts',
            'type' => 'link',
            'relationship' => 'accounts_dataprivacy',
            'source' => 'non-db',
            'vname' => 'LBL_ACCOUNTS',
        ],
        'contacts' => [
            'name' => 'contacts',
            'type' => 'link',
            'relationship' => 'contacts_dataprivacy',
            'source' => 'non-db',
            'vname' => 'LBL_CONTACTS',
        ],
        'prospects' => [
            'name' => 'prospects',
            'type' => 'link',
            'relationship' => 'prospects_dataprivacy',
            'source' => 'non-db',
            'vname' => 'LBL_PROSPECTS',
        ],
    ],
    'indices' => [
    ],
    'acls' => ['SugarACLDataPrivacy' => true, 'SugarACLStatic' => true],
];

VardefManager::createVardef('DataPrivacy', 'DataPrivacy', ['default', 'assignable', 'team_security', 'issue', 'audit']);
