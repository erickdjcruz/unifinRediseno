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

require_once 'modules/Users/password_utils.php';

use Sugarcrm\Sugarcrm\Entitlements\Subscription;
use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Sugarcrm\IdentityProvider\Srn;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config;
use Sugarcrm\Sugarcrm\SugarCloud\Discovery;
use Sugarcrm\Sugarcrm\SugarCloud\UserApi;
use Sugarcrm\Sugarcrm\FeatureToggle\FeatureFlag;
use Sugarcrm\Sugarcrm\FeatureToggle\Features\UserDownloadsHideOpiWpiPlugins;

class CurrentUserApi extends SugarApi
{
    /**
     * Hash of user preference indexes and their corresponding metadata index
     * name. This is used in both the user pref setting in this class and in
     * user preference setting in BWC mode. The list of preference indexes will
     * be used by the BWC implementation to determine whether the state of the
     * user has changed so as to notify clients that they need to rerequest user
     * data.
     *
     * @var array
     */
    protected $userPrefMeta = [
        'timezone' => 'timezone',
        'datef' => 'datepref',
        'timef' => 'timepref',
        'currency' => 'currency',
        'signature_default' => 'signature_default',
        'email_link_type' => 'email_link_type',
        'default_locale_name_format' => 'default_locale_name_format',
        'fdow' => 'first_day_of_week',
        'sweetspot' => 'sweetspot',
        'shortcuts' => 'shortcuts',
        'reminder_time' => 'reminder_time',
        'email_reminder_time' => 'email_reminder_time',
        'field_name_placement' => 'field_name_placement',
        'send_email_on_mention' => 'send_email_on_mention',
        'mobile_notification_on_assignment' => 'mobile_notification_on_assignment',
        'mobile_notification_on_mention' => 'mobile_notification_on_mention',
        'appearance' => 'appearance',
        'number_pinned_modules' => 'number_pinned_modules',
    ];

    public const TYPE_ADMIN = 'admin';
    public const TYPE_USER = 'user';

