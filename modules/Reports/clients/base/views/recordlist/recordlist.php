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
$viewdefs['Reports']['base']['view']['recordlist'] = [
    'favorite' => true,
    'sticky_resizable_columns' => true,
    'selection' => [
        'type' => 'multi',
        'actions' => [
            [
                'name' => 'massupdate_button',
                'type' => 'button',
                'label' => 'LBL_MASS_UPDATE',
                'primary' => true,
                'events' => [
                    'click' => 'list:massupdate:fire',
                ],
                'acl_action' => 'massupdate',
            ],
            [
                'name' => 'calc_field_button',
                'type' => 'button',
                'label' => 'LBL_UPDATE_CALC_FIELDS',
                'events' => [
                    'click' => 'list:updatecalcfields:fire',
                ],
                'acl_action' => 'massupdate',
            ],
            [
                'name' => 'massdelete_button',
                'type' => 'button',
                'label' => 'LBL_DELETE',
                'acl_action' => 'delete',
                'primary' => true,
                'events' => [
                    'click' => 'list:massdelete:fire',
                ],
            ],
        ],
    ],
    'rowactions' => [
        'actions' => [
            [
                'type' => 'rowaction',
                'css_class' => 'btn',
                'tooltip' => 'LBL_PREVIEW',
                'event' => 'list:preview:fire',
                'icon' => 'sicon-preview',
                'acl_action' => 'view',
            ],
            [
                'type' => 'rowaction',
                'name' => 'edit_button',
                'label' => 'LBL_EDIT_BUTTON',
                'event' => 'list:editrow:fire',
                'acl_action' => 'edit',
            ],
            [
                'type' => 'rowaction',
                'name' => 'edit_report_button',
                'label' => 'LBL_EDIT_REPORT_BUTTON',
                'event' => 'list:editreport:fire',
                'acl_action' => 'edit',
            ],
            [
                'type' => 'rowaction',
                'event' => 'list:copy:fire',
                'name' => 'copy',
                'label' => 'LBL_DUPLICATE_BUTTON_LABEL',
                'acl_action' => 'copy',
            ],
            [
                'type' => 'rowaction',
                'name' => 'create_schedule_button',
                'label' => 'LBL_SCHEDULE_REPORT_BUTTON',
                'event' => 'list:schedulereport:fire',
                'acl_module' => 'ReportSchedules',
                'acl_action' => 'edit',
            ],
            [
                'type' => 'rowaction',
                'name' => 'view_schedules_button',
                'label' => 'LBL_VIEW_SCHEDULES_BUTTON',
                'event' => 'list:viewschedules:fire',
                'acl_module' => 'ReportSchedules',
                'acl_action' => 'list',
            ],
            [
                'type' => 'rowaction',
                'name' => 'delete_button',
                'event' => 'list:deleterow:fire',
                'label' => 'LBL_DELETE_BUTTON',
                'acl_action' => 'delete',
            ],
        ],
    ],
];
