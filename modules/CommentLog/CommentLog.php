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

use Sugarcrm\Sugarcrm\Security\Validator\Constraints\Guid;
use Sugarcrm\Sugarcrm\Security\Validator\Validator;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;

/**
 * The SugarBean for Each commentlog message, should be immutable.
 */
class CommentLog extends Basic
{
    public $module_dir = 'CommentLog';
    public $object_name = 'CommentLog';
    public $module_name = 'CommentLog';
    public $table_name = 'commentlog';
    public $new_schema = true;
    public $entry;

    /**
     * The join table used to get the parent record for an entry
     * @var string
     */
    protected $joinTable = 'commentlog_rel';

    /**
     * The column in the join table to match the ID of this entry to in order to
     * find the parent record of this entry
     * @var string
     */
    protected $joinKey = 'commentlog_id';

    /**
     * The list of fields to select when getting the parent record
     * @var array
     */
    protected $parentFields = [
        [
            'field' => 'record_id',
            'alias' => 'record',
        ],
        [
            'field' => 'module',
        ],
    ];

    /**
     * @inheritDoc
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }

        return false;
    }

    /**
     * Sets the entry of this commentlog message. Shall only be called while creating
     * new commentlog message, not for editing
     * @param string $entry The entry of this commentlog message
     * @modifies $this->entry
     * @effects Sets $this->entry to processed $entry
     */
    public function setEntry(string $entry)
    {
        $this->entry = $entry;
    }

    /**
     * Sets the module of this commentlog message. Shall only be called while creating
     * new commentlog message, not for editing
     * @param string $module The module this commentlog is associated to,
     *                       has to be an existing module
     * @modifies $this->module
     * @effects Set $this->module to $module
     * @return true When $module exists and added to $this->module successfully
     *         Otherwise false.
     */
    public function setModule(string $module)
    {
        if (!is_string(BeanFactory::getBeanClass($module))) {
            return false;
        }

        $this->module = $module;

        return true;
    }

    /**
     * Gets all the commentlog for every record id given
     *
     * @param $focus
     * @param $ids array of record ids
     * @return array
     */
    public function getRelatedModuleRecords($focus, $ids)
    {
        // No ids means nothing to do
        // Not use this in CommentLog module, use only for other modules
        if (empty($ids) || ($focus == null) || ($focus->table_name === 'commentlog')) {
            return [];
        }

        $query = new SugarQuery($this->db);
        $query->from($focus);
        $query->join('commentlog_link');
        $query->select()->fieldRaw('commentlog_id');
        $query->where()->in('record_id', $ids);
        $results = $query->execute();

        $returnArray = [];
        foreach ($results as $result) {
            $returnArray[] = $result['commentlog_id'];
        }

        return $returnArray;
    }

    /**
     * Gets fields for selection from the join table to get a parent record
     * @return array
     */
    private function getParentSelectFields()
    {
        // Build a select field list
        $fields = [];
        foreach ($this->parentFields as $field) {
            $add = $field['field'];
            if (isset($field['alias'])) {
                $add .= ' ' . $field['alias'];
            }

            $fields[] = $add;
        }

        return $fields;
    }

    /**
     * Verifies that the necessary elements of the parent data array are found
     * in an array
     * @param array $row A row of data as an array, typically from a DB result
     * @return boolean
     */
    private function verifyParentData(array $row)
    {
        // If the result to verify is not an array then return false immediately
        if (!is_array($row)) {
            return false;
        }

        // Now loop over the parent fields and if any of them are not in the result
        // return false
        foreach ($this->parentFields as $field) {
            $verify = $field['alias'] ?? $field['field'];
            if (!isset($row[$verify])) {
                return false;
            }
        }

        // Return true as a default after passing through everything else
        return true;
    }

    /**
     * Retrieves the record id and module of the commentlog
     * @return array The id and module of the parent record if connecting parent
     *               record exists, otherwise empty array
     */
    public function getParentRecord()
    {
        $qry = $this->db->getConnection()->createQueryBuilder();
        $qry->select($this->getParentSelectFields())
            ->from($this->joinTable)
            ->where('deleted = 0')
            ->andWhere(
                $qry->expr()->eq(
                    $this->joinKey,
                    $qry->createPositionalParameter($this->id)
                )
            );

        $row = $qry->execute()->fetchAssociative();
        return $this->verifyParentData($row) ? $row : [];
    }

    /**
     * @inheritDoc
     * We also want to create Sugar notifications for any user that was mentioned
     */
    public function save($check_notify = false)
    {
        $id = parent::save($check_notify);
        $this->createNotifications();
        return $id;
    }