    public function registerApiRest()
    {
        return [
            'retrieve' => [
                'reqType' => 'GET',
                'path' => ['me',],
                'pathVars' => [],
                'method' => 'retrieveCurrentUser',
                'shortHelp' => 'Returns current user',
                'longHelp' => 'include/api/help/me_get_help.html',
                'ignoreMetaHash' => true,
                'ignoreSystemStatusError' => true,
                'noEtag' => true,
            ],
            'update' => [
                'reqType' => 'PUT',
                'path' => ['me',],
                'pathVars' => [],
                'method' => 'updateCurrentUser',
                'shortHelp' => 'Updates current user',
                'longHelp' => 'include/api/help/me_put_help.html',
                'ignoreMetaHash' => true,
                'ignoreSystemStatusError' => true,
            ],
            'updatePassword' => [
                'reqType' => 'PUT',
                'path' => ['me', 'password'],
                'pathVars' => [''],
                'method' => 'updatePassword',
                'shortHelp' => "Updates current user's password",
                'longHelp' => 'include/api/help/me_password_put_help.html',
                'ignoreSystemStatusError' => true,
            ],
            'verifyPassword' => [
                'reqType' => 'POST',
                'path' => ['me', 'password'],
                'pathVars' => [''],
                'method' => 'verifyPassword',
                'shortHelp' => "Verifies current user's password",
                'longHelp' => 'include/api/help/me_password_post_help.html',
                'ignoreSystemStatusError' => true,
            ],

            'userPreferences' => [
                'reqType' => 'GET',
                'path' => ['me', 'preferences'],
                'pathVars' => [],
                'method' => 'userPreferences',
                'shortHelp' => "Returns all the current user's stored preferences",
                'longHelp' => 'include/api/help/me_preferences_get_help.html',
                'ignoreMetaHash' => true,
                'ignoreSystemStatusError' => true,
            ],

            'userPreferencesSave' => [
                'reqType' => 'PUT',
                'path' => ['me', 'preferences'],
                'pathVars' => [],
                'method' => 'userPreferencesSave',
                'shortHelp' => 'Mass Save Updated Preferences For a User',
                'longHelp' => 'include/api/help/me_preferences_put_help.html',
                'ignoreSystemStatusError' => true,
            ],

            'userPreference' => [
                'reqType' => 'GET',
                'path' => ['me', 'preference', '?'],
                'pathVars' => ['', '', 'preference_name'],
                'method' => 'userPreference',
                'shortHelp' => 'Returns a specific preference for the current user',
                'longHelp' => 'include/api/help/me_preference_preference_name_get_help.html',
                'ignoreSystemStatusError' => true,
            ],

            'userPreferenceCreate' => [
                'reqType' => 'POST',
                'path' => ['me', 'preference', '?'],
                'pathVars' => ['', '', 'preference_name'],
                'method' => 'userPreferenceSave',
                'shortHelp' => 'Create a preference for the current user',
                'longHelp' => 'include/api/help/me_preference_preference_name_post_help.html',
                'ignoreSystemStatusError' => true,
            ],
            'userPreferenceUpdate' => [
                'reqType' => 'PUT',
                'path' => ['me', 'preference', '?'],
                'pathVars' => ['', '', 'preference_name'],
                'method' => 'userPreferenceSave',
                'shortHelp' => 'Update a specific preference for the current user',
                'longHelp' => 'include/api/help/me_preference_preference_name_put_help.html',
                'ignoreSystemStatusError' => true,
            ],
            'userPreferenceDelete' => [
                'reqType' => 'DELETE',
                'path' => ['me', 'preference', '?'],
                'pathVars' => ['', '', 'preference_name'],
                'method' => 'userPreferenceDelete',
                'shortHelp' => 'Delete a specific preference for the current user',
                'longHelp' => 'include/api/help/me_preference_preference_name_delete_help.html',
                'ignoreSystemStatusError' => true,
            ],
            'getMyFollowedRecords' => [
                'reqType' => 'GET',
                'path' => ['me', 'following'],
                'pathVars' => ['', ''],
                'method' => 'getMyFollowedRecords',
                'shortHelp' => 'This method retrieves all followed methods for the user.',
                'longHelp' => 'include/api/help/me_getfollowed_help.html',
                'ignoreSystemStatusError' => true,
            ],
            'mfaReset' => [
                'reqType' => 'PUT',
                'path' => ['mfa', 'reset'],
                'pathVars' => ['', ''],
                'method' => 'mfaReset',
                'shortHelp' => 'Reset multi-factor authentication for user',
                'longHelp' => 'include/api/help/mfa_reset_help.html',
                'minVersion' => '11.13',
                'ignoreSystemStatusError' => true,
            ],
            'retrieveLastStates' => [
                'reqType' => 'GET',
                'path' => ['me', 'last_states'],
                'pathVars' => ['', ''],
                'method' => 'retrieveLastStates',
                'ignoreMetaHash' => true,
                'shortHelp' => 'Retrieve the set of last state data for the current user',
                'longHelp' => 'include/api/help/me_last_states_get_help.html',
                'ignoreSystemStatusError' => true,
            ],
            'updateLastStates' => [
                'reqType' => 'PUT',
                'path' => ['me', 'last_states'],
                'pathVars' => ['', ''],
                'method' => 'updateLastStates',
                'shortHelp' => 'Perform an update to the set of last state data for the current user',
                'longHelp' => 'include/api/help/me_last_states_put_help.html',
                'ignoreSystemStatusError' => true,
            ],
            'retrieveLastStatesByPlatform' => [
                'reqType' => 'GET',
                'path' => ['me', 'last_states', '?'],
                'pathVars' => ['', '', 'platform'],
                'method' => 'retrieveLastStatesByPlatform',
                'minVersion' => '11.20',
                'ignoreMetaHash' => true,
                'shortHelp' => 'Retrieve the set of last state data for the current user for a specific platform',
                'longHelp' => 'include/api/help/me_last_states_by_platform_get_help.html',
                'ignoreSystemStatusError' => true,
            ],
            'updateLastStatesByPlatform' => [
                'reqType' => 'PUT',
                'path' => ['me', 'last_states', '?'],
                'pathVars' => ['', '', 'platform'],
                'method' => 'updateLastStatesByPlatform',
                'minVersion' => '11.20',
                'shortHelp' => 'Perform an update to the set of last state data for the current user for a platform',
                'longHelp' => 'include/api/help/me_last_states_by_platform_put_help.html',
                'ignoreSystemStatusError' => true,
            ],
            'getPlugins' => [
                'reqType' => 'GET',
                'path' => ['me', 'plugins'],
                'pathVars' => [''],
                'method' => 'getPlugins',
                'shortHelp' => 'Retrieves a list of all Sugar plugins available for Word, Excel, etc.',
                'longHelp' => 'include/api/help/plugins_get_help.html',
                'minVersion' => '11.23',
            ],
        ];
    }

    /**
     * Retrieves the current user info
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function retrieveCurrentUser(ServiceBase $api, array $args)
    {
        $current_user = $this->getUserBean();
        //If the users password is expired, don't generate an etag.
        if (!hasPasswordExpired($current_user)) {
            $hash = $this->getUserHash($current_user);
            if ($api->generateETagHeader($hash, 3)) {
                return;
            }
        }

        $data = $this->getUserData($api, $args);

        if (!empty($data['current_user']['preferences'])) {
            $this->htmlDecodeReturn($data['current_user']['preferences']);
        }

        return $data;
    }

    protected function getUserHash(User $user)
    {
        return $user->getUserMDHash();
    }

    /**
     * Returns TRUE if a user needs to run through the setup wizard after install
     * Used when building $user_data['show_wizard']
     * @return bool TRUE if client should run wizard
     */
    public function shouldShowWizard($category = 'global')
    {
        return $this->getUserBean()->shouldUserCompleteWizard($category);
    }

    /**
     * If user has exceeded time or number of attempts with a generated password,
     * this sets user data `is_password_expired` to true (otherwise false). Also,
     * if password has expired, than `password_expired_message` is set.
     */
    public function setExpiredPassword($user_data)
    {
        $user_data['is_password_expired'] = false;
        $user_data['password_expired_message'] = '';
        require_once 'modules/Users/password_utils.php';
        if (hasPasswordExpired($user_data['user_name'])) {
            $messageLabel = $_SESSION['expiration_label'];
            $message = translate($messageLabel, 'Users');
            $user_data['is_password_expired'] = true;
            $user_data['password_expired_message'] = $message;
            $passwordSettings = $GLOBALS['sugar_config']['passwordsetting'];
            $user_data['password_requirements'] = $this->getPasswordRequirements($passwordSettings);
        }
        return $user_data;
    }

