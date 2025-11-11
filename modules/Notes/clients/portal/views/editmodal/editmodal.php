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
$viewdefs['Notes']['portal']['view']['editmodal'] = [
    'buttons' => [
        [
            'name' => 'cancel_button',
            'type' => 'button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'value' => 'cancel',
            'css_class' => 'btn-invisible btn-link',
        ],
        [
            'name' => 'save_button',
            'type' => 'button',
            'label' => 'LBL_SAVE_BUTTON_LABEL',
            'value' => 'save',
            'css_class' => 'btn-primary',
        ],
    ],
    'panels' => [
        [
            'label' => 'LBL_EDIT_BUTTON_LABEL',
            'fields' => [
                0 =>
                    [
                        'name' => 'name',
                        'default' => true,
                        'enabled' => true,
                        'width' => 35,
                        'required' => true,
                    ],
                1 =>
                    [
                        'name' => 'description',
                        'default' => true,
                        'enabled' => true,
                        'width' => 35,
                        'required' => true,
                        'rows' => 5,
                    ],
                2 =>
                    [
                        'name' => 'filename',
                        'default' => true,
                        'enabled' => true,
                        'sorting' => true,
                        'width' => 35,
                    ],
            ],
        ],
    ],
];
