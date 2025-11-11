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
$viewdefs['ProductTemplates']['base']['filter']['default'] = [
    'default_filter' => 'all_records',
    'fields' => [
        'name' => [],
        'type_id' => [],
        'manufacturer_id' => [],
        'mft_part_num' => [],
        'service_duration_value' => [],
        'service_duration_unit' => [],
        'discount_price_date' => [],
        'vendor_part_num' => [],
        'category_id' => [],
        'contact_name' => [],
        'date_available' => [],
        'url' => [],
        'support_term' => [],
        'tag' => [],
        'active_status' => [],
        '$favorite' => [
            'predefined_filter' => true,
            'vname' => 'LBL_FAVORITES_FILTER',
        ],
    ],
];
