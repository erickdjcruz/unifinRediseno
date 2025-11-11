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
$viewdefs['ProductTemplates']['EditView'] = [
    'templateMeta' => ['maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [

        'default' => [
            [
                ['name' => 'name', 'label' => 'LBL_NAME', 'displayParams' => ['required' => true]],
                [
                    'name' => 'status',
                    'label' => 'LBL_STATUS',
                ],
            ],

            [
                [
                    'name' => 'category_name',
                    'label' => 'LBL_CATEGORY_NAME',
                ],

            ],

            [
                [
                    'name' => 'website',
                    'label' => 'LBL_URL',
                ],
                [
                    'name' => 'date_available',
                    'label' => 'LBL_DATE_AVAILABLE',
                ],

            ],

            [
                [
                    'name' => 'tax_class',
                    'label' => 'LBL_TAX_CLASS',
                ],
                [
                    'name' => 'qty_in_stock',
                    'label' => 'LBL_QUANTITY',
                ],
            ],

            [
                [
                    'name' => 'manufacturer_id',
                    'label' => 'LBL_LIST_MANUFACTURER_ID',
                ],
                [
                    'name' => 'weight',
                    'label' => 'LBL_WEIGHT',
                ],
            ],

            [
                [
                    'name' => 'mft_part_num',
                    'label' => 'LBL_MFT_PART_NUM',
                ],
            ],

            [
                [
                    'name' => 'vendor_part_num',
                    'label' => 'LBL_VENDOR_PART_NUM',
                ],
                [
                    'name' => 'type_id',
                    'label' => 'LBL_TYPE',
                ],
            ],

            [
                [
                    'name' => 'currency_id',
                    'label' => 'LBL_CURRENCY',
                ],
                [
                    'name' => 'support_name',
                    'label' => 'LBL_SUPPORT_NAME',
                ],
            ],

            [
                [
                    'name' => 'cost_price',
                    'label' => 'LBL_COST_PRICE',
                ],
                [
                    'name' => 'support_contact',
                    'label' => 'LBL_SUPPORT_CONTACT',
                ],
            ],

            [
                [
                    'name' => 'list_price',
                    'label' => 'LBL_LIST_PRICE',
                ],
                [
                    'name' => 'support_description',
                    'label' => 'LBL_SUPPORT_DESCRIPTION',
                ],
            ],

            [
                [
                    'name' => 'discount_price',
                    'label' => 'LBL_DISCOUNT_PRICE',
                ],
                [
                    'name' => 'support_term',
                    'label' => 'LBL_SUPPORT_TERM',
                ],
            ],

            [
                [
                    'name' => 'pricing_formula',
                    'label' => 'LBL_PRICING_FORMULA',
                ],
            ],

            [
                ['name' => 'description', 'label' => 'LBL_DESCRIPTION'],
            ],
        ],
    ],


];
