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

$viewdefs['Forecasts']['base']['view']['commitment-headerpane'] = [
    'buttons' => [
        [
            'name' => 'cancel_button',
            'events' => [
                'click' => 'button:cancel_button:click',
            ],
            'type' => 'button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
            'acl_action' => 'current_user',
        ],
        [
            'name' => 'save_draft_button',
            'events' => [
                'click' => 'button:save_draft_button:click',
            ],
            'tooltip' => 'LBL_SAVE_TOOLTIP',
            'type' => 'button',
            'label' => 'LBL_SAVE_DRAFT',
            'css_class' => 'btn-secondary save-draft-button',
            'acl_action' => 'current_user',
        ],
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'primary' => true,
            'buttons' => [
                [
                    'name' => 'commit_button',
                    'type' => 'button',
                    'label' => 'LBL_QC_COMMIT_BUTTON',
                    'events' => [
                        'click' => 'button:commit_button:click',
                    ],
                    'tooltip' => 'LBL_COMMIT_TOOLTIP_REP',
                    'css_class' => 'btn-primary disabled commit-button',
                    'acl_action' => 'current_user',
                    'primary' => true,
                ],
                [
                    'name' => 'assign_quota',
                    'type' => 'assignquota',
                    'label' => 'LBL_ASSIGN_QUOTA_BUTTON',
                    'events' => [
                        'click' => 'button:assign_quota:click',
                    ],
                    'acl_action' => 'manager_current_user',
                ],
                [
                    'name' => 'export_button',
                    'type' => 'rowaction',
                    'label' => 'LBL_EXPORT_CSV',
                    'event' => 'button:export_button:click',
                    'acl_action' => 'manager',
                ],
            ],
        ],
    ],
];
