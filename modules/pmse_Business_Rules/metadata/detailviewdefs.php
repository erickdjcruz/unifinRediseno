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

$module_name = 'pmse_Business_Rules';
$viewdefs[$module_name] =
    [
        'DetailView' => [
            'templateMeta' => [
                'form' => [
                    'buttons' => [
                        0 => 'EDIT',
                        1 => 'DUPLICATE',
                        2 => 'DELETE',
                        3 => 'FIND_DUPLICATES',
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
                'default' => [
                    0 =>
                        [
                            0 => 'name',
                            1 => 'assigned_user_name',
                        ],
                    1 =>
                        [
                        ],
                    2 =>
                        [
                            0 =>
                                [
                                    'name' => 'date_entered',
                                    'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
                                    'label' => 'LBL_DATE_ENTERED',
                                ],
                            1 =>
                                [
                                    'name' => 'date_modified',
                                    'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
                                    'label' => 'LBL_DATE_MODIFIED',
                                ],
                        ],
                    3 =>
                        [
                            0 => 'description',
                        ],
                ],
            ],
        ],
    ];
