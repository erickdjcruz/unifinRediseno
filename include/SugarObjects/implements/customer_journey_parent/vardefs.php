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
    'fields' => [
        'dri_workflows' => [
            'name' => 'dri_workflows',
            'vname' => 'LBL_DRI_WORKFLOWS',
            'source' => 'non-db',
            'type' => 'link',
            'side' => 'left',
            'bean_name' => 'DRI_Workflow',
            'relationship' => 'dri_workflow_' . strtolower($module),
            'module' => 'DRI_Workflows',
        ],
        'dri_workflow_template_id' => [
            'name' => 'dri_workflow_template_id',
            'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
            'required' => false,
            'reportable' => false,
            'audited' => true,
            'importable' => 'true',
            'massupdate' => true,
            'type' => 'enum',
            'placeholder' => 'LBL_SELECT_SMART_GUIDE_TEMPLATE_PLACEHOLDER',
            'options' => null,
            'dbType' => 'id',
            'processes' => true,
            'studio' => false,
            'function' => [
                'name' => 'listSmartGuideTemplatesByModule',
                'params' => [
                    $module,
                ],
            ],
        ],
        'dri_workflow_template_name' => [
            'name' => 'dri_workflow_template_name',
            'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
            'required' => false,
            'reportable' => false,
            'audited' => true,
            'importable' => 'true',
            'massupdate' => false,
            'studio' => false,
            'source' => 'non-db',
            'type' => 'relate',
            'rname' => 'name',
            'table' => 'dri_workflow_templates',
            'id_name' => 'dri_workflow_template_id',
            'sort_on' => 'name',
            'module' => 'DRI_Workflow_Templates',
            'link' => 'dri_workflow_template_link',
        ],
        'dri_workflow_template_link' => [
            'name' => 'dri_workflow_template_link',
            'vname' => 'LBL_DRI_WORKFLOW_TEMPLATE',
            'reportable' => false,
            'source' => 'non-db',
            'type' => 'link',
            'side' => 'right',
            'bean_name' => 'DRI_Workflow_Template',
            'relationship' => strtolower($object_name) . '_dri_workflow_templates',
            'module' => 'DRI_Workflow_Templates',
        ],
        'perform_sugar_action' => [
            'name' => 'perform_sugar_action',
            'vname' => 'LBL_PERFORM_SUGAR_ACTION',
            'type' => 'bool',
            'studio' => false,
            'massupdate' => false,
            'reportable' => false,
            'default' => 0,
        ],
    ],
    'relationships' => [
        strtolower($object_name) . '_dri_workflow_templates' => [
            'relationship_type' => 'one-to-many',
            'lhs_key' => 'id',
            'lhs_module' => 'DRI_Workflow_Templates',
            'lhs_table' => 'dri_workflow_templates',
            'rhs_module' => $module,
            'rhs_table' => strtolower($module),
            'rhs_key' => 'dri_workflow_template_id',
        ],
    ],
    'indices' => [
        'idx_' . trim(substr(strtolower($table_name), 0, 17)) . '_cjtpl_id' => [
            'name' => 'idx_' . trim(substr(strtolower($table_name), 0, 17)) . '_cjtpl_id',
            'type' => 'index',
            'fields' => [
                'dri_workflow_template_id',
            ],
        ],
    ],
];
