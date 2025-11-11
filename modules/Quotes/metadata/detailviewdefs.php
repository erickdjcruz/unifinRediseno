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

$viewdefs['Quotes']['DetailView'] = [
    'templateMeta' => [
        'form' => [
            'closeFormBeforeCustomButtons' => true,
            'buttons' => [
                'EDIT',
                'SHARE',
                'DUPLICATE',
                'DELETE',
                [
                    'customCode' => '<form action="index.php" method="POST" name="Quote2Opp" id="form">
                    <input type="hidden" name="module" value="Quotes">
                    <input type="hidden" name="record" value="{$fields.id.value}">
                    <input type="hidden" name="user_id" value="{$current_user->id}">
                    <input type="hidden" name="team_id" value="{$fields.team_id.value}">
                    <input type="hidden" name="user_name" value="{$current_user->user_name}">
                    <input type="hidden" name="action" value="QuoteToOpportunity">
                    <input type="hidden" name="opportunity_subject" value="{$fields.name.value}">
                    <input type="hidden" name="opportunity_name" value="{$fields.name.value}">
                    <input type="hidden" name="opportunity_id" value="{$fields.billing_account_id.value}">
                    <input type="hidden" name="amount" value="{$fields.total.value}">
                    <input type="hidden" name="valid_until" value="{$fields.date_quote_expected_closed.value}">
                    <input type="hidden" name="currency_id" value="{$fields.currency_id.value}">
                    <input id="create_opp_from_quote_button" title="{$APP.LBL_QUOTE_TO_OPPORTUNITY_TITLE}"
                        class="button" type="submit" name="opp_to_quote_button"
                        value="{$APP.LBL_QUOTE_TO_OPPORTUNITY_LABEL}" {$DISABLE_CONVERT}></form>',
                ],
            ],
            'footerTpl' => 'modules/Quotes/tpls/DetailViewFooter.tpl',
        ],
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        'lbl_quote_information' => [
            [
                [
                    'name' => 'name',
                    'label' => 'LBL_QUOTE_NAME',
                ],
                [
                    'name' => 'opportunity_name',
                ],
            ],
            [
                'quote_num',
                'quote_stage',
            ],
            [
                'purchase_order_num',
                [
                    'name' => 'date_quote_expected_closed',
                    'label' => 'LBL_DATE_QUOTE_EXPECTED_CLOSED',
                ],
            ],
            [
                'payment_terms',
                'original_po_date',
            ],
            [
                'billing_account_name',
                'shipping_account_name',
            ],
            [
                'billing_contact_name',
                'shipping_contact_name',
            ],
            [
                [
                    'name' => 'billing_address_street',
                    'label' => 'LBL_BILL_TO',
                    'type' => 'address',
                    'displayParams' => ['key' => 'billing'],
                ],
                [
                    'name' => 'shipping_address_street',
                    'label' => 'LBL_SHIP_TO',
                    'type' => 'address',
                    'displayParams' => ['key' => 'shipping'],
                ],
            ],
            [
                'description',
            ],
        ],
        'LBL_PANEL_ASSIGNMENT' => [
            [
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO',
                ],
                [
                    'name' => 'date_modified',
                    'label' => 'LBL_DATE_MODIFIED',
                    'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
                ],
            ],
            [

                'team_name',
                [
                    'name' => 'date_entered',
                    'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
                ],
            ],
        ],
    ],
];
