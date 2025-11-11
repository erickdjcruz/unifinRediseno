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

namespace Sugarcrm\Sugarcrm\Security\Password;

class Utilities
{
    protected static $validTemplateTypes = [
        'lostpasswordtmpl',
        'resetpasswordtmpl',
    ];

    protected static $templateData = [
        'props' => [
            'name' => 'name',
            'description' => 'description',
            'subject' => 'subject',
            'body' => 'txt_body',
            'body_html' => 'body',
        ],
        'lostpasswordtmpl' => [
            'key' => 'portal_forgot_password_email_link',
            'id' => 'f29f5864-bc90-11e9-82c9-a45e60e684a5',
        ],
        'resetpasswordtmpl' => [
            'key' => 'portal_password_reset_confirmation_email',
            'id' => '12d9843e-bed9-11e9-8cec-6003089fe26e',
        ],
    ];

    /**
     * Inserts values into db when a reset pwd link is created
     * for regular user and portal users
     * @param array $values
     * @return bool
     */
    public static function insertIntoUserPwdLink(array $values)
    {
        // we don't want to insert into the db if any of these fields are empty
        $requiredParams = [
            'guid',
            'bean_id',
            'name',
        ];
        foreach ($requiredParams as $param) {
            if (empty($values[$param])) {
                \LoggerManager::getLogger()->fatal('Could not insert into `users_password_link` because' . $param .
                    ' is empty.');
                return false;
            }
        }

        if (empty($values['platform'])) {
            $values['platform'] = 'base';
        }

        if (empty($values['bean_type'])) {
            $values['bean_type'] = 'Users';
        }

        $db = \DBManagerFactory::getInstance();
        $query = sprintf(
            'INSERT INTO users_password_link (id, bean_id, bean_type, username, date_generated, platform)
        VALUES(%s, %s, %s, %s, %s, %s) ',
            $db->quoted($values['guid']),
            $db->quoted($values['bean_id']),
            $db->quoted($values['bean_type']),
            $db->quoted($values['name']),
            $db->quoted(\TimeDate::getInstance()->nowDb()),
            $db->quoted($values['platform'])
        );

        return $db->query($query);
    }

    /**
     * Creates an Email Template for Portal Password Reset Email
     * @param string $teamId the Team ID for this template
     * @param array $mod_strings Module strings for use in setting values of the template
     * @param string $type the type of password email template to create
     * @return string|null
     */
    public static function addPortalPasswordSeedData(string $teamId, array $mod_strings, string $type): ?string
    {
        if (!in_array($type, self::$validTemplateTypes)) {
            return null;
        }

        // Grab the details/settings for the given template type
        $props = self::$templateData['props'];
        $key = self::$templateData[$type]['key'];

        // Set properties common to all Portal password email templates
        $template = \BeanFactory::newBean('EmailTemplates');
        $template->team_id = $teamId;
        $template->published = 'off';
        $template->type = 'system';
        $template->text_only = 1;
        $template->new_with_id = true;

        // Set properties specific to the given Portal password email template
        $template->id = self::$templateData[$type]['id'];
        foreach ($props as $beanField => $langField) {
            $template->$beanField = $mod_strings[$key][$langField];
        }

        $ETbean = \BeanFactory::retrieveBean('EmailTemplates', $template->id);
        if (empty($ETbean) || empty($ETbean->id)) {
            return $template->save();
        }
        return $template->id;
    }
}
