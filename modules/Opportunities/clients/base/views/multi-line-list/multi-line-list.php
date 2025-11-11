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
$viewdefs['Opportunities']['base']['view']['multi-line-list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'sales_stage',
                    'label' => 'LBL_RENEWALS_CONSOLE_STAGE_STATUS',
                    'subfields' => [
                        [
                            'name' => 'sales_stage',
                            'type' => 'fieldset-cascade',
                            'show_child_labels' => false,
                            'label' => 'LBL_WIDGET_SALES_STAGE',
                            'default' => true,
                            'enabled' => true,
                            'widget_name' => 'widget_sales_stage',
                            'fields' => [
                                [
                                    'name' => 'sales_stage',
                                    'label' => 'LBL_SALES_STAGE',
                                    'type' => 'enum-colorcoded-fore-bkgd',
                                ],
                            ],
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
                    ],
                ],
                [
                    'name' => 'name/account_name',
                    'label' => 'LBL_RENEWALS_CONSOLE_NAME_ACCOUNT',
                    'width' => 'xlarge',
                    'subfields' => [
                        [
                            'name' => 'name',
                            'label' => 'LBL_OPPORTUNITY_NAME',
                            'default' => true,
                            'enabled' => true,
                        ],
                        [
                            'name' => 'account_name',
                            'label' => 'LBL_ACCOUNT_NAME',
                            'default' => true,
                            'enabled' => true,
                            'module' => 'Accounts',
                            'id' => 'ACCOUNT_ID',
                            'ACLTag' => 'ACCOUNT',
                            'related_fields' => ['account_id'],
                            'link' => false,
                        ],
                    ],
                ],
                [
                    'name' => 'date_closed',
                    'label' => 'LBL_DATE_CLOSED',
                    'subfields' => [
                        [
                            'name' => 'date_closed',
                            'label' => 'LBL_WIDGET_DATE_CLOSED',
                            'default' => true,
                            'enabled' => true,
                            'type' => 'relative-date',
                            'widget_name' => 'widget_date_closed',
                            'readonly' => true,
                        ],
                        [
                            'name' => 'date_closed',
                            'label' => 'LBL_DATE_CLOSED',
                            'default' => true,
                            'enabled' => true,
                            'type' => 'date-cascade',
                            'disable_field' => [
                                'total_revenue_line_items',
                                'closed_revenue_line_items',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'likely/best/worst',
                    'label' => 'LBL_RENEWALS_CONSOLE_AMOUNT',
                    'subfields' => [
                        [
                            'name' => 'amount',
                            'label' => 'LBL_WIDGET_AMOUNT',
                            'type' => 'boxplot',
                            'related_fields' => [
                                'best_case',
                                'worst_case',
                            ],
                            'enabled' => true,
                            'default' => true,
                            'widget_name' => 'widget_amount',
                        ],
                    ],
                ],
                [
                    'name' => 'lead_source',
                    'label' => 'LBL_LEAD_SOURCE',
                    'subfields' => [
                        [
                            'name' => 'lead_source',
                            'label' => 'LBL_LEAD_SOURCE',
                            'default' => true,
                            'enabled' => true,
                            'type' => 'enum',
                        ],
                    ],
                ],
                [
                    'name' => 'next_step',
                    'label' => 'LBL_NEXT_STEP',
                    'subfields' => [
                        [
                            'name' => 'next_step',
                            'label' => 'LBL_NEXT_STEP',
                            'default' => true,
                            'enabled' => true,
                        ],
                    ],
                ],
            ],
        ],
    ],
    'collectionOptions' => [
        'params' => [
            'order_by' => 'date_closed',
            'nulls_last' => true,
        ],
    ],
    'defaults' => [
        'order_by_primary' => 'date_closed:asc',
        'order_by_secondary' => '',
        'freeze_first_column' => true,
    ],
];
