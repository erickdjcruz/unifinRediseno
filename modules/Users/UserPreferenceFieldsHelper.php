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

/**
 * Helper class for loading and saving data of User Preference proxy fields in
 * the Users module
 */
class UserPreferenceFieldsHelper
{
    /**
     * Returns the value stored for the given user preference field
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @param string $fieldName the name of the user preference field
     * @return mixed the value of the user preference, or null if it is not set
     */
    public function getPreferenceField(SugarBean $user, string $fieldName)
    {
        $getMethod = "get_$fieldName";
        if (method_exists($this, $getMethod)) {
            return $this->$getMethod($user);
        }
        return $user->getPreference($fieldName);
    }

    /**
     * Updates the value stored for the given user preference field
     *
     * @param SugarBean $user the User whose preferences are being updated
     * @param string $fieldName the name of the user preference field
     * @param mixed $value the new value for the user preference field
     */
    public function setPreferenceField(SugarBean $user, string $fieldName, $value)
    {
        $setMethod = "set_$fieldName";
        if (method_exists($this, $setMethod)) {
            return $this->$setMethod($user, $value);
        }
        $user->setPreference($fieldName, $value);
    }

    /**
     * Retrieves the email_link_type preference. Forces 'mailto' if the system
     * mailer is not configured
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the email_link_type preference value
     */
    protected function get_email_link_type(SugarBean $user)
    {
        if (empty($user->id)) {
            return 'mailto';
        }

        $mailerPreferenceStatus = OutboundEmailConfigurationPeer::getMailConfigurationStatusForUser($user);
        if ($mailerPreferenceStatus === OutboundEmailConfigurationPeer::STATUS_INVALID_SYSTEM_CONFIG) {
            return 'mailto';
        }
        return $user->getPreference('email_link_type');
    }

    /**
     * Retrieves the default_export_charset preference via locale
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the default_export_charset preference value
     */
    protected function get_default_export_charset(SugarBean $user)
    {
        global $locale;
        return $locale->getExportCharset('', $user);
    }

    /**
     * Retrieves the use_real_names preference and converts it from "on"/"off"
     * to a boolean value
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return bool the use_real_names preference value
     */
    protected function get_use_real_names(SugarBean $user)
    {
        return isTruthy($user->getPreference('use_real_names'));
    }

    /**
     * Converts the given value to "on" or "off" and sets it as the User's
     * use_real_names preference
     *
     * @param SugarBean $user the User whose preferences are being updated
     * @param mixed $value the new value for the preference
     */
    protected function set_use_real_names(SugarBean $user, $value)
    {
        $value = $value ? 'on' : 'off';
        $user->setPreference('use_real_names', $value);
    }

    /**
     * Retrieves the appearance preference
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the appearance preference value or the default if not set
     */
    protected function get_appearance(SugarBean $user)
    {
        return $user->getUserPrefAppearanceDefault();
    }

    /**
     * Retrieves the site URL
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the site URL
     */
    protected function get_site_url(SugarBean $user)
    {
        global $sugar_config;
        return $sugar_config['site_url'];
    }

    /**
     * Retrieves the current configuration of the User's module tab lists
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return array[] the User's displayed/hidden/removed module tab settings
     */
    protected function get_user_tabs(SugarBean $user)
    {
        $tabController = new TabController();
        $tabLists = $tabController->get_tabs($user);

        foreach ($tabLists as &$tabList) {
            // Remove the 'Home' module from any of the lists. It will be added
            // back at the top of the display list on save
            if (isset($tabList['Home'])) {
                unset($tabList['Home']);
            }
        }

        return [
            'display' => array_keys($tabLists[0] ?? []),
            'hide' => array_keys($tabLists[1] ?? []),
            'remove' => array_keys($tabLists[2] ?? []),
        ];
    }

    /**
     * Sets the user's new tab configuration in user preferences
     *
     * @param SugarBean $user the User whose preferences are being updated
     * @param mixed $value the new value for the preference
     */
    protected function set_user_tabs(SugarBean $user, $value)
    {
        global $current_user;

        // Regular users cannot set this preference if the admin has locked the
        // tab list
        $tabController = new TabController();
        if (!($current_user->isAdminForModule('Users') || $tabController->get_users_can_edit())) {
            return;
        }

        $oldTabLists = $tabController->get_tabs($user);

        if (isset($value['display'])) {
            $newDisplay = $value['display'];
            $oldDisplay = array_keys($oldTabLists[0]);

            // Re-add the 'Home' module as it should always be the top module
            // in the displayed list
            array_unshift($newDisplay, 'Home');
            if (array_values($newDisplay) !== array_values($oldDisplay)) {
                $tabController->set_user_tabs($newDisplay, $user, 'display');
            }
        }

        $tabController->set_user_tabs($value['hide'] ?? [], $user, 'hide');

        if (is_admin($current_user)) {
            $tabController->set_user_tabs($value['remove'] ?? [], $user, 'remove');
        }
    }

