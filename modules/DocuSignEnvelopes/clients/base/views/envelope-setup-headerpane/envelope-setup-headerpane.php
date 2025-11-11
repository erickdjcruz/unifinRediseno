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
$viewdefs['DocuSignEnvelopes']['base']['view']['envelope-setup-headerpane'] = [
    'template' => 'headerpane',
    'fields' => [
        [
            'name' => 'title',
            'type' => 'label',
            'default_value' => 'Envelope Setup',
        ],
    ],
    'buttons' => [
        [
            'name' => 'close',
            'type' => 'button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'events' => [
                'click' => 'setup:closedrawer:fire',
            ],
            'css_class' => 'btn-invisible btn-link ml-2',
        ],
        [
            'name' => 'back_button',
            'type' => 'button',
            'label' => 'LBL_BACK_BUTTON_LABEL',
            'events' => [
                'click' => 'setup:back:fire',
            ],
            'css_class' => 'btn-primary ml-2',
        ],
        [
            'name' => 'send_button',
            'type' => 'button',
            'label' => 'LBL_BUTTON_SEND',
            'events' => [
                'click' => 'setup:send:fire',
            ],
            'css_class' => 'btn-primary ml-2',
        ],
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
];
