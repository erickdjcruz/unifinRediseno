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


$viewdefs ['Accounts'] =
    [
        'QuickCreate' => [
            'templateMeta' => [
                'form' => [
                    'buttons' => [
                        'SAVE',
                        'CANCEL',
                    ],
                ],
                'maxColumns' => '2',
                'widths' => [
                    [
                        'label' => '10',
                        'field' => '30',
                    ],
                    [
                        'label' => '10',
                        'field' => '30',
                    ],
                ],
                'includes' => [
                    [
                        'file' => 'modules/Accounts/Account.js',
                    ],
                ],
            ],
            'panels' => [
                'default' => [
                    [
                        [
                            'name' => 'name',
                            'displayParams' => [
                                'required' => true,
                            ],
                        ],
                    ],
                    [
                        [
                            'name' => 'website',
                        ],
                        [
                            'name' => 'phone_office',
                        ],
                    ],
                    [
                        [
                            'name' => 'email1',
                        ],
                        [
                            'name' => 'phone_fax',
                        ],
                    ],
                    [
                        [
                            'name' => 'industry',
                        ],
                        [
                            'name' => 'account_type',
                        ],
                    ],
                    [
                        [
                            'name' => 'assigned_user_name',
                        ],
                        [
                            'name' => 'team_name',
                        ],
                    ],
                ],
            ],
        ],
    ];