    /**
     * Retrieves the number_pinned_modules preference, or one of its fallback
     * values
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the number_pinned_modules preference value
     */
    protected function get_number_pinned_modules(SugarBean $user)
    {
        global $sugar_config;
        $numberPinned = $sugar_config['maxPinnedModules'] ?? $sugar_config['default_max_pinned_modules'];

        $tabController = new TabController();
        if ($tabController->get_users_pinned_modules()) {
            $numberPinned = $user->getPreference('number_pinned_modules') ?? $numberPinned;
        }

        return $numberPinned;
    }

    /**
     * Updates the number_pinned_modules preference for the given User only
     * if admins have allowed it to be individually set
     *
     * @param SugarBean $user the User whose preferences are being updated
     * @param mixed $value the new value for the preference
     */
    protected function set_number_pinned_modules(SugarBean $user, $value)
    {
        $tabs = new TabController();
        if ($tabs->get_users_pinned_modules() && $value >= 1 && $value <= 100) {
            $user->setPreference('number_pinned_modules', $value);
        }
    }

    /**
     * Retrieves the field_name_placement preference, or one of its fallback
     * values
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the field_name_placement preference value
     */
    protected function get_field_name_placement(SugarBean $user)
    {
        return $user->getPreference('field_name_placement') ?? 'field_on_side';
    }

    /**
     * Retrieves the datef preference, or one of its fallback
     * values
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the datef preference value
     */
    protected function get_datef(SugarBean $user)
    {
        global $locale;
        return $locale->getPrecedentPreference($user->id ? 'datef' : 'default_date_format', $user);
    }

    /**
     * Retrieves the timef preference, or one of its fallback
     * values
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the timef preference value
     */
    protected function get_timef(SugarBean $user)
    {
        global $locale;
        return $locale->getPrecedentPreference($user->id ? 'timef' : 'default_time_format', $user);
    }

    /**
     * Retrieves the default_locale_name_format preference, or one of its
     * fallback values
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the default_locale_name_format preference value
     */
    protected function get_default_locale_name_format(SugarBean $user)
    {
        global $locale;
        return $locale->getPrecedentPreference('default_locale_name_format', $user);
    }

    /**
     * Retrieves the default_currency_significant_digits preference, or one of
     * its fallback values
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the default_currency_significant_digits preference value
     */
    protected function get_default_currency_significant_digits(SugarBean $user)
    {
        global $locale;
        return $locale->getPrecedentPreference('default_currency_significant_digits', $user);
    }

    /**
     * Retrieves the num_grp_sep preference, or one of its fallback values
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the num_grp_sep preference value
     */
    protected function get_num_grp_sep(SugarBean $user)
    {
        global $sugar_config;
        return $user->getPreference('num_grp_sep') ?? $sugar_config['default_number_grouping_seperator'];
    }

    /**
     * Retrieves the dec_sep preference, or one of its fallback values
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the dec_sep preference value
     */
    protected function get_dec_sep(SugarBean $user)
    {
        global $sugar_config;
        return $user->getPreference('dec_sep') ?? $sugar_config['default_decimal_seperator'];
    }

    /**
     * Retrieves the currency preference, or one of its fallbacks
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the currency preference value
     */
    protected function get_currency(SugarBean $user)
    {
        global $locale;
        global $sugar_config;

        $currencyId = $locale->getPrecedentPreference('currency', $user);
        if ($currencyId === $sugar_config['default_currency_name']) {
            return '-99';
        }
        return $currencyId;
    }

    /**
     * Retrieves the currency_show_preferred preference, or one of its
     * fallbacks
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the currency_show_preferred preference value
     */
    protected function get_currency_show_preferred(SugarBean $user)
    {
        global $locale;
        return $locale->getPrecedentPreference('currency_show_preferred', $user);
    }