    /**
     * Create Sugar notifications for each user mentioned in the comment
     */
    public function createNotifications()
    {
        $pattern = '/@\[([\w]+):([\d\w\-]+)\]/';
        $new_rel_relname = $this->new_rel_relname;
        $recordId = $this->new_rel_id;
        $matches = [];
        // We don't have the parent record here so no point in notifying the pinged user
        if (!$new_rel_relname || !$this->load_relationship($new_rel_relname)) {
            return;
        }

        $mentionedUsers = [];
        $module = $this->$new_rel_relname->getRelatedModuleName();
        $defaultLang = $this->getSugarConfigValue('default_language');
        preg_match_all($pattern, $this->entry, $matches, PREG_SET_ORDER);
        foreach ($matches as $mentionTag) {
            if ($mentionTag[1] === 'Users' || $mentionTag[1] === 'Employees') {
                $userId = $mentionTag[2];
                if (!$this->validateGuid($userId)) {
                    continue;
                }
                if (in_array($mentionTag[2], $mentionedUsers)) {
                    continue;
                }
                $notification = $this->getNewBean('Notifications');
                $user = $this->getBean('Users', $userId);
                $userLanguage = !empty($user->preferred_language) ? $user->preferred_language : $defaultLang;

                // we need to create a notification in the mentioned user's language
                $modStrings = $this->getModStrings($userLanguage, 'Notifications');
                $appListStrings = $this->getAppListStrings($userLanguage);

                $singularModuleName = $appListStrings['moduleListSingular'][$module];
                $notification->name = $singularModuleName . ': ' . $modStrings['LBL_YOU_HAVE_BEEN_MENTIONED'];
                $notification->description = 'LBL_YOU_HAVE_BEEN_MENTIONED_BY';
                $notification->parent_id = $recordId;
                $notification->parent_type = $module;
                $notification->assigned_user_id = $userId;
                $notification->severity = 'information';
                $notification->is_read = 0;
                $notification->save();

                // send email notification on mention if user preference is checked
                if ($user->getPreference('send_email_on_mention') == 'on') {
                    $this->sendNotificationsEmail($user, $module, $recordId, $singularModuleName);
                }

                // Send push notification on mention if user preference is enabled
                if ($user->canReceivePushNotifications('mobile_notification_on_mention')) {
                    $this->sendPushNotification($user, $module, $recordId, $singularModuleName);
                }

                $mentionedUsers[] = $userId;
            }
        }
    }

    /**
     * Wrapper for BeanFactory::newBean
     *
     * @param string $module The module name
     * @return SugarBean|null
     */
    public function getNewBean(string $module): SugarBean
    {
        return BeanFactory::newBean($module);
    }

    /**
     * Wrapper for BeanFactory::getBean
     * @param string $module The module name
     * @param string $id The record id
     * @param array $params
     * @return SugarBean|null
     * @throws Exception
     */
    public function getBean(string $module, string $id, $params = []): SugarBean
    {
        return BeanFactory::getBean($module, $id, $params);
    }

    /**
     * Validate we have a guid
     *
     * @param string $guid
     * @return bool
     */
    public function validateGuid(string $guid): bool
    {
        $constraint = new Guid();
        $violations = Validator::getService()->validate($guid, $constraint);
        return !(safeCount($violations) > 0);
    }

    /**
     * Retrieves sugar config values
     *
     * @param string $value The value to get
     * @return mixed
     */
    public function getSugarConfigValue(string $value)
    {
        $config = Container::getInstance()->get(SugarConfig::class);
        return $config->get($value);
    }

    /**
     * Wrapper for return_module_language
     *
     * @param string $language What language to get
     * @param string $module What module to get
     * @return array The translated module strings for the specified module and language
     */
    public function getModStrings(string $language, string $module): array
    {
        return return_module_language($language, $module);
    }

    /**
     * Wrapper for return_app_list_strings_language
     *
     * @param string $language What language to get
     * @return array The translated app list strings for the specified language
     */
    public function getAppListStrings(string $language): array
    {
        return return_app_list_strings_language($language);
    }


    /**
     * Builds the text that is sent with the push notification
     * @param $singularModuleName
     * @param $relatedBean
     * @param $currentUser
     * @param $receivingUser
     * @return array
     */
    public function getMentionPushNotificationText($singularModuleName, $relatedBean, $currentUser, $receivingUser)
    {
        $userLanguage = $receivingUser->getUserLanguageWithFallback();
        $modStrings = $this->getModStrings($userLanguage, 'PushNotifications');
        $msg = $modStrings['LBL_USER_MENTIONED'];
        $msg = str_replace('{{mentioning_user}}', $currentUser->full_name, $msg);
        $msg = str_replace('{{module_name_singular}}', $singularModuleName, $msg);
        $msg = str_replace('{{record_name}}', $relatedBean->name, $msg);

        $title = $modStrings['LBL_USER_MENTIONED_TITLE'];
        $title = str_replace('{{module_name_singular}}', $singularModuleName, $title);

        return [
            'title' => $title,
            'description' => $msg,
        ];
    }

