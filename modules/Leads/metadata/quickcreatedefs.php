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

$viewdefs ['Leads'] =
    [
        'QuickCreate' => [
            'templateMeta' => [
                'form' => [
                    'hidden' => [
                        0 => '<input type="hidden" name="prospect_id" value="{if isset($smarty.request.prospect_id)}{$smarty.request.prospect_id}{else}{$bean->prospect_id}{/if}">',
                        1 => '<input type="hidden" name="contact_id" value="{if isset($smarty.request.contact_id)}{$smarty.request.contact_id}{else}{$bean->contact_id}{/if}">',
                        2 => '<input type="hidden" name="opportunity_id" value="{if isset($smarty.request.opportunity_id)}{$smarty.request.opportunity_id}{else}{$bean->opportunity_id}{/if}">',
                        3 => '<input type="hidden" name="account_id" value="{if isset($smarty.request.account_id)}{$smarty.request.account_id}{else}{$bean->account_id}{/if}">',
                    ],
                ],
                'maxColumns' => '2',
                'widths' => [
                    0 =>
                        [
                            'label' => '10',
                            'field' => '30',
                        ],
                    1 =>
                        [
                            'label' => '10',
                            'field' => '30',
                        ],
                ],
                'javascript' => '<script type="text/javascript" language="Javascript">function copyAddressRight(form)  {ldelim} form.alt_address_street.value = form.primary_address_street.value;form.alt_address_city.value = form.primary_address_city.value;form.alt_address_state.value = form.primary_address_state.value;form.alt_address_postalcode.value = form.primary_address_postalcode.value;form.alt_address_country.value = form.primary_address_country.value;return true; {rdelim} function copyAddressLeft(form)  {ldelim} form.primary_address_street.value =form.alt_address_street.value;form.primary_address_city.value = form.alt_address_city.value;form.primary_address_state.value = form.alt_address_state.value;form.primary_address_postalcode.value =form.alt_address_postalcode.value;form.primary_address_country.value = form.alt_address_country.value;return true; {rdelim} </script>',
                'useTabs' => false,
            ],
            'panels' => [
                'lbl_contact_information' => [
                    0 =>
                        [
                            0 =>
                                [
                                    'name' => 'first_name',
                                ],
                            1 =>
                                [
                                    'name' => 'status',
                                ],
                        ],
                    1 =>
                        [
                            0 =>
                                [
                                    'name' => 'last_name',
                                    'displayParams' => [
                                        'required' => true,
                                    ],
                                ],
                            1 =>
                                [
                                    'name' => 'phone_work',
                                ],
                        ],
                    2 =>
                        [
                            0 =>
                                [
                                    'name' => 'title',
                                ],
                            1 =>
                                [
                                    'name' => 'phone_mobile',
                                ],
                        ],
                    3 =>
                        [
                            0 =>
                                [
                                    'name' => 'department',
                                ],
                            1 =>
                                [
                                    'name' => 'phone_fax',
                                ],
                        ],
                    4 =>
                        [
                            0 =>
                                [
                                    'name' => 'account_name',
                                ],
                            1 =>
                                [
                                    'name' => 'do_not_call',
                                ],
                        ],
                    5 =>
                        [
                            0 =>
                                [
                                    'name' => 'email1',
                                ],
                        ],
                    6 =>
                        [
                            0 =>
                                [
                                    'name' => 'lead_source',
                                ],
                            1 =>
                                [
                                    'name' => 'refered_by',
                                ],
                        ],
                    7 =>
                        [
                            0 =>
                                [
                                    'name' => 'assigned_user_name',
                                ],
                            1 =>
                                [
                                    'name' => 'team_name',
                                ],
                        ],
                ],
            ],
        ],
    ];