    //Essentially 7.X version of legacy smarty_function_sugar_password_requirements_box
    public function getPasswordRequirements($passwordSettings)
    {
        global $current_language;
        $settings = [];
        $administrationModStrings = return_module_language($current_language, 'Administration');

        //simple password settings keys
        $keys = [
            'oneupper' => 'LBL_PASSWORD_ONE_UPPER_CASE',
            'onelower' => 'LBL_PASSWORD_ONE_LOWER_CASE',
            'onenumber' => 'LBL_PASSWORD_ONE_NUMBER',
            'onespecial' => 'LBL_PASSWORD_ONE_SPECIAL_CHAR',
        ];
        foreach ($keys as $key => $labelKey) {
            if (!empty($passwordSettings[$key])) {
                $settings[$key] = $administrationModStrings[$labelKey] ?? '';
            }
        }
        //custom regex
        if (!empty($passwordSettings['customregex'])) {
            $settings['regex'] = $passwordSettings['regexcomment'] ?? '';
        }

        //Handles min/max password length messages
        $min = isset($passwordSettings['minpwdlength']) && $passwordSettings['minpwdlength'] > 0;
        $max = isset($passwordSettings['maxpwdlength']) && $passwordSettings['maxpwdlength'] > 0;
        if ($min && $max) {
            $settings['lengths'] = $administrationModStrings['LBL_PASSWORD_MINIMUM_LENGTH'] . ' = ' . $passwordSettings['minpwdlength'] . ' ' . $administrationModStrings['LBL_PASSWORD_AND_MAXIMUM_LENGTH'] . ' = ' . $passwordSettings['maxpwdlength'];
        } elseif ($min) {
            $settings['lengths'] = $administrationModStrings['LBL_PASSWORD_MINIMUM_LENGTH'] . ' = ' . $passwordSettings['minpwdlength'];
        } elseif ($max) {
            $settings['lengths'] = $administrationModStrings['LBL_PASSWORD_MAXIMUM_LENGTH'] . ' = ' . $passwordSettings['maxpwdlength'];
        }

        return $settings;
    }

    /**
     * Updates current user info
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function updateCurrentUser(ServiceBase $api, array $args)
    {
        $bean = $this->getUserBean();

        // setting these for the loadBean
        $args['module'] = $bean->module_name;
        $args['record'] = $bean->id;

        $this->updateBean($bean, $api, $args);

        return $this->getUserData($api, $args);
    }

    /**
     * Updates the current user's password
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter|SugarApiExceptionNotFound
     */
    public function updatePassword(ServiceBase $api, array $args)
    {
        $user_data = [];
        $user_data['valid'] = false;

        // Deals with missing required args else assigns oldpass and new paswords
        if (empty($args['old_password']) || empty($args['new_password'])) {
            // @TODO Localize this exception message
            throw new SugarApiExceptionMissingParameter('Error: Missing argument.');
        } else {
            $oldpass = $args['old_password'];
            $newpass = $args['new_password'];
        }

        $bean = $this->getUserIfPassword($oldpass);
        if (null !== $bean) {
            $change = $this->changePassword($bean, $oldpass, $newpass);
            $user_data = array_merge($user_data, $change);
        } else {
            $user_data['message'] = $GLOBALS['app_strings']['LBL_INCORRECT_PASSWORD'];
        }

        return $user_data;
    }

    /**
     * Verifies against the current user's password
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function verifyPassword(ServiceBase $api, array $args)
    {
        $user_data = [];
        $user_data['valid'] = false;

        // Deals with missing required args else assigns oldpass and new paswords
        if (empty($args['password_to_verify'])) {
            // @TODO Localize this exception message
            throw new SugarApiExceptionMissingParameter('Error: Missing argument.');
        }

        // If the user password is good, send that messaging back
        if (!is_null($this->getUserIfPassword($args['password_to_verify']))) {
            $user_data['valid'] = true;
            $user_data['message'] = 'Password verified.';
            $user_data['expiration'] = $this->getUserLoginExpirationPreference();
        }

        return $user_data;
    }

    /**
     * Gets acls given full module list passed in.
     * @param string The platform e.g. portal, mobile, base, etc.
     * @return array
     */
    public function getAcls($platform)
    {
        // in this case we should always have current_user be the user
        global $current_user;
        $mm = $this->getMetaDataManager($platform);
        $fullModuleList = array_keys($GLOBALS['app_list_strings']['moduleList']);
        $acls = [];
        foreach ($fullModuleList as $modName) {
            $bean = BeanFactory::newBean($modName);
            if (!$bean || !is_a($bean, 'SugarBean')) {
                // There is no bean, we can't get data on this
                continue;
            }


            $acls[$modName] = $mm->getAclForModule($modName, $current_user);
            $acls[$modName] = $this->verifyACLs($acls[$modName]);
        }
        // Handle enforcement of acls for clients that override this (e.g. portal)
        $acls = $this->enforceModuleACLs($acls);

        return $acls;
    }

    /**
     * Manipulates the ACLs as needed, per client
     *
     * @param array $acls
     * @return array
     */
    protected function verifyACLs(array $acls)
    {
        // No manipulation for base acls
        return $acls;
    }

    /**
     * Enforces module specific ACLs for users without accounts, as needed
     *
     * @param array $acls
     * @return array
     */
    protected function enforceModuleACLs(array $acls)
    {
        // No manipulation for base acls
        return $acls;
    }