    /**
     * Helper to create a new push notification
     * @return SugarBean|null
     */
    public function createPushNotification()
    {
        return BeanFactory::newBean('PushNotifications');
    }

    /**
     * Sends a push notification when a user is mentioned
     * @param $user
     * @param $moduleName
     * @param $recordId
     * @param $singularModuleName
     * @return SugarBean
     */
    public function sendPushNotification($user, $moduleName, $recordId, $singularModuleName)
    {
        $relatedBean = $this->getBean($moduleName, $recordId);
        $currentUser = $this->getCurrentUser();
        $pushText = $this->getMentionPushNotificationText($singularModuleName, $relatedBean, $currentUser, $user);

        $push = $this->createPushNotification();
        $push->notification_type = 'user_mentioned';
        $push->assigned_user_id = $user->id;
        $push->parent_type = $moduleName;
        $push->parent_id = $recordId;
        $push->name = $pushText['title'];
        $push->description = $pushText['description'];
        $push->extra_data = json_encode([
            'data' => [
                'mentioned_by_id' => $currentUser->id,
                'mentioned_by_name' => $currentUser->full_name,
                'record_name' => $relatedBean->name,
            ],
        ]);
        $push->is_sent = $push->send();

        $push->save();
        return $push;
    }


    /**
     * Send an email to the assigned user of the job
     *
     * @param SugarBean $user mentioned Bean
     * @param String $moduleName
     * @param String $recordId
     * @param String $singularModuleName
     */
    public function sendNotificationsEmail($user, $moduleName, $recordId, $singularModuleName)
    {
        $currentUser = $this->getCurrentUser();
        $initiatorName = $currentUser->full_name;
        $mailTransmissionProtocol = 'unknown';
        $record = $this->getBean($moduleName, $recordId);
        $recordName = $record->name;

        try {
            $mailer = $this->getSystemMailer();
            $mailTransmissionProtocol = $mailer->getMailTransmissionProtocol();

            // Get the comment log notification email template
            $settings = $this->getSugarConfigValue('emailTemplate');
            $tplID = $settings['CommentLogMention'];

            $emailTemplate = $this->getBean(
                'EmailTemplates',
                $tplID,
                ['disable_row_level_security' => true]
            );

            if (empty($emailTemplate->id)) {
                \LoggerManager::getLogger()->fatal('Error sending email notification: No email template found');
                return false;
            }

            // Place the site URL into the template
            $recordUrl = $this->getRecordUrl($moduleName, $recordId);
            $emailTemplate->subject = str_replace('$initiator_full_name', $initiatorName, $emailTemplate->subject);
            $emailTemplate->subject = str_replace(
                '$singular_module_name',
                strtolower($singularModuleName),
                $emailTemplate->subject
            );
            $emailTemplate->body_html = str_replace('$record_name', $recordName, $emailTemplate->body_html);
            $emailTemplate->body_html = str_replace('$record_url', $recordUrl, $emailTemplate->body_html);

            $emailTemplate->body = str_replace('$record_name', $recordName, $emailTemplate->body);
            $emailTemplate->body = str_replace('$record_url', $recordUrl, $emailTemplate->body);

            // add the recipient...
            $mailer->addRecipientsTo(new EmailIdentity($user->email1, $user->full_name));
            // set the subject
            $mailer->setSubject($emailTemplate->subject);
            $mailer->setTextBody($emailTemplate->body);
            // set html content of the email
            if (!isTruthy($emailTemplate->text_only)) {
                $mailer->setHtmlBody($emailTemplate->body_html);
            }

            $mailer->send();
        } catch (MailerException $me) {
            $message = $me->getMessage();
            $GLOBALS['log']->warn(
                "Notifications: error sending e-mail (method: {$mailTransmissionProtocol}), (error: {$message})"
            );
        }
    }

    /**
     * Returns the record url
     * @param $moduleName
     * @param $recordId
     * @return string
     */
    public function getRecordUrl($moduleName, $recordId)
    {
        return prependSiteURL('#' . buildSidecarRoute($moduleName, $recordId));
    }

    /**
     * Wrapper to get system default mailer
     * @return mixed
     */
    public function getSystemMailer()
    {
        return MailerFactory::getSystemDefaultMailer();
    }

    /**
     * Wrapper to get current user
     * @return null|SugarBean
     */
    public function getCurrentUser()
    {
        global $current_user;
        return $current_user;
    }
}
