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

$dictionary['ForecastOpportunities'] = ['table' => 'does_not_exist',
    'acl_fields' => false,
    'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
            'reportable' => true,
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],
        'revenue' => [
            'name' => 'revenue',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],
        'weighted_value' => [
            'name' => 'weighted_value',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],

        'account_name' => [
            'name' => 'account_name',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],
        'probability' => [
            'name' => 'probability',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],
        'worksheet_id' => [
            'name' => 'worksheet_id',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],
        //used to store worksheet values.
        'wk_likely_case' => [
            'name' => 'wk_likely_case',
            'vname' => 'LBL_LIKELY',
            'type' => 'currency',
            'source' => 'non-db',
        ],
        //used to store worksheet values.
        'wk_worst_case' => [
            'name' => 'wk_worst_case',
            'vname' => 'LBL_WORST',
            'type' => 'currency',
            'source' => 'non-db',
        ],
        //used to store worksheet values.
        'wk_best_case' => [
            'name' => 'wk_best_case',
            'vname' => 'LBL_BEST',
            'type' => 'currency',
            'source' => 'non-db',
        ],
        'next_step' => [
            'name' => 'next_step',
            'vname' => 'LB_FS_KEY',
            'type' => 'varchar',
            'source' => 'non-db',
        ],
        'opportunity_type' => [
            'name' => 'opportunity_type',
            'vname' => 'LB_FS_KEY',
            'type' => 'varchar',
            'source' => 'non-db',
        ],
        'description' => [
            'name' => 'description',
            'vname' => 'LB_FS_KEY',
            'type' => 'varchar',
            'source' => 'non-db',
        ],
        //represent's a value committed by forecast user.
        'best_case' => [
            'name' => 'best_case',
            'vname' => 'LBL_BEST_ADJUSTED',
            'type' => 'currency',
            'source' => 'non-db',
        ],
        //represent's a value committed by forecast user.
        'likely_case' => [
            'name' => 'likely_case',
            'vname' => 'LBL_LIKELY_ADJUSTED',
            'type' => 'currency',
            'source' => 'non-db',
        ],
        //represent's a value committed by forecast user.
        'worst_case' => [
            'name' => 'worst_case',
            'vname' => 'LBL_WORST_ADJUSTED',
            'type' => 'currency',
            'source' => 'non-db',
        ],

    ],
];

