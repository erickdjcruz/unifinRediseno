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

$module_name = '<module_name>';
$viewdefs[$module_name]['base']['view']['activity-timeline'] = [
    'dashlets' => [
        [
            'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
            'description' => 'LBL_ACTIVITY_TIMELINE_DASHLET_DESCRIPTION',
            'config' => ['module' => $module_name],
            'preview' => ['module' => $module_name],
            'filter' => [
                'view' => 'record',
                'module' => [
                    $module_name,
                ],
            ],
        ],
    ],
    'custom_toolbar' => [
        'buttons' => [
            [
                'type' => 'dashletaction',
                'css_class' => 'btn btn-invisible',
                'icon' => 'sicon-refresh',
                'action' => 'reloadData',
                'tooltip' => 'LBL_DASHLET_REFRESH_LABEL',
            ],
            [
                'type' => 'dashletaction',
                'css_class' => 'dashlet-toggle btn btn-invisible minify',
                'icon' => 'sicon-chevron-up',
                'action' => 'toggleMinify',
                'tooltip' => 'LBL_DASHLET_MINIMIZE',
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
                        'action' => 'removeClicked',
                        'label' => 'LBL_DASHLET_REMOVE_LABEL',
                        'name' => 'remove_button',
                    ],
                ],
            ],
        ],
    ],
];
