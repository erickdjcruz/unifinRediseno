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
$viewdefs['Users']['QuickCreate'] = [
    'templateMeta' => ['maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'form' => [
            'headerTpl' => 'modules/Users/tpls/EditViewHeader.tpl',
            'footerTpl' => 'modules/Users/tpls/EditViewFooter.tpl',
        ],
        'javascript' => '<script type="text/javascript" src="modules/Users/UserEditView.js"></script>',
    ],
    'panels' => [
        'LBL_USER_INFORMATION' => [
            [
                [
                    'name' => 'user_name',
                    'displayParams' => ['required' => true],
                ],
                'first_name',
            ],
            [
                [
                    'name' => 'status',
                    'customCode' => '{if $IS_ADMIN}@@FIELD@@{else}{$STATUS_READONLY}{/if}',
                    'displayParams' => ['required' => true],
                ],
                [
                    'name' => 'last_name',
                    'displayParams' => ['required' => true],
                ],
            ],
            [
                [
                    'name' => 'email1',
                    'displayParams' => ['required' => true],
                ],
                [
                    'name' => 'UserType',
                    'customCode' =>
                        '{if $IS_ADMIN && !$IDM_MODE_ENABLED}{$USER_TYPE_DROPDOWN}{else}{$USER_TYPE_READONLY}{/if}',
                ],
            ],
        ],
        'LBL_EMPLOYEE_INFORMATION' => [
            [
                [
                    'name' => 'employee_status',
                    'customCode' => '{if $IS_ADMIN}@@FIELD@@{else}{$EMPLOYEE_STATUS_READONLY}{/if}',
                ],
                'show_on_employees',
            ],
            [
                [
                    'name' => 'title',
                    'customCode' => '{if $IS_ADMIN}@@FIELD@@{else}{$TITLE_READONLY}{/if}',
                ],
                'phone_work',
            ],
            [
                [
                    'name' => 'department',
                    'customCode' => '{if $IS_ADMIN}@@FIELD@@{else}{$DEPT_READONLY}{/if}',
                ],
                [
                    'name' => 'reports_to_name',
                    'customCode' => '{if $IS_ADMIN}@@FIELD@@{else}{$REPORTS_TO_READONLY}{/if}',
                ],
            ],
        ],
    ],
];