    /**
     * Checks a given password and sends back the user bean if the password matches
     *
     * @param string $passwordToVerify
     * @return User
     */
    protected function getUserIfPassword($passwordToVerify)
    {
        $user = BeanFactory::getBean('Users', $GLOBALS['current_user']->id);
        $currentPassword = $user->user_hash;
        if (User::checkPassword($passwordToVerify, $currentPassword)) {
            return $user;
        }

        return null;
    }

    /**
     * Gets the list of fields that should trigger a user metadata change reauth
     *
     * @return array
     */
    public function getUserPrefsToCache()
    {
        return $this->userPrefMeta;
    }

    /**
     * Gets a single preference for a user by name
     *
     * @param User $user Current User object
     * @param string $pref The name of the pref to get
     * @param string $metaName The metadata property name, usually the same as $pref
     * @param string $category The category for the preference
     * @return array
     */
    protected function getUserPref(User $user, $pref, $metaName, $category = 'global')
    {
        $method = 'getUserPref' . ucfirst($pref);
        if (method_exists($this, $method)) {
            return $this->$method($user, $category);
        }

        // Get the val so we can check for null
        $val = $user->getPreference($pref, $category);

        // Set nulls to empty string
        if (is_null($val)) {
            $val = '';
        }

        return [$metaName => $val];
    }

    /**
     * Gets the user preference name by meta name.
     *
     * @param string $metaName
     * @return string
     */
    protected function getUserPreferenceName($metaName)
    {
        if (false !== $preferenceName = array_search($metaName, $this->userPrefMeta)) {
            return $preferenceName;
        }
        return $metaName;
    }

    /**
     * Gets the user's timezone setting
     *
     * @param User $user The current user
     * @return string
     */
    protected function getUserPrefTimezone(User $user, $category = 'global')
    {
        // Grab the user's timezone preference if it's set
        $val = $user->getPreference('timezone', $category);

        $timeDate = TimeDate::getInstance();

        // If there is no setting for the user, fall back to the system setting
        if (!$val) {
            $val = $timeDate->guessTimezone();
        }

        // If there is still no timezone, fallback to UTC
        if (!$val) {
            $val = 'UTC';
        }

        $dateTime = new SugarDateTime();
        $timeDate->tzUser($dateTime, $user);
        $offset = $timeDate->getIsoOffset($dateTime, ['stripTZColon' => true]);
        $offsetSec = $dateTime->getOffset();

        return ['timezone' => $val, 'tz_offset' => $offset, 'tz_offset_sec' => $offsetSec];
    }

    protected function getUserPrefCurrency(User $user, $category = 'global')
    {
        $return = [];
        global $locale;

        $currency = BeanFactory::newBean('Currencies');
        $currency_id = $user->getPreference('currency', $category);
        $currency->retrieve($currency_id);
        $return['currency_id'] = $currency->id;
        $return['currency_name'] = $currency->name;
        $return['currency_symbol'] = $currency->symbol;
        $return['currency_iso'] = $currency->iso4217;
        $return['currency_rate'] = $currency->conversion_rate;
        $return['currency_show_preferred'] = $user->getPreference('currency_show_preferred');
        $return['currency_create_in_preferred'] = $user->getPreference('currency_create_in_preferred');

        // user number formatting prefs
        $return['decimal_precision'] = $locale->getPrecision();
        $return['decimal_separator'] = $locale->getDecimalSeparator();
        $return['number_grouping_separator'] = $locale->getNumberGroupingSeparator();

        return $return;
    }

    /**
     * Helper function that gets a default signature user pref
     *
     * @param User $user Current User
     * @return array
     */
    protected function getUserPrefSignature_default(User $user)
    {
        // email signature preferences
        return ['signature_default' => $user->getDefaultSignature()];
    }

    /**
     * Helper function to get the email link type user pref
     * @param User $user Current User object
     * @return array
     */
    protected function getUserPrefEmail_link_type(User $user)
    {
        $emailClientPreference = $user->getEmailClientPreference();
        $preferences = ['type' => $emailClientPreference];

        if ($emailClientPreference === 'sugar') {
            $statusCode = OutboundEmailConfigurationPeer::getMailConfigurationStatusForUser($user);
            if ($statusCode != OutboundEmailConfigurationPeer::STATUS_VALID_CONFIG) {
                $preferences['error'] = [
                    'code' => $statusCode,
                    'message' => OutboundEmailConfigurationPeer::$configurationStatusMessageMappings[$statusCode],
                ];
            }
        }

        return [
            'email_client_preference' => $preferences,
        ];
    }

    /**
     * Utility function to get the users preferred language
     *
     * @param User $user Current User object
     * @return array
     */
    protected function getUserPrefLanguage(User $user)
    {
        // use their current auth language if it exists
        if (!empty($_SESSION['authenticated_user_language'])) {
            $language = $_SESSION['authenticated_user_language'];
        } elseif (!empty($user->preferred_language)) {
            // if current auth language doesn't exist get their preferred lang from the user obj
            $language = $user->preferred_language;
        } else {
            // if nothing exists, get the sugar_config default language
            $language = $GLOBALS['sugar_config']['default_language'];
        }

        return ['language' => $language];
    }

