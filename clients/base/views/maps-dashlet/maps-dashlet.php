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

$viewdefs['base']['view']['maps-dashlet'] = [
    'dashlets' => [
        [
            'label' => 'LBL_MAPS_DASHLET',
            'description' => 'LBL_MAPS_DASHLET_DESCRIPTION',
            'config' => [
            ],
            'filter' => [
                'view' => ['record', 'records'],
                'licenseType' => ['MAPS'],
                'blacklist' => [
                    'module' => [
                        'Home',
                    ],
                ],
            ],
        ],
    ],
    'panels' => [
        [
            'name' => 'dashlet_settings',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'maps_display_type',
                    'label' => 'LBL_MAP_DASHLET_CONFIGURE_MAP_TYPE',
                    'type' => 'enum',
                    'options' => 'maps_display_type_list',
                    'span' => 6,
                ],
                [
                    'name' => 'maps_display_zoom',
                    'label' => 'LBL_MAP_DASHLET_CONFIGURE_MAP_ZOOM',
                    'type' => 'enum',
                    'options' => 'maps_display_zoom_list',
                    'span' => 6,
                ],
            ],
        ],
    ],
];
