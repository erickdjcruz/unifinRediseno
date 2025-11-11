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
$viewdefs['Metrics']['base']['view']['record-header-buttons'] = [
    'fields' => [
        [
            'name' => 'name',
            'label' => 'LBL_METRIC_NAME',
            'type' => 'name',
        ],
    ],
    'buttons' => [
        [
            'name' => 'cancel_button',
            'type' => 'button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link hide',
        ],
        [
            'type' => 'rowaction',
            'css_class' => 'btn btn-primary hide',
            'primary' => true,
            'name' => 'save_button',
            'label' => 'LBL_SAVE_BUTTON_LABEL',
        ],
        [
            'type' => 'rowaction',
            'css_class' => 'btn btn-primary hide',
            'name' => 'edit_button',
            'label' => 'LBL_EDIT_BUTTON_LABEL',
        ],
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
];
