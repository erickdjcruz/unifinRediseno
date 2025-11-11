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
$viewdefs['ProductTemplates']['DetailView'] = [
    'templateMeta' => ['maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [

        [
            'name',
            'status',
        ],

        [

            [
                'name' => 'website',
                'label' => 'LBL_URL',
                'type' => 'link',
            ],
            'date_available',
        ],

        [
            'tax_class',

            [
                'name' => 'qty_in_stock',
                'label' => 'LBL_QUANTITY',
            ],
        ],

        [
            'manufacturer_id',
            'weight',
        ],

        [
            'mft_part_num',

            [
                'name' => 'category_name',
                'type' => 'varchar',
                'label' => 'LBL_CATEGORY',
            ],
        ],

        [
            'vendor_part_num',

            [
                'name' => 'type_id',
                'type' => 'varchar',
                'label' => 'LBL_TYPE',
            ],
        ],

        [
            'currency_id',
            'support_name',
        ],

        [

            [
                'name' => 'cost_price',
                'label' => '{$MOD.LBL_COST_PRICE|strip_semicolon} ({$CURRENCY})',
            ],
            'support_contact',
        ],

        [

            [
                'name' => 'list_price',
                'label' => '{$MOD.LBL_LIST_PRICE|strip_semicolon} ({$CURRENCY})',
            ],
            'support_description',
        ],

        [

            [
                'name' => 'discount_price',
                'label' => '{$MOD.LBL_DISCOUNT_PRICE|strip_semicolon} ({$CURRENCY})',
            ],
            'support_term',
        ],

        [
            'pricing_formula',
        ],

        [
            ['name' => 'description', 'displayParams' => ['nl2br' => true]],
        ],
    ],


];
