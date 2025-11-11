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
$viewdefs['Calendar']['base']['layout']['main-scheduler'] = [
    'css_class' => 'calendar-main-scheduler',
    'components' => [
        [
            'view' => [
                'type' => 'main-panel',
                'name' => 'main-panel',
                'css_class' => 'calendar-main-panel',
                'fields' => [
                    [
                        'name' => 'myCalendars',
                        'label' => 'LBL_CALENDAR_MY_CALENDARS',
                        'type' => 'calendars',
                        'view' => 'edit',
                        'view_source' => 'main-panel',
                        'calendar_type' => 'main',
                        'calendar_filter' => 'my_calendars',
                        'css_class' => 'calendar',
                    ],
                    [
                        'name' => 'otherCalendars',
                        'label' => 'LBL_CALENDAR_OTHER_CALENDARS',
                        'type' => 'calendars',
                        'view' => 'edit',
                        'view_source' => 'main-panel',
                        'calendar_type' => 'main',
                        'calendar_filter' => 'other_calendars',
                        'css_class' => 'calendar',
                    ],
                ],
            ],
        ],
        [
            'view' => [
                'type' => 'scheduler',
                'name' => 'scheduler',
                'css_class' => 'scheduler-component',
            ],
        ],
        [
            'layout' => [
                'type' => 'tabbed-layout',
                'css_class' => 'side sidebar-content preview-pane span4 hide',
                'name' => 'preview-pane',
                'notabs' => true,
                'components' => [
                    [
                        'layout' => 'preview',
                        'xmeta' => [
                            'editable' => true,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
