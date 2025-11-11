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
$viewdefs['Accounts']['base']['view']['multi-line-list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_RENEWALS_CONSOLE_ACCOUNT_NAME_INDUSTRY',
                    'width' => 'xlarge',
                    'subfields' => [
                        [
                            'name' => 'name',
                            'label' => 'LBL_LIST_ACCOUNT_NAME',
                            'width' => 'large',
                        ],
                        [
                            'name' => 'industry',
                            'label' => 'LBL_INDUSTRY',
                            'default' => true,
                            'enabled' => true,
                            'readonly' => true,
                            'type' => 'enum',
                        ],
                    ],
                ],
                [
                    'name' => 'description',
                    'label' => 'LBL_DESCRIPTION',
                    'subfields' => [
                        [
                            'name' => 'description',
                            'label' => 'LBL_DESCRIPTION',
                            'default' => true,
                            'enabled' => true,
                            'sortable' => false,
                        ],
                    ],
                ],
                [
                    'name' => 'next_renewal_date',
                    'label' => 'LBL_NEXT_RENEWAL_DATE',
                    'subfields' => [
                        [
                            'name' => 'next_renewal_date',
                            'label' => 'LBL_WIDGET_NEXT_RENEWAL_DATE',
                            'default' => true,
                            'enabled' => true,
                            'type' => 'relative-date',
                            'widget_name' => 'widget_next_renewal_date',
                        ],
                        [
                            'name' => 'next_renewal_date',
                            'label' => 'LBL_NEXT_RENEWAL_DATE',
                            'default' => true,
                            'enabled' => true,
                            'type' => 'date',
                        ],
                    ],
                ],
                [
                    'name' => 'annual_revenue',
                    'label' => 'LBL_ANNUAL_REVENUE',
                    'subfields' => [
                        [
                            'name' => 'annual_revenue',
                            'label' => 'LBL_ANNUAL_REVENUE',
                            'default' => true,
                            'enabled' => true,
                        ],
                    ],
                ],
                [
                    'name' => 'service_level',
                    'label' => 'LBL_SERVICE_LEVEL',
                    'subfields' => [
                        [
                            'name' => 'service_level',
                            'label' => 'LBL_SERVICE_LEVEL',
                            'type' => 'enum',
                            'options' => 'service_level_dom',
                            'audited' => true,
                            'comment' => 'An indication of the service level of a company',
                            'default' => true,
                            'enabled' => true,
                        ],
                    ],
                ],
                [
                    'name' => 'account_type',
                    'label' => 'LBL_TYPE',
                    'subfields' => [
                        [
                            'name' => 'account_type',
                            'label' => 'LBL_TYPE',
                            'default' => true,
                            'enabled' => true,
                            'type' => 'enum',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'collectionOptions' => [
        'params' => [
            'order_by' => 'date_modified',
            'nulls_last' => true,
        ],
    ],
    'filterDef' => [
        [
            '$owner' => '',
        ],
    ],
    'defaults' => [
        'order_by_primary' => 'next_renewal_date:asc',
        'order_by_secondary' => '',
        'freeze_first_column' => true,
    ],
];
