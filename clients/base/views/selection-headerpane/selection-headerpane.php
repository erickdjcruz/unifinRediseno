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

$viewdefs['base']['view']['selection-headerpane'] = [
    'template' => 'headerpane',
    'fields' => [
        [
            'name' => 'title',
            'type' => 'label',
            'default_value' => 'LBL_SEARCH_AND_SELECT',
        ],
        [
            'name' => 'collection-count',
            'type' => 'collection-count',
        ],
    ],
    'buttons' => [
        [
            'name' => 'close',
            'type' => 'button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'events' => [
                'click' => 'selection:closedrawer:fire',
            ],
            'css_class' => 'btn-invisible btn-link',
        ],
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'primary' => true,
            'buttons' => [
                [
                    'name' => 'link_button',
                    'type' => 'link-button',
                    'label' => 'LBL_ADD_BUTTON',
                    'events' => [
                        'click' => 'selection:link:fire',
                    ],
                ],
                [
                    'name' => 'create_button',
                    'type' => 'rowaction',
                    'label' => 'LBL_CREATE_BUTTON_LABEL',
                    'acl_action' => 'create',
                ],
                [
                    'name' => 'select_button',
                    'type' => 'button',
                    'label' => 'LBL_SELECT_BUTTON_LABEL',
                    'events' => [
                        'click' => 'selection:select:fire',
                    ],
                ],
            ],
        ],
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
];
