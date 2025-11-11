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
$viewdefs['base']['view']['side-drawer-header'] = [
    'buttons' => [
        [
            'type' => 'actiondropdown',
            'buttons' => [
                [
                    'name' => 'edit_button',
                    'type' => 'rowaction',
                    'label' => 'LBL_EDIT_BUTTON',
                    'acl_action' => 'edit',
                ],
            ],
            'showOn' => 'view',
        ], [
            'name' => 'cancel_button',
            'type' => 'button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
            'showOn' => 'edit',
        ], [
            'name' => 'save_button',
            'type' => 'button',
            'events' => [
                'click' => 'button:save_button:click',
            ],
            'label' => 'LBL_SAVE_BUTTON_LABEL',
            'css_class' => 'btn-primary',
            'showOn' => 'edit',
        ],
    ],
];
