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
/*********************************************************************************
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$viewdefs['PurchasedLineItems']['mobile']['view']['detail'] = [
    'templateMeta' => [
        'maxColumns' => '1',
        'widths' => [
            [
                'label' => '10',
                'field' => '30',
            ],
        ],
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                'name',
                'purchase_name',
                'date_closed',
                'product_template_name',
                'revenue',
                'quantity',
                'deal_calc',
                'discount_price',
                'discount_amount',
                'discount_select',
                'tag',
                'commentlog',
                'service',
                'annual_revenue',
                'service_start_date',
                'service_end_date',
                'service_duration_unit',
                'service_duration_value',
                'renewable',
                'description',
                'assigned_user_name',
                'team_name',
            ],
        ],
    ],
];