    /**
     * Gets the stored number_pinned_modules preference value for the user.
     * Should return the value as empty if an admin has configured the instance
     * to not allow users to set their own number of pinned modules
     *
     * @param User $user The current user object
     * @return array the mapping for the preference setting
     */
    protected function getUserPrefNumber_pinned_modules(User $user)
    {
        $value = null;
        $tabController = new TabController();
        if ($tabController->get_users_pinned_modules()) {
            $value = $user->getPreference('number_pinned_modules');
        }
        return ['number_pinned_modules' => $value];
    }

    /**
     * Returns all the user data to be sent in the REST API call for a normal
     * `/me` call.
     *
     * This data is dependent on the platform used. Each own platform has a
     * different data set to be sent in the response.
     *
     * @param ServiceBase $api
     * @param array $options A list of options like `category` to retrieve the
     *   basic user info. Will use `global` if no `category` is supplied.
     * @return array The user's data to be used in a `/me` request.
     */
    protected function getUserData(ServiceBase $api, array $options)
    {
        $platform = $api->platform;
        $current_user = $this->getUserBean();

        // Get the basics
        $category = $options['category'] ?? 'global';
        $user_data = $this->getBasicUserInfo($platform, $category);

        // Fill in the rest
        $user_data['type'] = self::TYPE_USER;
        if ($current_user->isAdmin()) {
            $user_data['type'] = self::TYPE_ADMIN;
        }
        $user_data['show_wizard'] = $this->shouldShowWizard($category);
        $user_data['id'] = $current_user->id;
        $current_user->_create_proper_name_field();
        $user_data['full_name'] = $current_user->full_name;
        $user_data['user_name'] = $current_user->user_name;
        $user_data['roles'] = ACLRole::getUserRoles($current_user->id);
        $user_data = $this->setExpiredPassword($user_data);
        $user_data['picture'] = $current_user->picture;
        $user_data['acl'] = $this->getAcls($platform);
        $user_data['is_manager'] = User::isManager($current_user->id);
        $user_data['is_idm_user_manager'] = $current_user->isIdmUserManager;
        $user_data['is_top_level_manager'] = false;
        $user_data['reports_to_id'] = $current_user->reports_to_id;
        $user_data['reports_to_name'] = $current_user->reports_to_name;
        if ($user_data['is_manager']) {
            $user_data['is_top_level_manager'] = User::isTopLevelManager($current_user->id);
        }
        $user_data['site_user_id'] = $current_user->site_user_id;

        // licenses return all license types, including bundled products
        $sm = SubscriptionManager::instance();
        $licenses = $sm->getAllImpliedSubscriptions($sm->getAllUserSubscriptions($current_user));
        $user_data['licenses'] = $licenses;

        // Products
        $user_data['products'] = $current_user->getProductsData();

        // Email addresses
        $fieldDef = $current_user->getFieldDefinition('email');

        if (!$fieldDef) {
            $fieldDef = [];
        }

        $sf = SugarFieldHandler::getSugarField('email');
        $sf->apiFormatField($user_data, $current_user, $options, 'email', $fieldDef, ['email'], $api);

        // Address information
        $user_data['address_street'] = $current_user->address_street;
        $user_data['address_city'] = $current_user->address_city;
        $user_data['address_state'] = $current_user->address_state;
        $user_data['address_country'] = $current_user->address_country;
        $user_data['address_postalcode'] = $current_user->address_postalcode;

        require_once 'modules/Teams/TeamSetManager.php';

        $teams = $current_user->get_my_teams();
        $my_teams = [];
        foreach ($teams as $id => $name) {
            $my_teams[] = ['id' => $id, 'name' => $name,];
        }
        $user_data['my_teams'] = $my_teams;
        $user_data['private_team_id'] = $current_user->getPrivateTeamID();
        $defaultTeams = TeamSetManager::getTeamsFromSet($current_user->team_set_id);
        $defaultSelectedTeamIds = [];
        foreach (TeamSetManager::getTeamsFromSet($current_user->acl_team_set_id) as $selectedTeam) {
            $defaultSelectedTeamIds[] = $selectedTeam['id'];
        }
        foreach ($defaultTeams as $id => $team) {
            $defaultTeams[$id]['primary'] = false;
            if ($team['id'] == $current_user->team_id) {
                $defaultTeams[$id]['primary'] = true;
            }
            $defaultTeams[$id]['selected'] = safeInArray($team['id'], $defaultSelectedTeamIds);
        }
        $user_data['preferences']['default_teams'] = $defaultTeams;

        $user_data['site_user_id'] = $current_user->site_user_id;
        $user_data['cookie_consent'] = !empty($current_user->cookie_consent);

        // Send the appearance preference so we can listen for when it changes
        $user_data['appearance'] = $this->getUserPrefAppearance($current_user, 'global')['appearance'];

        // Send fields that can be used for sugar logic expressions
        $user_data = array_merge($user_data, $this->getSugarLogicFields($api, $options, $current_user));

        // Send back a hash of this data for use by the client
        $user_data['_hash'] = $current_user->getUserMDHash();

        return ['current_user' => $user_data];
    }

    /**
     * Gets the subset of User fields that are allowed to be used in sugar logic expressions
     * @param $api
     * @param $options
     * @param $current_user
     * @return array
     */
    protected function getSugarLogicFields($api, $options, $current_user)
    {
        $options['args'] = $options['args'] ?? [];
        $validFields = FormulaHelper::getValidUserFields($current_user->getFieldDefinitions());
        return [
            'sugar_logic_fielddefs' => $validFields,
            'sugar_logic_fields' => ApiHelper::getHelper($api, $current_user)->formatForApi(
                $current_user,
                array_column($validFields, 'name'),
                $options,
            ),
        ];
    }

