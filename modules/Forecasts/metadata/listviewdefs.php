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

$listViewDefs['ForecastOpportunities'] = [
    'NAME' => [
        'width' => '25',
        'label' => 'LBL_OW_OPPORTUNITIES',
        'tablename' => 'opportunities',
    ],
    'REVENUE' => [
        'width' => '10',
        'label' => 'LBL_OW_REVENUE',
    ],
    'PROBABILITY' => [
        'width' => '5',
        'label' => 'LBL_OW_PROBABILITY',
        'tablename' => 'opportunities',
    ],
    'WEIGHTED_VALUE' => [
        'width' => '15',
        'label' => 'LBL_OW_WEIGHTED',
    ],
    'WK_BEST_CASE' => [
        'width' => '15',
        'label' => 'LBL_FDR_WK_BEST_CASE',
        'edit' => true,
        'sortable' => false,
    ],
    'WK_LIKELY_CASE' => [
        'width' => '15',
        'label' => 'LBL_FDR_WK_LIKELY_CASE',
        'edit' => true,
        'sortable' => false,
    ],
    'WK_WORST_CASE' => [
        'width' => '15',
        'label' => 'LBL_FDR_WK_WORST_CASE',
        'edit' => true,
        'sortable' => false,
    ],
    //not visible in the list view.
    'ACCOUNT_NAME' => [
        'label' => 'LBL_OW_ACCOUNTNAME',
        'hidden' => true,
        'width' => '0',
    ],
    'NEXT_STEP' => [
        'label' => 'LBL_OW_NEXT_STEP',
        'hidden' => true,
        'width' => '0',
    ],
    'OPPORTUNITY_TYPE' => [
        'label' => 'LBL_OW_TYPE',
        'hidden' => true,
        'width' => '0',
    ],
    'DESCRIPTION' => [
        'label' => 'LBL_OW_DESCRIPTION',
        'hidden' => true,
        'width' => '0',
    ],
];

$listViewDefs['ForecastDirectReports'] = [
    'USER_NAME' => [
        'width' => '16',
        'label' => 'LBL_FDR_USER_NAME',
        'tablename' => 'users',
    ],
    'BEST_CASE' => [
        'width' => '12',
        'label' => 'LBL_FDR_C_BEST_CASE',
        'sortable' => false,
    ],
    'LIKELY_CASE' => [
        'width' => '12',
        'label' => 'LBL_FDR_C_LIKELY_CASE',
        'sortable' => false,
    ],
    'WORST_CASE' => [
        'width' => '12',
        'label' => 'LBL_FDR_C_WORST_CASE',
        'sortable' => false,
    ],
    'DATE_COMMITTED' => [
        'width' => '12',
        'label' => 'LBL_FDR_DATE_COMMIT',
        'sortable' => false,
    ],
    'WK_BEST_CASE' => [
        'width' => '12',
        'label' => 'LBL_FDR_WK_BEST_CASE',
        'edit' => true,
        'sortable' => false,
    ],
    'WK_LIKELY_CASE' => [
        'width' => '12',
        'label' => 'LBL_FDR_WK_LIKELY_CASE',
        'edit' => true,
        'sortable' => false,
    ],
    'WK_WORST_CASE' => [
        'width' => '12',
        'label' => 'LBL_FDR_WK_WORST_CASE',
        'edit' => true,
        'sortable' => false,
    ],
//fields not visible in the list view.
    'OPP_COUNT' => [
        'hidden' => true,
        'width' => '0',
        'label' => 'LBL_FDR_OPPORTUNITIES'],
    'OPP_WEIGH_VALUE' => [
        'hidden' => true,
        'width' => '0',
        'label' => 'LBL_FDR_WEIGH'],
    'FORECAST_TYPE' => [
        'width' => '0',
        'label' => 'LBL_FDR_ADJ_AMOUNT',
        'hidden' => true,],
    'DATE_ENTERED' => [
        'width' => '0',
        'label' => 'LBL_FDR_DATE_COMMIT',
        'hidden' => true,
    ],

];
