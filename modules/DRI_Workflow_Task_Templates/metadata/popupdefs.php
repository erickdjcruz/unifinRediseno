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

$popupMeta = [
    'moduleMain' => 'DRI_Workflow_Task_Template',
    'varName' => 'DRI_Workflow_Task_Template',
    'orderBy' => 'dri_workflow_task_templates.name',
    'whereClauses' => [
        'name' => 'dri_workflow_task_templates.name',
        'dri_subworkflow_template_name' => 'dri_workflow_task_templates.dri_subworkflow_template_name',
        'sort_order' => 'dri_workflow_task_templates.sort_order',
    ],
    'searchInputs' => [
        1 => 'name',
        4 => 'dri_subworkflow_template_name',
        5 => 'sort_order',
    ],
    'searchdefs' => [
        'name' => [
            'type' => 'name',
            'label' => 'LBL_NAME',
            'width' => '10%',
            'name' => 'name',
        ],
        'dri_subworkflow_template_name' => [
            'type' => 'relate',
            'link' => true,
            'label' => 'LBL_DRI_SUBWORKFLOW_TEMPLATE',
            'id' => 'DRI_SUBWORKFLOW_TEMPLATE_ID',
            'width' => '10%',
            'name' => 'dri_subworkflow_template_name',
        ],
        'sort_order' => [
            'type' => 'int',
            'label' => 'LBL_SORT_ORDER',
            'width' => '10%',
            'name' => 'sort_order',
        ],
    ],
    'listviewdefs' => [
        'NAME' => [
            'type' => 'name',
            'label' => 'LBL_NAME',
            'width' => '10%',
            'default' => true,
            'name' => 'name',
        ],
        'DRI_SUBWORKFLOW_TEMPLATE_NAME' => [
            'type' => 'relate',
            'link' => true,
            'label' => 'LBL_DRI_SUBWORKFLOW_TEMPLATE',
            'id' => 'DRI_SUBWORKFLOW_TEMPLATE_ID',
            'width' => '10%',
            'default' => true,
            'name' => 'dri_subworkflow_template_name',
        ],
        'DRI_WORKFLOW_TEMPLATE_NAME' => [
            'type' => 'relate',
            'readonly' => true,
            'link' => true,
            'label' => 'LBL_DRI_WORKFLOW_TEMPLATE',
            'id' => 'DRI_WORKFLOW_TEMPLATE_ID',
            'width' => '10%',
            'default' => true,
        ],
        'SORT_ORDER' => [
            'type' => 'int',
            'default' => true,
            'label' => 'LBL_SORT_ORDER',
            'width' => '10%',
            'name' => 'sort_order',
        ],
        'ACTIVITY_TYPE' => [
            'type' => 'enum',
            'default' => true,
            'label' => 'LBL_ACTIVITY_TYPE',
            'width' => '10%',
            'name' => 'activity_type',
        ],
        'TYPE' => [
            'type' => 'enum',
            'default' => true,
            'label' => 'LBL_TYPE',
            'width' => '10%',
            'name' => 'type',
        ],
        'DATE_MODIFIED' => [
            'type' => 'datetime',
            'studio' => [
                'portaleditview' => false,
            ],
            'readonly' => true,
            'label' => 'LBL_DATE_MODIFIED',
            'width' => '10%',
            'default' => true,
            'name' => 'date_modified',
        ],
    ],
];
