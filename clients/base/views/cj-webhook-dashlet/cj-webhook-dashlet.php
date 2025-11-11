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

$viewdefs['base']['view']['cj-webhook-dashlet'] = [
    'dashlets' => [
        [
            'label' => 'LBL_DEFAULT_TEST_WEB_HOOK_DASHLET_TITLE',
            'description' => 'LBL_DEFAULT_TEST_WEB_HOOK_DASHLET_DESC',
            'config' => [],
            'preview' => [],
            'filter' => [
                'module' => [
                    'CJ_WebHooks',
                ],
                'view' => 'record',
            ],
        ],
    ],
    'custom_toolbar' => [
        'buttons' => [
            [
                'type' => 'dashletaction',
                'css_class' => 'btn btn-invisible sendRequest',
                'icon' => 'sicon-launch',
                'action' => 'sendClicked',
                'tooltip' => 'Send Test Request',
            ],
            [
                'dropdown_buttons' => [
                    [
                        'type' => 'dashletaction',
                        'action' => 'editClicked',
                        'label' => 'LBL_DASHLET_CONFIG_EDIT_LABEL',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'refreshClicked',
                        'label' => 'LBL_DASHLET_REFRESH_LABEL',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'removeClicked',
                        'label' => 'LBL_DASHLET_REMOVE_LABEL',
                    ],
                    [
                        'type' => 'dashletaction',
                        'action' => 'sendClicked',
                        'label' => 'LBL_DASHLET_SEND_LABEL',
                        'event' => 'send',
                    ],
                ],
            ],
        ],
    ],
];
