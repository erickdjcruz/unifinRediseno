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
$module_name = 'Calendar';

$viewdefs[$module_name]['base']['view']['record'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_RECORD_HEADER_TAB',
            'header' => true,
            'fields' => [
                [
                    'name' => 'picture',
                    'type' => 'avatar',
                    'width' => 42,
                    'height' => 42,
                    'dismiss_label' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'name',
                    'related_fields' => [
                        'options',
                    ],
                ],
                [
                    'name' => 'favorite',
                    'label' => 'LBL_FAVORITE',
                    'type' => 'favorite',
                    'readonly' => true,
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'follow',
                    'label' => 'LBL_FOLLOW',
                    'type' => 'follow',
                    'readonly' => true,
                    'dismiss_label' => true,
                ],
            ],
        ],
        [
            'name' => 'primary_tab',
            'label' => 'LBL_RECORD_SETTINGS_TAB',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'newTab' => true,
            'panelDefault' => 'expanded',
            'fields' => [
                [
                    'name' => 'calendar_module',
                    'label' => 'LBL_CALENDAR_MODULE',
                ],
                [
                    'name' => 'subject',
                    'label' => 'LBL_SUBJECT',
                ],
                [
                    'name' => 'color',
                    'label' => 'LBL_COLOR',
                ],
                [
                    'name' => 'event_start',
                    'label' => 'LBL_EVENT_START',
                    'help' => 'LBL_START_DATE_INFO',
                ],
                [
                    'name' => 'event_end',
                    'label' => 'LBL_EVENT_END',
                    'help' => 'LBL_END_DATE_INFO',
                ],
                [
                    'name' => 'duration_minutes',
                    'label' => 'LBL_DURATION_MINUTES',
                    'help' => 'LBL_DURATION_MINUTES_INFO',
                ],
                [
                    'name' => 'duration_hours',
                    'label' => 'LBL_DURATION_HOURS',
                    'help' => 'LBL_DURATION_HOURS_INFO',
                ],
                [
                    'name' => 'duration_days',
                    'label' => 'LBL_DURATION_DAYS',
                    'help' => 'LBL_DURATION_DAYS_INFO',
                ],
                [
                    'name' => 'dblclick_event',
                    'label' => 'LBL_DBLCLICK_EVENT',
                ],
                [
                    'name' => 'allow_create',
                    'label' => 'LBL_ALLOW_CREATE',
                ],
                [
                    'name' => 'allow_update',
                    'label' => 'LBL_ALLOW_UPDATE',
                ],
                [
                    'name' => 'allow_delete',
                    'label' => 'LBL_ALLOW_DELETE',
                ],
                [
                    'name' => 'calendar_filter',
                    'label' => 'LBL_CALENDAR_FILTER',
                    'span' => 12,
                ],
            ],
        ],
        [
            'name' => 'templates_tab',
            'label' => 'LBL_RECORD_TEMPLATES_TAB',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'newTab' => true,
            'panelDefault' => 'expanded',
            'fields' => [
                [
                    'name' => 'event_tooltip_template',
                    'label' => 'LBL_TOOLTIP_EVENT_TEMPLATE',
                    'span' => 12,
                ],
                [
                    'name' => 'day_event_template',
                    'label' => 'LBL_DAY_EVENT_TEMPLATE',
                    'span' => 12,
                ],
                [
                    'name' => 'week_event_template',
                    'label' => 'LBL_WEEK_EVENT_TEMPLATE',
                    'span' => 12,
                ],
                [
                    'name' => 'month_event_template',
                    'label' => 'LBL_MONTH_EVENT_TEMPLATE',
                    'span' => 12,
                ],
                [
                    'name' => 'agenda_event_template',
                    'label' => 'LBL_AGENDA_EVENT_TEMPLATE',
                    'span' => 12,
                ],
                [
                    'name' => 'timeline_event_template',
                    'label' => 'LBL_TIMELINE_EVENT_TEMPLATE',
                    'span' => 12,
                ],
                [
                    'name' => 'schedulermonth_event_template',
                    'label' => 'LBL_SCHEDULERMONTH_EVENT_TEMPLATE',
                    'span' => 12,
                ],
                [
                    'name' => 'ical_event_template',
                    'label' => 'LBL_ICAL_EVENT_TEMPLATE',
                    'span' => 12,
                ],
            ],
        ],
        [
            'name' => 'other_tab',
            'label' => 'LBL_RECORD_OTHER_TAB',
            'hide' => true,
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'newTab' => true,
            'panelDefault' => 'expanded',
            'fields' => [
                [
                    'name' => 'description',
                    'span' => 12,
                ],
                'assigned_user_name',
                'team_name',
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_MODIFIED',
                    'fields' => [
                        [
                            'name' => 'date_modified',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'modified_by_name',
                        ],
                    ],
                ],
                [
                    'name' => 'date_entered_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_ENTERED',
                    'fields' => [
                        [
                            'name' => 'date_entered',
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'created_by_name',
                        ],
                    ],
                ],
                [
                    'name' => 'commentlog',
                    'displayParams' => [
                        'type' => 'commentlog',
                        'fields' => [
                            'entry',
                            'date_entered',
                            'created_by_name',
                        ],
                        'max_num' => 100,
                    ],
                    'studio' => [
                        'listview' => false,
                        'recordview' => true,
                    ],
                    'label' => 'LBL_COMMENTLOG',
                ],
                [],
            ],
        ],
    ],
    'templateMeta' => [
        'useTabs' => true,
    ],
];