    /**
     * Utility function to get the users preferred field name placement
     * Default is 'field_on_side'
     *
     * @param User $user Current User object
     * @param string $category The category for the preference
     * @return array
     */
    protected function getUserPrefField_name_placement(User $user, $category = 'global')
    {
        return [
            'field_name_placement' =>
                $user->getPreference('field_name_placement', $category) ?? 'field_on_side',
        ];
    }

    /**
     * Utility function to get the users preference in case of comment log mention.
     * Default is 'off'
     *
     * @param User $user Current User object
     * @param string $category The category for the preference
     * @return array
     */
    protected function getUserPrefSend_email_on_mention(User $user, $category = 'global')
    {
        return [
            'send_email_on_mention' =>
                $user->getPreference('send_email_on_mention', $category) ?? 'send_email_on_mention',
        ];
    }

    /**
     * Utility function to get the users preference for Mobile notifications on record assignment
     * Default is false
     *
     * @param User $user Current User object
     * @param string $category The category for the preference
     * @return array
     */
    protected function getUserPrefMobile_notification_on_assignment(User $user, $category = 'global')
    {
        return [
            'mobile_notification_on_assignment' =>
                $user->getPreference('mobile_notification_on_assignment', $category) ?? false,
        ];
    }

    /**
     * Utility function to get the users preference for Mobile notifications on comment log mentions
     * Default is false
     *
     * @param User $user Current User object
     * @param string $category The category for the preference
     * @return array
     */
    protected function getUserPrefMobile_notification_on_mention(User $user, $category = 'global')
    {
        return [
            'mobile_notification_on_mention' =>
                $user->getPreference('mobile_notification_on_mention', $category) ?? false,
        ];
    }

    /**
     * Utility function to get the user's appearance preference
     * Default is 'system_default'
     *
     * @param User $user Current User object
     * @param string $category The category for the preference
     * @return array
     */
    protected function getUserPrefAppearance(User $user, $category = 'global')
    {
        return [
            'appearance' => $user->getPreference('appearance', $category) ?? 'system_default',
        ];
    }

    /**
     * Gets the basic user data that all users that are logged in will need. Client
     * specific user information will be filled in within the client API class.
     *
     * @param string $platform The platform for this request
     * @return array
     */
    protected function getBasicUserInfo($platform, $category = 'global')
    {
        $user_data = [];
        global $current_user;

        $this->forceUserPreferenceReload($current_user);

        $user_data['preferences'] = [];
        foreach ($this->userPrefMeta as $pref => $metaName) {
            // Twitterate this, since long lines are the devil
            $val = $this->getUserPref($current_user, $pref, $metaName, $category);
            $user_data['preferences'] = array_merge($user_data['preferences'], $val);
        }

        // Handle language on its own for now
        $lang = $this->getUserPrefLanguage($current_user);
        $user_data['preferences'] = array_merge($user_data['preferences'], $lang);

        // Set the user module list
        $user_data['module_list'] = $this->getModuleList($platform);

        return $user_data;
    }

    /**
     * Gets the user bean for the user of the api
     *
     * @return User
     */
    protected function getUserBean()
    {
        global $current_user;

        return $current_user;
    }

    /**
     * Changes a password for a user from old to new
     *
     * @param User $bean User bean
     * @param string $old Old password
     * @param string $new New password
     * @return array
     */
    protected function changePassword(SugarBean $bean, $old, $new)
    {
        if ($bean->change_password($old, $new)) {
            return [
                'valid' => true,
                'message' => 'Password updated.',
                'expiration' => $bean->getPreference('loginexpiration'),
            ];
        }
        //Legacy change_password populates user bean with an error_string on error
        $errorMessage = $bean->error_string ?? $GLOBALS['app_strings']['LBL_PASSWORD_UPDATE_GENERIC_ISSUE'];
        return [
            'valid' => false,
            'message' => $errorMessage,
        ];
    }

    /**
     * Gets the preference for user login expiration
     *
     * @return string
     */
    protected function getUserLoginExpirationPreference()
    {
        global $current_user;

        return $current_user->getPreference('loginexpiration');
    }

    /**
     * Return all the current users preferences
     *
     * @param ServiceBase $api Api Service
     * @param array $args Array of arguments from the rest call
     * @return mixed       User Preferences, if the category exists.  If it doesn't then return an empty array
     */
    public function userPreferences(ServiceBase $api, array $args)
    {
        $current_user = $this->getUserBean();

        // For filtering results back
        $pref_filter = [];
        if (isset($args['pref_filter'])) {
            $pref_filter = explode(',', $args['pref_filter']);
        }

        $category = 'global';
        if (isset($args['category'])) {
            $category = $args['category'];
        }
        $this->forceUserPreferenceReload($current_user);

        $prefs = $current_user->user_preferences[$category] ?? [];

        // Handle filtration of requested preferences
        $data = $this->filterResults($prefs, $pref_filter);
        $this->htmlDecodeReturn($data);
        return $data;
    }

