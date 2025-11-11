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

class SugarUpgradeAddMentionCommentLogEmailTemplate extends UpgradeScript
{
    public $order = 9020;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if (version_compare($this->from_version, '10.1.0', '<')) {
            // prepare the template
            $team = BeanFactory::getBean('Teams');
            $teamId = $team->retrieve_team_id('Administrator');

            global $mod_strings;

            $emailTemp = new EmailTemplate();
            $emailTemp->name = $mod_strings['comment_log_mention_email']['name'];
            $emailTemp->description = $mod_strings['comment_log_mention_email']['description'];
            $emailTemp->subject = $mod_strings['comment_log_mention_email']['subject'];
            $emailTemp->body = $mod_strings['comment_log_mention_email']['txt_body'];
            $emailTemp->body_html = $mod_strings['comment_log_mention_email']['body'];
            $emailTemp->deleted = 0;

            $emailTemp->team_id = $teamId;
            $emailTemp->published = 'off';
            $emailTemp->type = 'system';
            $emailTemp->text_only = 1;
            $id = $emailTemp->save();
            $this->upgrader->config['emailTemplate']['CommentLogMention'] = $id;
        }
    }
}
