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

$searchdefs['ProductTemplates'] = [
    'templateMeta' => [
        'maxColumns' => '3',
        'maxColumnsBasic' => '4',
        'widths' => ['label' => '10', 'field' => '30'],
    ],
    'layout' => [
        'basic_search' => [
            'name',
        ],
        'advanced_search' => [
            'name',
            'tax_class',
            ['name' => 'type_id', 'label' => 'LBL_TYPE', 'type' => 'multienum', 'function' => ['name' => 'getProductTypes', 'returns' => 'html', 'include' => 'modules/ProductTemplates/ProductTemplate.php', 'preserveFunctionValue' => true]],
            ['name' => 'manufacturer_id', 'label' => 'LBL_MANUFACTURER', 'type' => 'multienum', 'function' => ['name' => 'getManufacturers', 'returns' => 'html', 'include' => 'modules/ProductTemplates/ProductTemplate.php', 'preserveFunctionValue' => true]],
            'mft_part_num',
            ['name' => 'discount_price_date', 'label' => 'LBL_DISCOUNT_PRICE_DATE'],
            'vendor_part_num',
            ['name' => 'category_id', 'label' => 'LBL_CATEGORY', 'type' => 'multienum', 'function' => ['name' => 'getCategories', 'returns' => 'html', 'include' => 'modules/ProductTemplates/ProductTemplate.php', 'preserveFunctionValue' => true]],
            ['name' => 'contact_name', 'label' => 'LBL_CONTACT_NAME'],
            'date_available',
            ['name' => 'url', 'label' => 'LBL_URL'],
            'support_term',
        ],
    ],
];
