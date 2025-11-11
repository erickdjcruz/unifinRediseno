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

$module_name = 'pmse_Business_Rules';
$viewdefs[$module_name]['base']['view']['massupdate'] = [
    'buttons' => [
        [
            'name' => 'update_button',
            'type' => 'button',
            'label' => 'Update',
            'acl_action' => 'massupdate',
            'css_class' => 'btn-primary',
            'primary' => true,
        ],
        [
            'type' => 'button',
            'value' => 'cancel',
            'css_class' => 'btn-invisible cancel_button',
            'icon' => 'sicon-close',
            'primary' => false,
        ],
    ],
    'panels' => [
        [
            'fields' => [
            ],
        ],
    ],
];
