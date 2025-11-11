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
$viewdefs['Employees']['QuickCreate'] = [
    'templateMeta' => ['maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'form' => [
            'headerTpl' => 'modules/Users/tpls/EditViewHeader.tpl',
            'footerTpl' => 'modules/Users/tpls/EditViewFooter.tpl',
        ],
    ],
    'panels' => [
        'LBL_EMPLOYEE_INFORMATION' => [
            [
                [
                    'name' => 'employee_status',
                    'customCode' => '{if $EDIT_REPORTS_TO || $IS_ADMIN}@@FIELD@@{else}{$EMPLOYEE_STATUS_READONLY}{/if}',
                ],
                [
                    'name' => 'title',
                    'customCode' => '{if  $EDIT_REPORTS_TO || $IS_ADMIN}@@FIELD@@{else}{$TITLE_READONLY}{/if}',
                ],
            ],
            [
                'first_name',
                [
                    'name' => 'last_name',
                    'displayParams' => ['required' => true],
                ],
            ],
            [
                [
                    'name' => 'department',
                    'customCode' => '{if  $EDIT_REPORTS_TO || $IS_ADMIN}@@FIELD@@{else}{$DEPT_READONLY}{/if}',
                ],
                'phone_work',
            ],
            [
                [
                    'name' => 'reports_to_name',
                    'customCode' => '{if  $EDIT_REPORTS_TO || $IS_ADMIN}@@FIELD@@{else}{$REPORTS_TO_READONLY}{/if}',
                ],
                [
                    'name' => 'email1',
                    'displayParams' => ['required' => false],
                ],
            ],
        ],
    ],
];