    /**
     * Retrieves the currency_create_in_preferred preference, or one of its
     * fallbacks
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the currency_create_in_preferred preference value
     */
    protected function get_currency_create_in_preferred(SugarBean $user)
    {
        global $locale;
        return $locale->getPrecedentPreference('currency_create_in_preferred', $user);
    }

    /**
     * Retrieves the sugarpdf_pdf_font_name_main preference, or one of its
     * fallbacks
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the sugarpdf_pdf_font_name_main preference value
     */
    protected function get_sugarpdf_pdf_font_name_main(SugarBean $user)
    {
        include_once 'include/Sugarpdf/sugarpdf_config.php';
        return PDF_FONT_NAME_MAIN;
    }

    /**
     * Retrieves the sugarpdf_pdf_font_size_main preference, or one of its
     * fallbacks
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the sugarpdf_pdf_font_size_main preference value
     */
    protected function get_sugarpdf_pdf_font_size_main(SugarBean $user)
    {
        include_once 'include/Sugarpdf/sugarpdf_config.php';
        return PDF_FONT_SIZE_MAIN;
    }

    /**
     * Retrieves the sugarpdf_pdf_font_name_data preference, or one of its
     * fallbacks
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the sugarpdf_pdf_font_name_data preference value
     */
    protected function get_sugarpdf_pdf_font_name_data(SugarBean $user)
    {
        include_once 'include/Sugarpdf/sugarpdf_config.php';
        return PDF_FONT_NAME_DATA;
    }

    /**
     * Retrieves the sugarpdf_pdf_font_size_data preference, or one of its
     * fallbacks
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the sugarpdf_pdf_font_size_data preference value
     */
    protected function get_sugarpdf_pdf_font_size_data(SugarBean $user)
    {
        include_once 'include/Sugarpdf/sugarpdf_config.php';
        return PDF_FONT_SIZE_DATA;
    }

    /**
     * Updates the calendar_publish_key preference, removing any tags from
     * the key
     *
     * @param SugarBean $user the User whose preferences are being updated
     * @param mixed $value the new value for the preference
     */
    protected function set_calendar_publish_key(SugarBean $user, $value)
    {
        $user->setPreference('calendar_publish_key', SugarCleaner::stripTags($value, false));
    }

    /**
     * Retrieves the calendar_publish_location, building it from the
     * site_url, calendar_publish_key preference, and user properties
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the calendar_publish_location value
     */
    protected function get_calendar_publish_location(SugarBean $user)
    {
        global $sugar_config;

        $publishKey = $user->getPreference('calendar_publish_key');
        $publishUrl = $sugar_config['site_url'] . '/vcal_server.php';
        $token = '/';

        // Determine if the web server is running IIS. If so then change the publish url
        if (isset($_SERVER) && !empty($_SERVER['SERVER_SOFTWARE'])) {
            if (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'iis') !== false) {
                $token = '?parms=';
            }
        }

        $publishUrl .= $token . "type=vfb&source=outlook&key=$publishKey";
        if (!empty($user->email1)) {
            $publishUrl .= '&email=' . $user->email1;
        } else {
            $publishUrl .= '&user_name=' . $user->user_name;
        }

        return $publishUrl;
    }

    /**
     * Retrieves the calendar_search_location, building it from the
     * site_url and calendar_publish_key preference
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the calendar_search_location value
     */
    protected function get_calendar_search_location(SugarBean $user)
    {
        global $sugar_config;
        $publishKey = $user->getPreference('calendar_publish_key');

        return $sugar_config['site_url'] . "/vcal_server.php/type=vfb&key=$publishKey&email=%NAME%@%SERVER%";
    }

    /**
     * Retrieves the calendar_ical_subscription_url, building it from the
     * site_url and calendar_publish_key preference
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return string the calendar_search_location value
     */
    protected function get_calendar_ical_subscription_url(SugarBean $user)
    {
        global $sugar_config;
        $publishKey = $user->getPreference('calendar_publish_key');

        return $sugar_config['site_url'] . "/ical_server.php?type=ics&key=$publishKey&user_id=" . $user->id;
    }

    /**
     * Retrieves the fdow preference, or its fallback value
     *
     * @param SugarBean $user the User whose preferences are being fetched
     * @return int the fdow preference value
     */
    protected function get_fdow(SugarBean $user)
    {
        return $user->getPreference('fdow') ?? 0;
    }
}
