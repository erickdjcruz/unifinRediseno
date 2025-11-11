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

$viewdefs = [
    'Bugs' => [
        'QuickCreate' => [
            'templateMeta' => [
                'form' => [
                    'hidden' => [
                        0 => '<input type="hidden" name="account_id" value="{$smarty.request.account_id}">',
                        1 => '<input type="hidden" name="contact_id" value="{$smarty.request.contact_id}">',
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
            ],
            'panels' => [
                'DEFAULT' => [
                    0 =>
                        [
                            0 =>
                                [
                                    'name' => 'priority',
                                ],
                            1 =>
                                [
                                    'name' => 'assigned_user_name',
                                ],
                        ],
                    1 =>
                        [
                            0 =>
                                [
                                    'name' => 'source',
                                ],
                            1 =>
                                [
                                    'name' => 'team_name',
                                ],
                        ],
                    2 =>
                        [
                            0 =>
                                [
                                    'name' => 'type',
                                ],
                            1 =>
                                [
                                    'name' => 'status',
                                ],
                        ],
                    3 =>
                        [
                            0 =>
                                [
                                    'name' => 'product_category',
                                ],
                            1 =>
                                [
                                    'name' => 'found_in_release',
                                ],
                        ],
                    4 =>
                        [
                            0 =>
                                [
                                    'name' => 'name',
                                    'displayParams' => ['required' => true],
                                ],
                        ],
                    5 =>
                        [
                            0 =>
                                [
                                    'name' => 'description',
                                ],
                        ],
                ],
            ],
        ],
    ],
];
