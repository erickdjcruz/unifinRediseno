<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

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
require 'config.php';
global $sugar_config;
global $mod_strings;

$templateData = [
    'Meeting' => 'meeting_notification_email',
    'Call' => 'call_notification_email',
    '‌ReportSchedule' => 'scheduled_report_email',
    'CommentLogMention' => 'comment_log_mention_email',
];

$team = new Team();
$teamId = $team->retrieve_team_id('Administrator');

foreach ($templateData as $templateKey => $templateValue) {
    $emailTemp = new EmailTemplate();
    $emailTemp->name = $mod_strings[$templateValue]['name'];
    $emailTemp->description = $mod_strings[$templateValue]['description'];
    $emailTemp->subject = $mod_strings[$templateValue]['subject'];
    $emailTemp->body = $mod_strings[$templateValue]['txt_body'];
    $emailTemp->body_html = $mod_strings[$templateValue]['body'];
    $emailTemp->deleted = 0;

    $emailTemp->team_id = $teamId;
    $emailTemp->published = 'off';
    $emailTemp->type = 'system';
    $emailTemp->text_only = 1;

    if ($templateKey === '‌ReportSchedule') {
        $emailTemp->text_only = 0;
    }

    $id = $emailTemp->save();
    $sugar_config['emailTemplate'][$templateKey] = $id;
}

write_array_to_file('sugar_config', $sugar_config, 'config.php');