    /**
     * Filters results from a preferences request against a list of prefs
     *
     * @param array $prefs Preferences collection for a user
     * @param array $prefFilter Filter definition to filter against
     * @return array
     */
    protected function filterResults($prefs, $prefFilter)
    {
        if (empty($prefFilter) || !is_array($prefFilter)) {
            return $prefs;
        }

        $return = [];
        foreach ($prefFilter as $key) {
            if (isset($prefs[$key])) {
                $return[$key] = $prefs[$key];
            }
        }
        return $return;
    }

    /**
     * Update multiple user preferences at once
     *
     * @param ServiceBase $api Api Service
     * @param array $args Array of arguments from the rest call
     * @return mixed       Return the updated keys with their values
     */
    public function userPreferencesSave(ServiceBase $api, array $args)
    {
        $current_user = $this->getUserBean();

        $category = 'global';
        if (isset($args['category'])) {
            $category = $args['category'];
            unset($args['category']);
        }

        // set each of the args in the array
        foreach ($args as $key => $value) {
            $preferenceName = $this->getUserPreferenceName($key);
            if ($key === 'default_locale_name_format') {
                if (!isset($GLOBALS['sugar_config']['name_formats'][$value])) {
                    $GLOBALS['log']->security('default_locale_name_format allow list violation');
                    throw new SugarApiExceptionInvalidParameter("Invalid value provided for default_locale_name_format");
                }
            }
            $current_user->setPreference($preferenceName, $value, 0, $category);
        }

        // save the preferences to the db
        $current_user->save();
        $args['_hash'] = $current_user->getUserMDHash();
        return $args;
    }

    /**
     * Return a specific preference for the key that was passed in.
     *
     * @param ServiceBase $api
     * @param array $args
     * @return mixed
     * @return mixed
     */
    public function userPreference(ServiceBase $api, array $args)
    {
        // Get the pref so we can find out if it needs special handling
        $pref = $args['preference_name'];
        $current_user = $this->getUserBean();

        $category = 'global';
        if (isset($args['category'])) {
            $category = $args['category'];
        }
        $this->forceUserPreferenceReload($current_user);

        // Handle special cases if there are any
        $prefKey = array_search($pref, $this->userPrefMeta);
        $alias = $prefKey ?: $pref;
        $data = $this->getUserPref($current_user, $alias, $pref, $category);

        // If the value of the user pref is not an array, or is an array but does
        // not contain an index with the same name as our pref, send the response
        // back an array keyed on the pref. This turns prefs like "m/d/Y" or ""
        // into {"datef": "m/d/Y"} on the client.
        if (!is_array($data) || !isset($data[$pref])) {
            $data = [$pref => $data];
        }

        $this->htmlDecodeReturn($data);
        return $data;
    }

    /**
     * Update a preference.  The key is part of the url and the value comes from the value $args variable
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function userPreferenceSave(ServiceBase $api, array $args)
    {
        $current_user = $this->getUserBean();

        $category = 'global';
        if (isset($args['category'])) {
            $category = $args['category'];
        }

        $preferenceName = $this->getUserPreferenceName($args['preference_name']);

        $current_user->setPreference($preferenceName, $args['value'], 0, $category);
        $current_user->save();

        return [$preferenceName => $args['value']];
    }

    /**
     * Delete a preference.  Since there is no way to actually delete with out resetting the whole category, we just
     * set the value of the key = null.
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function userPreferenceDelete(ServiceBase $api, array $args)
    {
        $current_user = $this->getUserBean();

        $category = 'global';
        if (isset($args['category'])) {
            $category = $args['category'];
        }

        $preferenceName = $this->getUserPreferenceName($args['preference_name']);

        $current_user->setPreference($preferenceName, null, 0, $category);
        $current_user->save();

        return [$preferenceName => ''];
    }

    /**
     * Gets the module list for the current user and platform
     *
     * @param string $platform The platform for this request
     * @return array
     */
    public function getModuleList($platform = '')
    {
        return $this->getMetaDataManager($platform)->getUserModuleList();
    }

    /**
     * Forces a fresh fetching of user preferences.
     *
     * User preferences are written to the users session, so when an admin changes
     * a preference for a user, that user won't get the change until they logout.
     * This forces a fresh fetching of a users preferences from the DB when called.
     * This shouldn't be too expensive of a hit since user preferences need only
     * be fetched once and can be stored on the client.
     *
     * @param User $current_user A User bean
     */
    public function forceUserPreferenceReload($current_user)
    {
        $current_user->reloadPreferences();
    }

    /**
     * Get all of the records a user follows.
     * @param ServiceBase $api
     * @param array $args
     * @return array - records user follows
     */
    public function getMyFollowedRecords(ServiceBase $api, array $args)
    {
        $current_user = $this->getUserBean();

        $options = [];
        $options['limit'] = !empty($args['limit']) ? $args['limit'] : 20;
        $options['offset'] = 0;

        if (!empty($args['offset'])) {
            if ($args['offset'] == 'end') {
                $options['offset'] = 'end';
            } else {
                $options['offset'] = (int)$args['offset'];
            }
        }
        $records = Subscription::getSubscribedRecords($current_user, 'array', $options);
        $beans = [];

        $data = [];
        $data['next_offset'] = -1;
        foreach ($records as $i => $record) {
            if ($i == $options['limit']) {
                $data['next_offset'] = (int)($options['limit'] + $options['offset']);
                continue;
            }
            $beans[] = BeanFactory::getBean($record['parent_type'], $record['parent_id']);
        }

        $data['records'] = $this->formatBeans($api, $args, $beans);
        return $data;
    }

