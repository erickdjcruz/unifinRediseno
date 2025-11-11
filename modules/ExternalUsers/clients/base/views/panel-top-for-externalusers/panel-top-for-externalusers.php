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

$viewdefs['ExternalUsers']['base']['view']['panel-top-for-externalusers'] = [
    'type' => 'panel-top',
    'template' => 'panel-top',
    'buttons' => [
        [
            'type' => 'actiondropdown',
            'name' => 'panel_dropdown',
            'css_class' => 'pull-right',
            'buttons' => [
                [
                    'type' => 'sticky-rowaction',
                    'icon' => 'sicon-plus',
                    'name' => 'create_button',
                    'dismiss_label' => true,
                    'label' => '',
                    'css_class' => 'disabled',
                    'acl_action' => 'create',
                    'tooltip' => 'LBL_CREATE_BUTTON_LABEL',
                ],
                [
                    'type' => 'link-action',
                    'name' => 'select_button',
                    'label' => 'LBL_ASSOC_RELATED_RECORD',
                ],
            ],
        ],
    ],
];