$dictionary['ForecastDirectReports'] = ['table' => 'does_not_exist',
    'acl_fields' => false,
    'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],
        'user_id' => [
            'name' => 'user_id',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],
        'user_name' => [
            'name' => 'user_name',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],
        'first_name' => [
            'name' => 'first_name',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],
        'last_name' => [
            'name' => 'last_name',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],

        'opp_count' => [
            'name' => 'opp_count',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],
        'opp_weigh_value' => [
            'name' => 'opp_weigh_value',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],
        //represent's a value committed by forecast user.
        'best_case' => [
            'name' => 'best_case',
            'vname' => 'LBL_BEST_ADJUSTED',
            'type' => 'currency',
            'source' => 'non-db',
        ],
        //represent's a value committed by forecast user.
        'likely_case' => [
            'name' => 'likely_case',
            'vname' => 'LBL_LIKELY_ADJUSTED',
            'type' => 'currency',
            'source' => 'non-db',
        ],
        //represent's a value committed by forecast user.
        'worst_case' => [
            'name' => 'worst_case',
            'vname' => 'LBL_WORST_ADJUSTED',
            'type' => 'currency',
            'source' => 'non-db',
        ],
        //used to store worksheet values.
        'wk_likely_case' => [
            'name' => 'wk_likely_case',
            'vname' => 'LBL_LIKELY',
            'type' => 'currency',
            'source' => 'non-db',
        ],
        //used to store worksheet values.
        'wk_worst_case' => [
            'name' => 'wk_worst_case',
            'vname' => 'LBL_WORST',
            'type' => 'currency',
            'source' => 'non-db',
        ],
        //used to store worksheet values.
        'wk_best_case' => [
            'name' => 'wk_best_case',
            'vname' => 'LBL_BEST',
            'type' => 'currency',
            'source' => 'non-db',
        ],
        'ref_timeperiod_id' => [
            'name' => 'ref_timeperiod_id',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],
        'ref_user_id' => [
            'name' => 'ref_user_id',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],
        'forecast_type' => [
            'name' => 'forecast_type',
            'vname' => 'LB_FS_KEY',
            'type' => 'id',
            'source' => 'non-db',
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LBL_FDR_DATE_COMMIT',
            'type' => 'datetime',
            'source' => 'non-db',
        ],
        'date_comitted' => [
            'name' => 'date_comitted',
            'vname' => 'LBL_FDR_DATE_COMMIT',
            'type' => 'date',
            'source' => 'non-db',
        ],

    ],
];
$dictionary['Forecast'] = [
    'table' => 'forecasts',
    'color' => 'army',
    'icon' => 'sicon-forecasts-lg',
    'acl_fields' => false,
    'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_FORECAST_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
            'comment' => 'Unique identifier',
        ],

        'timeperiod_id' => [
            'name' => 'timeperiod_id',
            'vname' => 'LBL_FORECAST_TIME_ID',
            'type' => 'enum',
            'dbType' => 'id',
            'reportable' => true,
            'function' => 'getTimePeriodsDropDownForForecasts',
            'comment' => 'ID of the associated time period for this forecast',
        ],

        'commit_type' => [
            'name' => 'commit_type',
            'type' => 'string',
            'source' => 'non-db',
            'comment' => 'This is used by the commit code to figure out what type of worksheet we are committing',
        ],

        'forecast_type' => [
            'name' => 'forecast_type',
            'vname' => 'LBL_FORECAST_TYPE',
            'type' => 'enum',
            'len' => 100,
            'massupdate' => false,
            'options' => 'forecast_type_dom',
            'comment' => 'Indicator of whether forecast is direct or rollup',
            'reportable' => false,
        ],
        'opp_count' => [
            'name' => 'opp_count',
            'vname' => 'LBL_FORECAST_OPP_COUNT',
            'type' => 'int',
            'len' => '5',
            'comment' => 'Number of opportunities represented by this forecast',
        ],
        'pipeline_opp_count' => [
            'name' => 'pipeline_opp_count',
            'vname' => 'LBL_FORECAST_PIPELINE_OPP_COUNT',
            'type' => 'int',
            'len' => '5',
            'studio' => false,
            'default' => '0',
            'comment' => 'Number of opportunities minus closed won/closed lost represented by this forecast',
        ],
        'pipeline_amount' => [
            'name' => 'pipeline_amount',
            'vname' => 'LBL_PIPELINE_REVENUE',
            'type' => 'currency',
            'studio' => false,
            'default' => '0',
            'comment' => 'Total of opportunities minus closed won/closed lost represented by this forecast',
        ],
        'closed_amount' => [
            'name' => 'closed_amount',
            'vname' => 'LBL_CLOSED',
            'type' => 'currency',
            'studio' => false,
            'default' => '0',
            'comment' => 'Total of closed won items in the forecast',
        ],
        'opp_weigh_value' => [
            'name' => 'opp_weigh_value',
            'vname' => 'LBL_FORECAST_OPP_WEIGH',
            'type' => 'int',
            'comment' => 'Weighted amount of all opportunities represented by this forecast',
            'reportable' => false,
        ],
        'best_case' => [
            'name' => 'best_case',
            'vname' => 'LBL_BEST',
            'type' => 'currency',
            'comment' => 'Best case forecast amount',
        ],
        //renamed commit_value to likely_case
        'likely_case' => [
            'name' => 'likely_case',
            'vname' => 'LBL_FORECAST_OPP_COMMIT',
            'type' => 'currency',
            'comment' => 'Likely case forecast amount',
        ],
        'worst_case' => [
            'name' => 'worst_case',
            'vname' => 'LBL_WORST',
            'type' => 'currency',
            'comment' => 'Worst case likely amount',
        ],
        'user_id' => [
            'name' => 'user_id',
            'vname' => 'LBL_FORECAST_USER',
            'type' => 'id',
            'reportable' => false,
            'comment' => 'User to which this forecast pertains',
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date record created',
        ],
        'date_modified' => [
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date record modified',
        ],
        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'required' => false,
            'reportable' => false,
            'comment' => 'Record deletion indicator',
        ],
        'user_name' =>
            [
                'name' => 'user_name',
                'rname' => 'user_name',
                'id_name' => 'user_id',
                'vname' => 'LBL_USER_NAME',
                'type' => 'relate',
                'table' => 'users',
                'isnull' => 'true',
                'module' => 'Users',
                'massupdate' => false,
                'source' => 'non-db',
            ],
        'reports_to_user_name' =>
            [
                'name' => 'reports_to_user_name',
                'rname' => 'user_name',
                'id_name' => 'reports_to_user_name',
                'vname' => 'LBL_REPORTS_TO_USER_NAME',
                'type' => 'relate',
                'table' => 'reports_to',
                'isnull' => 'true',
                'module' => 'Users',
                'massupdate' => false,
                'source' => 'non-db',
            ],
//timeperiod's start date
        'start_date' => [
            'name' => 'start_date',
            'type' => 'date',
            'source' => 'non-db',
            'table' => 'timeperiods',
        ],
//timeperiod's end date
            'end_date' => [
            'name' => 'end_date',
            'type' => 'date',
            'source' => 'non-db',
            'table' => 'timeperiods',
            ],
//timeperiod's name
            'name' => [
            'name' => 'name',
            'type' => 'varchar',
            'source' => 'non-db',
            ],
        'created_by_link' => [
            'name' => 'created_by_link',
            'type' => 'link',
            'relationship' => 'forecasts_created_by',
            'vname' => 'LBL_CREATED_BY_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
        ],
            'closed_count' => [
            'name' => 'closed_count',
            'type' => 'int',
            'source' => 'non-db',
            'comment' => 'This is used by the commit code to determine how many closed opps exist for the pipeline calc',
            ],
    ],

    'relationships' => [

        'forecasts_created_by' => ['lhs_module' => 'Users', 'lhs_table' => 'users', 'lhs_key' => 'id',
            'rhs_module' => 'Forecasts', 'rhs_table' => 'forecasts', 'rhs_key' => 'user_id',
            'relationship_type' => 'one-to-many'],

    ],
    'indices' => [
        ['name' => 'forecastspk', 'type' => 'primary', 'fields' => ['id']],
        ['name' => 'idx_forecast_user_tp', 'type' => 'index', 'fields' => ['user_id', 'timeperiod_id', 'date_modified']],
    ],
    'acls' => ['SugarACLStatic' => true],
];

VardefManager::createVardef(
    'Forecasts',
    'Forecast',
    [
        'currency',
    ]
);
