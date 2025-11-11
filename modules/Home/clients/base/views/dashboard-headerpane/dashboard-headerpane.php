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
$viewdefs['Home']['base']['view']['dashboard-headerpane'] = [
    'buttons' => [
        [
            'name' => 'create_cancel_button',
            'type' => 'button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link mx-2',
            'showOn' => 'create',
        ],
        [
            'name' => 'create_button',
            'type' => 'button',
            'events' => [
                'click' => 'button:save_button:click',
            ],
            'label' => 'LBL_SAVE_BUTTON_LABEL',
            'css_class' => 'btn-primary',
            'showOn' => 'create',
        ],
        [
            'name' => 'cancel_dashboard_filters',
            'type' => 'button',
            'events' => [
                'click' => 'button:cancel_dashboard_filters:click',
            ],
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link !hidden',
        ],
        [
            'name' => 'save_dashboard_filters',
            'type' => 'button',
            'events' => [
                'click' => 'button:save_dashboard_filters:click',
            ],
            'label' => 'LBL_SAVE_BUTTON_LABEL',
            'css_class' => 'btn-primary disabled !hidden',
        ],
    ],
    'panels' => [
        [
            'name' => 'header',
            'fields' => [
                [
                    'type' => 'dashboardtitle',
                    'name' => 'name',
                    'placeholder' => 'LBL_DASHBOARD_TITLE',
                ],
                [
                    'name' => 'my_favorite',
                    'label' => 'LBL_FAVORITE',
                    'type' => 'favorite',
                    'dismiss_label' => true,
                ],
            ],
        ],
    ],
];
