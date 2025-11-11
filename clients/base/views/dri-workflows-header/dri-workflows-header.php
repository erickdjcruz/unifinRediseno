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

$viewdefs['base']['view']['dri-workflows-header'] = [
    'left_fields' => [
        [
            'name' => 'filter',
            'vname' => 'LBL_DRI_WORKFLOWS_FILTER',
            'type' => 'cj-dri-workflow-filter',
            'css_class' => 'dropDownToggleButtonBg',
            'switch_on_click' => false,
            'no_default_action' => true,
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-check',
                    'name' => 'active_smart_guides',
                    'event' => 'parent:active_smart_guides:click',
                    'acl_action' => 'edit',
                    'acl_module' => 'DRI_Workflows',
                    'label' => 'LBL_ACTIVE_SMART_GUIDES',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-check',
                    'name' => 'archive_smart_guides',
                    'event' => 'parent:archive_smart_guides:click',
                    'acl_action' => 'edit',
                    'acl_module' => 'DRI_Workflows',
                    'label' => 'LBL_ARCHIVED_SMART_GUIDES',
                ],
                [
                    'type' => 'rowaction',
                    'icon' => 'sicon-check',
                    'name' => 'all_smart_guides',
                    'event' => 'parent:all_smart_guides:click',
                    'acl_action' => 'edit',
                    'acl_module' => 'DRI_Workflows',
                    'label' => 'LBL_ALL_SMART_GUIDES',
                ],
            ],
        ],
    ],
    'fields' => [
        [
            'name' => 'dri_workflow_template_id',
        ],
    ],
    'right_buttons' => [
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'buttons' => [
                [
                    'type' => 'cj-add',
                    'name' => 'start_cycle',
                    'event' => 'parent:start_cycle:click',
                    'acl_module' => 'DRI_Workflows',
                    'label' => 'LBL_ADD_BUTTON_LABEL',
                    'css_class' => 'btn-primary',
                ],
            ],
        ],
    ],
    'left_vertical_button' => [
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'buttons' => [
                [
                    'type' => 'cj-scroll',
                    'icon' => 'sicon-automate-stacked',
                    'name' => 'vertical_scroll_view',
                    'event' => 'parent:vertical_scroll_view:click',
                    'tooltip' => 'LBL_VERTICAL_SCROLL_VIEW',
                    'acl_action' => 'edit',
                    'acl_module' => 'DRI_Workflows',
                    'label' => ' ',
                ],
            ],
        ],
    ],
    'left_horizontal_button' => [
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'buttons' => [
                [
                    'type' => 'cj-scroll',
                    'icon' => 'sicon-automate-scroll',
                    'name' => 'horizontal_scroll_view',
                    'event' => 'parent:horizontal_scroll_view:click',
                    'tooltip' => 'LBL_HORIZONTAL_SCROLL_VIEW',
                    'acl_action' => 'edit',
                    'acl_module' => 'DRI_Workflows',
                    'label' => ' ',
                ],
            ],
        ],
    ],
    'last_state' => [
        'id' => 'dri-workflows-header',
        'defaults' => [
            'show_more' => 'less',
        ],
    ],
];