    /**
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiException
     */
    public function mfaReset(ServiceBase $api, array $args): array
    {
        $idmConfig = $this->getIdmConfig();
        $idmModeConfig = $idmConfig->getIDMModeConfig();
        if (!$idmConfig->isIDMModeEnabled()) {
            throw new SugarApiExceptionNoMethod('This method works only in IDM mode');
        }

        if (!$idmConfig->isMultiFactorAuthenticationEnabled()) {
            throw new SugarApiExceptionNoMethod('This method works only if MFA enabled');
        }

        $user = $this->getCurrentUser();
        $tenantSrn = Srn\Converter::fromString($idmModeConfig['tid']);
        $srnManager = new Srn\Manager([
            'partition' => $tenantSrn->getPartition(),
            'region' => $tenantSrn->getRegion(),
        ]);
        $userSrn = Srn\Converter::toString($srnManager->createUserSrn($tenantSrn->getTenantId(), $user->id));

        $container = $this->getDIContainer();
        $httpClient = new Client(['headers' => $idmModeConfig['http_client']['headers'] ?? []]);
        $discovery = new Discovery($idmModeConfig, $container, $httpClient);
        $userApi = new UserApi($httpClient, $discovery, $container);

        if (!$userApi->resetMfa($userSrn, $api->grabToken())) {
            throw new SugarApiExceptionServiceUnavailable(
                sprintf('Can not reset MFA for user %s', $userSrn)
            );
        }

        return [];
    }

    /**
     * Retrieves the last state data for the current user
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function retrieveLastStates(ServiceBase $api, array $args)
    {
        global $current_user;
        $lastStates = $current_user->retrieveLastStates($api->platform);
        return !empty($lastStates) ? $lastStates : [];
    }

    /**
     * Updates the last state data for the current user
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function updateLastStates(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['values']);

        global $current_user;
        return $current_user->updateLastStates($args['values'], $api->platform);
    }

    /**
     * Retrieves the last state data for the current user for a specific platform
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function retrieveLastStatesByPlatform(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['platform']);

        global $current_user;
        $lastStates = $current_user->retrieveLastStates($args['platform']);
        return !empty($lastStates) ? $lastStates : [];
    }

    /**
     * Updates the last state data for the current user for a specific platform
     * @param ServiceBase $api
     * @param array $args
     * @return array
     */
    public function updateLastStatesByPlatform(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['values', 'platform']);

        global $current_user;
        return $current_user->updateLastStates($args['values'], $args['platform']);
    }

    /**
     * @return Config
     */
    protected function getIdmConfig(): Config
    {
        return new Config(\SugarConfig::getInstance());
    }

    /**
     * @return ContainerInterface
     */
    protected function getDIContainer(): ContainerInterface
    {
        return Container::getInstance();
    }

    /**
     * @return \User
     */
    protected function getCurrentUser(): \User
    {
        return $GLOBALS['current_user'];
    }

    /**
     * Retrieves a list of all Sugar plugins available for Word, Excel, etc.
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array[] the list of available plugins grouped by category
     */
    public function getPlugins(ServiceBase $api, array $args)
    {
        global $app_strings;

        // Get the full list of plugins
        $sp = $this->getSugarPluginsInstance();
        $plugins = $sp->getPluginList();

        // Build the list of plugin categories
        $pluginsCat = [
            'Outlook' => [
                'name' => $app_strings['LBL_PLUGIN_OUTLOOK_NAME'],
                'desc' => $app_strings['LBL_PLUGIN_OUTLOOK_DESC'],
                'plugins' => [],
            ],
            'Word' => [
                'name' => $app_strings['LBL_PLUGIN_WORD_NAME'],
                'desc' => $app_strings['LBL_PLUGIN_WORD_DESC'],
                'plugins' => [],
            ],
            'Excel' => [
                'name' => $app_strings['LBL_PLUGIN_EXCEL_NAME'],
                'desc' => $app_strings['LBL_PLUGIN_EXCEL_DESC'],
                'plugins' => [],
            ],
        ];

        if ($this->shouldHideOpiWpiPlugins()) {
            unset($pluginsCat['Outlook']);
            unset($pluginsCat['Word']);
        }

        // Group the plugins into their respective categories
        foreach ($pluginsCat as $key => $value) {
            foreach ($plugins as $plugin) {
                $display_name = str_replace('_', ' ', (string)$plugin['formatted_name']);
                if (strpos($display_name, $key) !== false) {
                    $pluginsCat[$key]['plugins'][] = [
                        'link' => $sp->getPluginLink($plugin['raw_name']),
                        'label' => $display_name,
                    ];
                }
            }
        }

        return $pluginsCat;
    }

    /**
     * Returns whether the UserDownloadsHideOpiWpiPlugins feature flag is
     * toggled
     *
     * @return bool true if the feature is set; false otherwise
     */
    protected function shouldHideOpiWpiPlugins()
    {
        $features = Container::getInstance()->get(FeatureFlag::class);
        return $features->isEnabled(UserDownloadsHideOpiWpiPlugins::getName());
    }

    /**
     * Returns an instance of the SugarPlugins class
     *
     * @return SugarPlugins
     */
    protected function getSugarPluginsInstance()
    {
        return new SugarPlugins();
    }
}
