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

$viewdefs['base']['view']['maps-widget-list'] = [
    'template' => 'flex-list',
    'sticky_resizable_columns' => true,
    'favorite' => true,
    'selection' => [
        'type' => 'multi',
        'actions' => [
            [
                'name' => 'drawdirections_from_user_button',
                'type' => 'button',
                'label' => 'LBL_MAP_DIRECTIONS_FROM_USER',
                'primary' => true,
                'events' => [
                    'click' => 'subpanel:massdirectionsfromuserdraw:fire',
                ],
            ],
            [
                'name' => 'drawdirections_from_record_button',
                'type' => 'button',
                'label' => 'LBL_MAP_DIRECTIONS_FROM_RECORD',
                'events' => [
                    'click' => 'subpanel:massdirectionsfromrecorddraw:fire',
                ],
            ],
            [
                'name' => 'drawmap_button',
                'type' => 'button',
                'label' => 'LBL_MAP_MAP',
                'events' => [
                    'click' => 'subpanel:massmapdraw:fire',
                ],
            ],
        ],
    ],
    'rowactions' => [
        'actions' => [
            [
                'type' => 'rowaction',
                'css_class' => 'btn',
                'tooltip' => 'LBL_PREVIEW',
                'event' => 'list:preview:fire',
                'icon' => 'sicon-preview',
                'acl_action' => 'view',
            ],
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_body',
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'maps_distance',
                    'label' => 'LBL_MAPS_DISTANCE',
                    'type' => 'text',
                ],
                [
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'type' => 'name',
                    'enabled' => true,
                    'default' => true,
                    'link' => true,
                ],
                [
                    'name' => 'maps_addressLine',
                    'label' => 'LBL_MAP_ADDRESS',
                    'type' => 'text',
                ],
                [
                    'name' => 'maps_locality',
                    'label' => 'LBL_CITY',
                    'type' => 'text',
                ],
            ],
        ],
    ],
    'last_state' => [
        'id' => 'maps-subpanel-list',
    ],
];
