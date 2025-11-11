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

namespace Sugarcrm\Sugarcrm\Hint;

use Sugarcrm\Sugarcrm\Hint\Queue\EventTypes;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\InstanceInitCompletedEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\InstanceInitCloneCompletedEvent;
use Sugarcrm\Sugarcrm\Hint\Queue\Event\InstanceResyncCompletedEvent;

class HintConstants
{
    public const HINT_CONFIG_GEO = 'HintConfigGeo';
    public const HINT_CONFIG_NOTIFICATION = 'HintConfigNotification';
    public const HINT_CONFIG_HARD_UNINSTALL = 'HintConfigHardUninstall';

    public const HINT_CONFIG_MAP = [
        self::HINT_CONFIG_GEO => [
            'name' => 'HintGeo',
            'platform' => 'base',
            'module' => 'HintGeoConfig',
        ],

        self::HINT_CONFIG_NOTIFICATION => [
            'name' => 'HintNotification',
            'platform' => 'base',
            'module' => 'HintNotificationConfig',
        ],

        self::HINT_CONFIG_HARD_UNINSTALL => [
            'name' => 'HintHardUninstall',
            'platform' => 'base',
            'module' => 'HintHardUninstallConfig',
        ],
    ];

    // Metadata for Hint field View defs
    public const DEFAULT_HINT_ACCOUNTS_BASIC_PANEL = [
        'hint_account_size', 'hint_account_industry', 'hint_account_location', 'annual_revenue', 'description',
    ];

    public const DEFAULT_HINT_ACCOUNTS_EXPANDED_PANEL = [
        'hint_account_naics_code_lbl', 'sic_code', 'hint_account_fiscal_year_end', 'hint_account_founded_year',
        'hint_account_facebook_handle', 'twitter', 'hint_account_industry_tags',
    ];

    public const DEFAULT_HINT_CONTACTS_BASIC_PANEL = [
        'phone_work', 'phone_mobile', 'phone_other', 'email',
    ];

    public const DEFAULT_HINT_CONTACTS_EXPANDED_PANEL = [
        'hint_education', 'hint_education', 'hint_job_2', 'hint_facebook', 'hint_twitter',
    ];

    public const DEFAULT_HINT_LEADS_BASIC_PANEL = [
        'phone_work', 'phone_mobile', 'phone_other', 'email',
    ];

    public const DEFAULT_HINT_LEADS_EXPANDED_PANEL = [
        'hint_education', 'hint_education', 'hint_job_2', 'hint_facebook', 'hint_twitter',
    ];

    public const DEFAULT_ACCOUNTS_FIELDS = [
        'twitter', 'sic_code', 'description', 'annual_revenue', 'hint_account_size',
        'hint_account_industry', 'hint_account_location', 'hint_account_founded_year', 'hint_account_industry_tags',
        'hint_account_naics_code_lbl', 'hint_account_sic_code_label', 'hint_account_facebook_handle',
        'hint_account_fiscal_year_end', 'hint_account_annual_revenue',
    ];

    public const DEFAULT_CONTACTS_FIELDS = [
        'title', 'phone_work', 'phone_mobile', 'hint_photo', 'phone_other', 'hint_job_2', 'hint_twitter',
        'hint_facebook', 'hint_education', 'hint_account_size', 'hint_industry_tags', 'hint_account_website',
        'hint_account_industry', 'hint_account_location', 'hint_account_description', 'hint_account_founded_year',
        'hint_account_annual_revenue', 'hint_account_naics_code_lbl', 'hint_account_sic_code_label',
        'hint_account_twitter_handle', 'hint_account_facebook_handle', 'hint_account_fiscal_year_end',
        'hint_education_2',
    ];

    public const DEFAULT_LEADS_FIELDS = [
        'title', 'account_name', 'phone_work', 'hint_photo', 'phone_mobile', 'phone_other', 'hint_job_2',
        'hint_twitter', 'hint_facebook', 'hint_education', 'hint_account_size', 'hint_industry_tags',
        'hint_account_website', 'hint_account_industry', 'hint_account_location', 'hint_account_description',
        'hint_account_founded_year', 'hint_account_annual_revenue', 'hint_account_naics_code_lbl',
        'hint_account_sic_code_label', 'hint_account_twitter_handle', 'hint_account_facebook_handle',
        'hint_account_fiscal_year_end', 'hint_education_2',
    ];

    /**
     * Hint config
     *
     * @return array
     */
    public static function hintConfig()
    {
        return include 'src/Hint/Stage2/Stage2ClientBuildConfig.php';
    }

    /**
     * Return corresponding completion event based on the eventType.
     */
    public static function getCompletionEvent($eventType)
    {
        switch ($eventType) {
            case EventTypes::INSTANCE_INIT_COMPLETED:
                return new InstanceInitCompletedEvent();
            case EventTypes::INSTANCE_INIT_CLONE_COMPLETED:
                return new InstanceInitCloneCompletedEvent();
            case EventTypes::INSTANCE_RESYNC_COMPLETED:
                return new InstanceResyncCompletedEvent();
        }
    }
}
