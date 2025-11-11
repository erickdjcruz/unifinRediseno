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

$viewdefs['Opportunities']['base']['view']['record'] = [
    'buttons' => [
        [
            'type' => 'button',
            'name' => 'cancel_button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
            'showOn' => 'edit',
            'events' => [
                'click' => 'button:cancel_button:click',
            ],
        ],
        [
            'type' => 'rowaction',
            'event' => 'button:save_button:click',
            'name' => 'save_button',
            'label' => 'LBL_SAVE_BUTTON_LABEL',
            'css_class' => 'btn btn-primary',
            'showOn' => 'edit',
            'acl_action' => 'edit',
        ],
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'primary' => true,
            'showOn' => 'view',
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'event' => 'button:edit_button:click',
                    'name' => 'edit_button',
                    'label' => 'LBL_EDIT_BUTTON_LABEL',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'escalate-action',
                    'event' => 'button:escalate_button:click',
                    'name' => 'escalate_button',
                    'label' => 'LBL_ESCALATE_BUTTON_LABEL',
                    'acl_action' => 'create',
                ],
                [
                    'type' => 'shareaction',
                    'name' => 'share',
                    'label' => 'LBL_RECORD_SHARE_BUTTON',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'pdfaction',
                    'name' => 'download-pdf',
                    'label' => 'LBL_PDF_VIEW',
                    'action' => 'download',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'pdfaction',
                    'name' => 'email-pdf',
                    'label' => 'LBL_PDF_EMAIL',
                    'action' => 'email',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:find_duplicates_button:click',
                    'name' => 'find_duplicates_button',
                    'label' => 'LBL_DUP_MERGE',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:duplicate_button:click',
                    'name' => 'duplicate_button',
                    'label' => 'LBL_DUPLICATE_BUTTON_LABEL',
                    'acl_module' => 'Opportunities',
                    'acl_action' => 'create',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:historical_summary_button:click',
                    'name' => 'historical_summary_button',
                    'label' => 'LBL_HISTORICAL_SUMMARY',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:audit_button:click',
                    'name' => 'audit_button',
                    'label' => 'LNK_VIEW_CHANGE_LOG',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:delete_button:click',
                    'name' => 'delete_button',
                    'label' => 'LBL_DELETE_BUTTON_LABEL',
                    'acl_action' => 'delete',
                ],
            ],
        ],
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_header',
            'header' => true,
            'fields' => [
                [
                    'name' => 'picture',
                    'type' => 'avatar',
                    'size' => 'large',
                    'dismiss_label' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'name',
                    'related_fields' => [
                        'total_revenue_line_items',
                        'closed_revenue_line_items',
                        'included_revenue_line_items',
                    ],
                ],
                [
                    'name' => 'favorite',
                    'label' => 'LBL_FAVORITE',
                    'type' => 'favorite',
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'renewal',
                    'type' => 'renewal',
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'is_escalated',
                    'type' => 'badge',
                    'badge_label' => 'LBL_ESCALATED',
                    'warning_level' => 'important',
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'follow',
                    'label' => 'LBL_FOLLOW',
                    'type' => 'follow',
                    'readonly' => true,
                    'dismiss_label' => true,
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'labels' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'account_name',
                    'related_fields' => [
                        'account_id',
                    ],
                ],
                [
                    'name' => 'date_closed',
                    'type' => 'date-cascade',
                    'label' => 'LBL_LIST_DATE_CLOSED',
                    'disable_field' => [
                        'total_revenue_line_items',
                        'closed_revenue_line_items',
                    ],
                ],
                [
                    'name' => 'sales_status',
                    'label' => 'LBL_SALES_STATUS',
                    'default' => true,
                    'enabled' => true,
                    'type' => 'enum',
                ],
                [
                    'name' => 'sales_stage',
                    'type' => 'enum-cascade',
                    'label' => 'LBL_SALES_STAGE',
                    'disable_field' => [
                        'total_revenue_line_items',
                        'closed_revenue_line_items',
                    ],
                    'span' => 6,
                ],
                [
                    'name' => 'amount',
                    'type' => 'currency',
                    'label' => 'LBL_LIKELY',
                    'related_fields' => [
                        'amount',
                        'currency_id',
                        'base_rate',
                    ],
                    'currency_field' => 'currency_id',
                    'base_rate_field' => 'base_rate',
                ],
                [
                    'name' => 'service_start_date',
                    'type' => 'date-cascade',
                    'label' => 'LBL_SERVICE_START_DATE',
                    'disable_field' => 'service_open_revenue_line_items',
                    'related_fields' => [
                        'service_open_revenue_line_items',
                    ],
                ],
                [
                    'name' => 'forecasted_likely',
                    'comment' => 'Rollup of included RLIs on the Opportunity',
                    'readonly' => true,
                    'related_fields' => [
                        0 => 'currency_id',
                        1 => 'base_rate',
                    ],
                    'label' => 'LBL_FORECASTED_LIKELY',
                    'span' => 6,
                ],
                [
                    'name' => 'service_duration',
                    'type' => 'fieldset-cascade',
                    'label' => 'LBL_SERVICE_DURATION',
                    'inline' => true,
                    'show_child_labels' => false,
                    'css_class' => 'service-duration-field',
                    'fields' => [
                        [
                            'name' => 'service_duration_value',
                            'label' => 'LBL_SERVICE_DURATION_VALUE',
                        ],
                        [
                            'name' => 'service_duration_unit',
                            'label' => 'LBL_SERVICE_DURATION_UNIT',
                        ],
                    ],
                    'related_fields' => [
                        'service_duration_value',
                        'service_duration_unit',
                        'service_open_flex_duration_rlis',
                    ],
                    'disable_field' => 'service_open_flex_duration_rlis',
                ],
                [
                    'name' => 'commit_stage',
                    'type' => 'enum-cascade',
                    'disable_field' => 'closed_won_revenue_line_items',
                    'disable_positive' => true,
                    'related_fields' => [
                        'probability',
                        'closed_won_revenue_line_items',
                    ],
                    'span' => 6,
                ],
                [
                    'name' => 'commentlog',
                    'label' => 'LBL_COMMENTLOG',
                    'displayParams' => [
                        'type' => 'commentlog',
                        'fields' => [
                            'entry',
                            'date_entered',
                            'created_by_name',
                        ],
                        'max_num' => 100,
                    ],
                ],
                [
                    'name' => 'lost',
                    'comment' => 'Rollup of lost RLIs on the Opportunity',
                    'readonly' => true,
                    'related_fields' => [
                        0 => 'currency_id',
                        1 => 'base_rate',
                    ],
                    'label' => 'LBL_LOST',
                    'span' => 6,
                ],
                [
                    'name' => 'tag',
                    'span' => 6,
                ],
            ],
        ],
        [
            'name' => 'panel_hidden',
            'label' => 'LBL_RECORD_SHOWMORE',
            'hide' => true,
            'placeholders' => true,
            'columns' => 2,
            'fields' => [
                'next_step',
                'opportunity_type',
                'renewal_parent_name',
                'lead_source',
                [
                    'name' => 'description',
                    'span' => 12,
                ],
                'assigned_user_name',
                'team_name',
                [
                    'name' => 'date_entered_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_ENTERED',
                    'fields' => [
                        [
                            'name' => 'date_entered',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'created_by_name',
                        ],
                    ],
                ],
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_MODIFIED',
                    'fields' => [
                        [
                            'name' => 'date_modified',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'modified_by_name',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
