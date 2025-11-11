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
$viewdefs['base']['view']['history-summary-headerpane'] = [
    'template' => 'headerpane',
    'fields' => [
        [
            'name' => 'picture',
            'type' => 'avatar',
            'size' => 'large',
            'dismiss_label' => true,
            'readonly' => true,
        ],
        [
            'name' => 'title',
            'type' => 'label',
            'default_value' => 'LBL_HISTORICAL_SUMMARY',
        ],
    ],
    'buttons' => [
        [
            'name' => 'cancel_button',
            'type' => 'button',
            'label' => 'LBL_CLOSE_BUTTON_LABEL',
            'css_class' => 'btn-primary',
        ],
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
];
