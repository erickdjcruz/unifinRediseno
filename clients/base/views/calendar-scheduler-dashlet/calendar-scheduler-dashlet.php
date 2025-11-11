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

$viewdefs['base']['view']['calendar-scheduler-dashlet'] = [
    'dashlets' => [
        [
            'label' => 'LBL_CALENDAR_DASHLET_NAME',
            'description' => 'Calendar',
            'config' => [],
            'preview' => [],
            'filter' => [
                'blacklist' => [
                    'view' => [
                        'portaltheme-config',
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
                    'name' => 'availableViews',
                    'label' => 'LBL_CALENDAR_AVAILABLE_VIEWS',
                    'type' => 'enum',
                    'isMultiSelect' => true,
                    'span' => 8,
                    'sort_alpha' => true,
                    'options' => 'calendar_views_options',
                    'required' => true,
                ],
                [
                    'name' => 'defaultView',
                    'label' => 'LBL_CALENDAR_DEFAULT_VIEW',
                    'type' => 'enum',
                    'span' => 8,
                    'required' => true,
                    'sort_alpha' => true,
                    'options' => [],
                ],
                [
                    'name' => 'myCalendars',
                    'label' => 'LBL_CALENDAR_MY_CALENDARS',
                    'type' => 'calendars',
                    'isMultiSelect' => true,
                    'css_class' => 'calendar',
                    'span' => 8,
                    'calendar_filter' => 'my_calendars',
                    'view_source' => 'calendar-scheduler-dashlet',

                ],
                [
                    'name' => 'otherCalendars',
                    'label' => 'LBL_CALENDAR_OTHER_CALENDARS',
                    'type' => 'calendars',
                    'isMultiSelect' => true,
                    'css_class' => 'calendar',
                    'span' => 8,
                    'calendar_filter' => 'other_calendars',
                    'view_source' => 'calendar-scheduler-dashlet',
                ],
            ],
        ],
    ],
];
