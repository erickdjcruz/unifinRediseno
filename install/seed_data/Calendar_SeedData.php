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

$calendars = [];
$calendarsData[] = [
    'name' => 'Calls',
    'calendar_module' => 'Calls',
    'subject' => 'name',
    'color' => '#c0edff',
    'event_start' => 'date_start',
    'duration_minutes' => 'duration_minutes',
    'duration_hours' => 'duration_hours',
    'allow_create' => true,
    'allow_update' => true,
    'allow_delete' => true,
    'dblclick_event' => 'detail:self:id',
    'calendar_type' => '^main^',
    'calendar_filter' => '{"filterId":"calls_attending","filterName":"My Calls as Guest","filterDef":[{"$guest":""}]}',
    'event_tooltip_template' => '<p>{::description::}</p>',
    'day_event_template' => '<p><strong>{::name::}</strong></p><p>{::description::}</p>',
    'week_event_template' => '<p><strong>{::name::}</strong></p><p>{::description::}</p>',
    'month_event_template' => '<p>{::name::}</p>',
    'agenda_event_template' => '<p><strong>{::name::}</strong></p><p>{::description::}</p>',
    'timeline_event_template' => '<p>{::name::} {::description::}</p>',
    'schedulermonth_event_template' => '<p>{::name::} {::description::}</p>',
    'ical_event_template' => '<p>{::name::} {::description::}</p>',
    'assigned_user_id' => '1',
    'team_id' => '1',
];
$calendarsData[] = [
    'name' => 'Meetings',
    'calendar_module' => 'Meetings',
    'subject' => 'name',
    'color' => '#c6ddff',
    'event_start' => 'date_start',
    'duration_minutes' => 'duration_minutes',
    'duration_hours' => 'duration_hours',
    'allow_create' => true,
    'allow_update' => true,
    'allow_delete' => true,
    'dblclick_event' => 'detail:self:id',
    'calendar_type' => '^main^',
    'calendar_filter' =>
        '{"filterId":"meetings_attending","filterName":"My Meetings as Guest","filterDef":[{"$guest":""}]}',
    'event_tooltip_template' => '<p>{::description::}</p>',
    'day_event_template' => '<p><strong>{::name::}</strong></p><p>{::description::}</p>',
    'week_event_template' => '<p><strong>{::name::}</strong></p><p>{::description::}</p>',
    'month_event_template' => '<p>{::name::}</p>',
    'agenda_event_template' => '<p><strong>{::name::}</strong></p><p>{::description::}</p>',
    'timeline_event_template' => '<p>{::name::} {::description::}</p>',
    'schedulermonth_event_template' => '<p>{::name::} {::description::}</p>',
    'ical_event_template' => '<p>{::name::} {::description::}</p>',
    'assigned_user_id' => '1',
    'team_id' => '1',
];
$calendarsData[] = [
    'name' => 'Tasks',
    'calendar_module' => 'Tasks',
    'subject' => 'name',
    'color' => '#e2d4fd',
    'event_start' => 'date_start',
    'event_end' => 'date_due',
    'allow_create' => true,
    'allow_update' => true,
    'allow_delete' => true,
    'dblclick_event' => 'detail:self:id',
    'calendar_type' => '^main^',
    'calendar_filter' => '{"filterId":"assigned_to_me","filterName":"My Tasks","filterDef":{"$owner":""}}',
    'event_tooltip_template' => '<p>{::description::}</p>',
    'day_event_template' => '<p><strong>{::name::}</strong></p><p>{::description::}</p>',
    'week_event_template' => '<p><strong>{::name::}</strong></p><p>{::description::}</p>',
    'month_event_template' => '<p>{::name::}</p>',
    'agenda_event_template' => '<p><strong>{::name::}</strong></p><p>{::description::}</p>',
    'timeline_event_template' => '<p>{::name::} {::description::}</p>',
    'schedulermonth_event_template' => '<p>{::name::} {::description::}</p>',
    'ical_event_template' => '<p>{::name::} {::description::}</p>',
    'assigned_user_id' => '1',
    'team_id' => '1',
];

foreach ($calendarsData as $i => $seedCal) {
    $calendar = BeanFactory::newBean('Calendar');
    $calendar->name = $seedCal['name'];
    $calendar->calendar_module = $seedCal['calendar_module'];
    $calendar->subject = $seedCal['subject'];
    $calendar->color = $seedCal['color'];
    $calendar->event_start = $seedCal['event_start'];
    if (isset($seedCal['event_end'])) {
        $calendar->event_end = $seedCal['event_end'];
    }
    if (isset($seedCal['duration_minutes'])) {
        $calendar->duration_minutes = $seedCal['duration_minutes'];
    }
    if (isset($seedCal['duration_hours'])) {
        $calendar->duration_hours = $seedCal['duration_hours'];
    }
    $calendar->allow_create = $seedCal['allow_create'];
    $calendar->allow_update = $seedCal['allow_update'];
    $calendar->allow_delete = $seedCal['allow_delete'];
    $calendar->dblclick_event = $seedCal['dblclick_event'];
    $calendar->calendar_type = $seedCal['calendar_type'];
    $calendar->calendar_filter = $seedCal['calendar_filter'];
    $calendar->event_tooltip_template = $seedCal['event_tooltip_template'];
    $calendar->day_event_template = $seedCal['day_event_template'];
    $calendar->week_event_template = $seedCal['week_event_template'];
    $calendar->month_event_template = $seedCal['month_event_template'];
    $calendar->agenda_event_template = $seedCal['agenda_event_template'];
    $calendar->timeline_event_template = $seedCal['timeline_event_template'];
    $calendar->schedulermonth_event_template = $seedCal['schedulermonth_event_template'];
    $calendar->ical_event_template = $seedCal['ical_event_template'];
    $calendar->assigned_user_id = $seedCal['assigned_user_id'];
    $calendar->team_id = $seedCal['team_id'];
    $calendar->save();
}
