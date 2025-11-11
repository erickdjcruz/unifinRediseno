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
$viewdefs['Products']['DetailView'] = [
    'templateMeta' => ['maxColumns' => '2',
        'form' => ['buttons' => ['EDIT',
            'DUPLICATE',
            'DELETE',
            'AUDIT',
        ]],
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        'default' => [
            [
                'name',
                'status',
            ],

            [
                'quote_name',
                'contact_name',
            ],

            [
                'account_name',
            ],

            [
                'quantity',
                'date_purchased',
            ],

            [
                'serial_number',
                'date_support_starts',
            ],

            [
                'asset_number',
                'date_support_expires',
            ],

        ],
        [

            [
                'currency_id',
            ],

            [
                [
                    'name' => 'cost_price',
                    'label' => '{$MOD.LBL_COST_PRICE|strip_semicolon} ({$CURRENCY})',
                ],
                '',
            ],

            [

                [
                    'name' => 'list_price',
                    'label' => '{$MOD.LBL_LIST_PRICE|strip_semicolon} ({$CURRENCY})',
                ],

                [
                    'name' => 'book_value',
                    'label' => '{$MOD.LBL_BOOK_VALUE|strip_semicolon} ({$CURRENCY})',
                ],
            ],

            [

                [
                    'name' => 'discount_price',
                    'label' => '{$MOD.LBL_DISCOUNT_PRICE|strip_semicolon} ({$CURRENCY})',
                ],
                'book_value_date',
            ],

            [
                [
                    'name' => 'discount_amount',
                    'customCode' => '{if $fields.discount_select.value}{sugar_number_format var=$fields.discount_amount.value}%{else}{$fields.currency_symbol.value}{sugar_number_format var=$fields.discount_amount.value}{/if}',
                ],
                '',
            ],
        ],
        [
            [
                ['name' => 'website', 'type' => 'link'],
                'tax_class',
            ],

            [
                'manufacturer_name',
                'weight',
            ],

            [
                'mft_part_num',
                ['name' => 'category_name', 'type' => 'text'],
            ],

            [
                'vendor_part_num',
                'type_name',
            ],

            [
                'description',
            ],

            [
                'support_name',
                'support_contact',
            ],

            [
                'support_description',
                'support_term',
            ],
        ],
    ],


];
