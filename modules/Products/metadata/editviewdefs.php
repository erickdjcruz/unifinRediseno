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
$viewdefs['Products']['EditView'] = [
    'templateMeta' => ['maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'javascript' => '{sugar_getscript file="modules/Products/EditView.js"}',
    ],

    'panels' => [
        'default' => [

            [
                ['name' => 'name',
                    'displayParams' => ['required' => true],
                    'customCode' => '<input name="name" id="name" type="text" value="{$fields.name.value}">' .
                        '<input name="product_template_id" id="product_template_id" type="hidden" value="{$fields.product_template_id.value}">' .
                        '&nbsp;<input title="{$APP.LBL_SELECT_BUTTON_TITLE}" type="button" class="button" value="{$APP.LBL_SELECT_BUTTON_LABEL}" onclick=\'return get_popup_product("{$form_name}");\'>' .
                        '&nbsp;<input tabindex="1" title="{$LBL_CLEAR_BUTTON_TITLE}" class="button" onclick="this.form.product_template_id.value = \'\'; this.form.name.value = \'\';" type="button" value="{$APP.LBL_CLEAR_BUTTON_LABEL}">',
                ],
                'status',
            ],

            [
                'account_name',
                'contact_name',
            ],

            [
                ['name' => 'quantity', 'displayParams' => ['size' => 5]],
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
                '',
            ],

            [
                'cost_price',
                '',
            ],

            [
                'list_price',
                'book_value',
            ],

            [
                'discount_price',
                'book_value_date',
            ],
            [
                'discount_amount',
                'discount_select',
            ],
        ],

        [

            [
                ['name' => 'website', 'type' => 'Link'],
                'tax_class',
            ],

            [
                'manufacturer_id',
                'weight',
            ],

            [
                'mft_part_num',
                'category_id',
            ],

            [
                'vendor_part_num',
                'type_id',
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
