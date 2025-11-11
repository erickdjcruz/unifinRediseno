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
$viewdefs['base']['view']['dashlet-toolbar'] = [
    'buttons' => [
        [
            'type' => 'dashletaction',
            'css_class' => 'btn btn-invisible dashlet-toggle minify',
            'icon' => 'sicon-chevron-up',
            'action' => 'toggleMinify',
            'tooltip' => 'LBL_DASHLET_TOGGLE',
            'disallowed_layouts' => [
                [
                    'name' => 'omnichannel-dashboard',
                ], [
                    'name' => 'portal-preview',
                ],
            ],
        ],
        [
            'dropdown_buttons' => [
                [
                    'type' => 'dashletaction',
                    'action' => 'editClicked',
                    'label' => 'LBL_DASHLET_CONFIG_EDIT_LABEL',
                    'name' => 'edit_button',
                ],
                [
                    'type' => 'dashletaction',
                    'action' => 'refreshClicked',
                    'label' => 'LBL_DASHLET_REFRESH_LABEL',
                    'name' => 'refresh_button',
                ],
                [
                    'type' => 'dashletaction',
                    'action' => 'removeClicked',
                    'label' => 'LBL_DASHLET_REMOVE_LABEL',
                    'name' => 'remove_button',
                    'disallowed_layouts' => [
                        [
                            'name' => 'omnichannel-dashboard',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
