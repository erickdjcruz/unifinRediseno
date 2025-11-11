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

$dictionary['Dashboard'] = [
    'table' => 'dashboards',
    'template_restricted_actions' => ['delete'],
    'template_editable_fields' => ['default_dashboard', 'assigned_user_name', 'team_name'],
    'fields' => [
        'dashboard_module' => [
            'required' => false,
            'name' => 'dashboard_module',
            'vname' => 'LBL_DASHBOARD_MODULE',
            'type' => 'enum',
            'dbType' => 'varchar',
            'len' => 100,
            'massupdate' => 0,
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => true,
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'merge_filter' => 'disabled',
            'calculated' => false,
            'options' => 'moduleList',
        ],
        'view_name' => [
            'required' => false,
            'name' => 'view_name',
            'vname' => 'LBL_VIEW',
            'type' => 'enum',
            'dbType' => 'varchar',
            'len' => 100,
            'massupdate' => 0,
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => true,
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'merge_filter' => 'disabled',
            'calculated' => false,
            'options' => 'dashboard_view_name_list',
        ],
        'metadata' => [
            'required' => false,
            'name' => 'metadata',
            'vname' => 'LBL_METADATA',
            'type' => 'json',
            'dbType' => 'longtext',
            'massupdate' => 0,
            'no_default' => false,
            'comments' => '',
            'help' => '',
            'importable' => true,
            'duplicate_merge' => 'disabled',
            'duplicate_merge_dom_value' => '0',
            'audited' => false,
            'reportable' => true,
            'unified_search' => false,
            'merge_filter' => 'disabled',
            'calculated' => false,
        ],
        'default_dashboard' => [
            'name' => 'default_dashboard',
            'vname' => 'LBL_DEFAULT_DASHBOARD',
            'type' => 'bool',
            'default' => '0',
            'reportable' => false,
            'duplicate_on_record_copy' => 'no',
            'merge_filter' => 'disabled',
            'comments' => '',
            'massupdate' => 0,
        ],
        'is_template' => [
            'name' => 'is_template',
            'vname' => 'LBL_TEMPLATE',
            'type' => 'bool',
            'default' => false,
            'readonly' => true,
            'reportable' => true,
            'importable' => true,
            'full_text_search' => [
                'enabled' => true,
                'searchable' => true,
            ],
        ],
    ],
    'indices' => [
        [
            'name' => 'user_module_view',
            'type' => 'index',
            'fields' => ['assigned_user_id', 'dashboard_module', 'view_name'],
        ],
    ],
    'relationships' => [],
    'uses' => [
        'team_security',
    ],
    'acls' => [
        'SugarACLOwnerWrite' => true,
        'SugarACLAdminOnlyFields' => [
            'non_writable_fields' => [
                'default_dashboard',
            ],
        ],
    ],
    // FIXME TY-1675 Fix the Default and Basic SugarObject templates so that
    // Basic implements Default. This would allow the application of various
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
    'portal_visibility' => [
        'class' => 'Dashboards',
    ],
];

if (!class_exists('VardefManager')) {
}
VardefManager::createVardef('Dashboards', 'Dashboard', ['basic', 'assignable']);
