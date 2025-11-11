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

require_once 'modules/Administration/updater_utils.php';

use Sugarcrm\Sugarcrm\AccessControl\AccessControlManager;
use Sugarcrm\Sugarcrm\Entitlements\Addon;
use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;
use Sugarcrm\Sugarcrm\Entitlements\SubscriptionPrefetcher;
use Sugarcrm\Sugarcrm\Entitlements\Subscription;
use Sugarcrm\Sugarcrm\Security\Password\Hash;
use Sugarcrm\Sugarcrm\Util\Arrays\ArrayFunctions\ArrayFunctions;
use Sugarcrm\Sugarcrm\Denormalization\TeamSecurity\Listener;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\ACL\Cache as AclCacheInterface;
use Sugarcrm\Sugarcrm\PushNotification\ServiceFactory as PushNotificationService;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config as IdpConfig;

/**
 * User is used to store customer information.
 */
class User extends Person
{
    // Stored fields
    public $name = '';
    public $full_name;
    public $id;
    public $user_name;
    public $user_hash;
    public $salutation;
    public $first_name;
    public $last_name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $created_by;
    public $created_by_name;
    public $modified_by_name;
    public $description;
    public $phone_home;
    public $phone_mobile;
    public $phone_work;
    public $phone_other;
    public $phone_fax;
    public $email1;
    public $email2;
    public $address_street;
    public $address_city;
    public $address_state;
    public $address_postalcode;
    public $address_country;
    public $status;
    public $title;
    public $portal_only;
    public $department;
    public $authenticated = false;
    public $error_string;
    public $is_admin;
    public $employee_status;
    public $messenger_id;
    public $messenger_type;
    public $is_group = 0;
    public $accept_status; // to support Meetings
    //adding a property called team_id so we can populate it for use in the team widget
    public $team_id;
    public $sudoer = null;
    public $isIdmUserManager = null;

    public $receive_notifications;
    public $send_email_on_mention;
    public $default_team;

    public $business_center_name;
    public $business_center_id;

    public $reports_to_name;
    public $reports_to_id;
    public $team_exists = false;
    public $table_name = 'users';
    public $module_dir = 'Users';
    public $object_name = 'User';
    public $module_name = 'Users';
    public $user_preferences;

    public $importable = true;
    public $site_user_id;

    /**
     * license type associated with the user
     */
    public $license_type;

    /**
     * @var bool
     */
    public $isDuplicateRecord = false;

    /**
     * old license type from DB
     */
    protected $oldLicenseType;

    /**
     * Old status from DB
     */
    protected $oldStatus;

    public const DEFAULT_LICENSE_TYPE = 'CURRENT';

    protected static $demoUsers = [
        'jim',
        'jane',
        'charles',
        'chris',
        'sarah',
        'regina',
        'admin',
    ];

    /**
     * support user names
     */
    public const SUPPORT_USER_NAME = 'SugarCRMSupport';
    public const SUPPORT_PROVISION_USER_NAME = 'SugarCRMProvisionUser';
    public const SUPPORT_UPGRADE_USER_NAME = 'SugarCRMUpgradeUser';
    public const SUPPORT_PORTAL_USER = 'SugarCustomerSupportPortalUser';

    /**
     * array of well known support users
     */
    public const SUPPORT_USERS = [
        self::SUPPORT_USER_NAME,
        self::SUPPORT_PROVISION_USER_NAME,
        self::SUPPORT_UPGRADE_USER_NAME,
        self::SUPPORT_PORTAL_USER,
    ];

    /**
     * These modules don't take kindly to the studio trying to play about with them.
     *
     * @var array
     */
    protected static $ignoredModuleList = [
        'iFrames',
        'Feeds',
        'Home',
        'Dashboard',
        'Calendar',
        'Activities',
        'Reports',
        'UpgradeHistory',
        'pmse_Inbox',
    ];

    /**
     * @var string|null
     */
    private $hashTS;

    /**
     * @param $userName
     * @return bool
     */
    public static function isTrialDemoUser($userName)
    {
        if (!empty($GLOBALS['sugar_config']['disable_password_change']) && !empty($userName) && in_array($userName, self::$demoUsers)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * check if it is a support user
     * @param User $user
     * @return bool
     */
    public static function isSupportUser(\User $user): bool
    {
        return in_array($user->user_name, self::SUPPORT_USERS);
    }

    /**
     * @var UserPreference
     */
    // @codingStandardsIgnoreLine PSR2.Classes.PropertyDeclaration.Underscore
    public $_userPreferenceFocus;

    public $encodeFields = ['first_name', 'last_name', 'description'];

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = ['reports_to_name',
    ];

    public $emailAddress;

    public $relationship_fields = [
        'call_id' => 'calls',
        'meeting_id' => 'meetings',
        'business_center_id' => 'business_centers',
    ];

    public $new_schema = true;

    public function __construct()
    {
        parent::__construct();
        $this->disable_row_level_security = true;

        $this->_loadUserPreferencesFocus();
    }

    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    protected function _loadUserPreferencesFocus()
    {
        $this->_userPreferenceFocus = new UserPreference($this);
    }

    /**
     * returns an admin user
     */
    public function getSystemUser()
    {
        $q = new SugarQuery();
        $q->from($this);
        $q->select('id');
        $q->where()->equals('is_admin', '1');
        $q->where()->equals('status', 'Active');
        //prefer to get the default administrator
        $q->orderByRaw("(CASE WHEN id = '1' THEN 1 ELSE 0 END)", 'DESC');

        $this->retrieve($q->getOne());
        return $this;
    }

    /**
     * Checks if the user has any registered mobile devices.
     * @return bool
     * @throws SugarQueryException
     */
    public function hasRegisteredDevices(): bool
    {
        $query = new SugarQuery();
        $query->select(['id']);
        $query->from(BeanFactory::newBean('MobileDevices'), ['team_security' => false, 'add_deleted' => false]);
        $query->where()->queryAnd()
            ->equals('assigned_user_id', $this->id);
        $id = $query->getOne();

        return !empty($id);
    }

    /**
     * convenience function to get user's default signature
     */
    public function getDefaultSignature()
    {
        if ($defaultId = $this->getPreference('signature_default')) {
            return $this->getSignature($defaultId);
        } else {
            return [];
        }
    }

    /**
     * retrieves the signatures for a user
     * @param string id ID of user_signature
     * @return array ID, signature, and signature_html
     */
    public function getSignature($id)
    {
        $signatures = $this->getSignaturesArray();

        return $signatures[$id] ?? false;
    }

    public function getSignaturesArray()
    {
        $q = 'SELECT * FROM users_signatures WHERE user_id = \'' . $this->id . '\' AND deleted = 0 ORDER BY name ASC';
        $r = $this->db->query($q);

        // provide "none"
        $sig = ['' => ''];

        while ($a = $this->db->fetchByAssoc($r)) {
            $sig[$a['id']] = $a;
        }

        return $sig;
    }

    /**
     * retrieves any signatures that the User may have created as <select>
     */
    public function getSignatures(
        $live = false,
        $defaultSig = '',
        $forSettings = false
    ) {


        $sig = $this->getSignaturesArray();
        $sigs = [];
        foreach ($sig as $key => $arr) {
            $sigs[$key] = !empty($arr['name']) ? $arr['name'] : '';
        }

        $change = '';
        if (!$live) {
            $change = ($forSettings) ? "onChange='displaySignatureEdit();'" : "onChange='setSigEditButtonVisibility();'";
        }

        $id = (!$forSettings) ? 'signature_id' : 'signature_idDisplay';

        $out = "<select {$change} id='{$id}' name='{$id}'>";
        $out .= get_select_options_with_id($sigs, $defaultSig) . '</select>';

        return $out;
    }

    /**
     * returns buttons and JS for signatures
     */
    public function getSignatureButtons($jscall = '', $defaultDisplay = false)
    {
        global $mod_strings;

        $jscall = empty($jscall) ? 'open_email_signature_form' : $jscall;

        $butts = "<input class='button' onclick='javascript:{$jscall}(\"\", \"{$this->id}\");' value='{$mod_strings['LBL_BUTTON_CREATE']}' type='button'>&nbsp;";
        if ($defaultDisplay) {
            $butts .= '<span name="edit_sig" id="edit_sig" style="visibility:inherit;"><input class="button" onclick="javascript:' . $jscall . '(document.getElementById(\'signature_id\', \'\').value)" value="' . $mod_strings['LBL_BUTTON_EDIT'] . '" type="button" tabindex="392">&nbsp;
					</span>';
        } else {
            $butts .= '<span name="edit_sig" id="edit_sig" style="visibility:hidden;"><input class="button" onclick="javascript:' . $jscall . '(document.getElementById(\'signature_id\', \'\').value)" value="' . $mod_strings['LBL_BUTTON_EDIT'] . '" type="button" tabindex="392">&nbsp;
					</span>';
        }
        return $butts;
    }

    /**
     * performs a rudimentary check to verify if a given user has setup personal
     * InboundEmail
     *
     * @return bool
     */
    public function hasPersonalEmail()
    {
        $focus = BeanFactory::newBean('InboundEmail');
        $focus->disable_row_level_security = true;
        $focus->retrieve_by_string_fields(['group_id' => $this->id]);

        return !empty($focus->id);
    }

    /* Returns the User's private GUID; this is unassociated with the User's
     * actual GUID.  It is used to secure file names that must be HTTP://
     * accesible, but obfusicated.
     */
    public function getUserPrivGuid()
    {
        $userPrivGuid = $this->getPreference('userPrivGuid', 'global', $this);
        if ($userPrivGuid) {
            return $userPrivGuid;
        } else {
            $this->setUserPrivGuid();
            if (!isset($_SESSION['setPrivGuid'])) {
                $_SESSION['setPrivGuid'] = true;
                $userPrivGuid = $this->getUserPrivGuid();
                return $userPrivGuid;
            } else {
                sugar_die('Breaking Infinite Loop Condition: Could not setUserPrivGuid.');
            }
        }
    }

    public function setUserPrivGuid()
    {
        $privGuid = create_guid();
        $this->setPreference('userPrivGuid', $privGuid, 0, 'global', $this);
    }

    /**
     * Interface for the User object to calling the UserPreference::setPreference() method in modules/UserPreferences/UserPreference.php
     *
     * @param string $name Name of the preference to set
     * @param string $value Value to set preference to
     * @param null $nosession For BC, ignored
     * @param string $category Name of the category to retrieve
     * @see UserPreference::setPreference()
     *
     */
    public function setPreference(
        $name,
        $value,
        $nosession = 0,
        $category = 'global'
    ) {


        // for BC
        if (func_num_args() > 4) {
            $user = func_get_arg(4);
            $GLOBALS['log']->deprecated('User::setPreferences() should not be used statically.');
        } else {
            $user = $this;
        }

        $user->_userPreferenceFocus->setPreference($name, $value, $category);
    }

    /**
     * Interface for the User object to calling the UserPreference::resetPreferences() method in modules/UserPreferences/UserPreference.php
     *
     * @param string $category category to reset
     * @see UserPreference::resetPreferences()
     *
     */
    public function resetPreferences(
        $category = null
    ) {


        // for BC
        if (func_num_args() > 1) {
            $user = func_get_arg(1);
            $GLOBALS['log']->deprecated('User::resetPreferences() should not be used statically.');
        } else {
            $user = $this;
        }

        $user->_userPreferenceFocus->resetPreferences($category);
    }

    /**
     * Interface for the User object to calling the UserPreference::savePreferencesToDB() method in modules/UserPreferences/UserPreference.php
     *
     * @see UserPreference::savePreferencesToDB()
     */
    public function savePreferencesToDB()
    {
        // for BC
        if (func_num_args() > 0) {
            $user = func_get_arg(0);
            $GLOBALS['log']->deprecated('User::savePreferencesToDB() should not be used statically.');
        } else {
            $user = $this;
        }

        $user->_userPreferenceFocus->savePreferencesToDB();
    }

    /**
     * Unconditionally reloads user preferences from the DB and updates the session
     * @param string $category name of the category to retreive, defaults to global scope
     * @return bool successful?
     */
    public function reloadPreferences($category = 'global')
    {
        return $this->_userPreferenceFocus->reloadPreferences($category = 'global');
    }

    /**
     * Interface for the User object to calling the UserPreference::getUserDateTimePreferences() method in modules/UserPreferences/UserPreference.php
     *
     * @return array 'date' - date format for user ; 'time' - time format for user
     * @see UserPreference::getUserDateTimePreferences()
     *
     */
    public function getUserDateTimePreferences()
    {
        // for BC
        if (func_num_args() > 0) {
            $user = func_get_arg(0);
            $GLOBALS['log']->deprecated('User::getUserDateTimePreferences() should not be used statically.');
        } else {
            $user = $this;
        }

        return $user->_userPreferenceFocus->getUserDateTimePreferences();
    }

    /**
     * Interface for the User object to calling the UserPreference::loadPreferences() method in modules/UserPreferences/UserPreference.php
     *
     * @param string $category name of the category to retreive, defaults to global scope
     * @return bool successful?
     * @see UserPreference::loadPreferences()
     *
     */
    public function loadPreferences(
        $category = 'global'
    ) {


        // for BC
        if (func_num_args() > 1) {
            $user = func_get_arg(1);
            $GLOBALS['log']->deprecated('User::loadPreferences() should not be used statically.');
        } else {
            $user = $this;
        }

        return $user->_userPreferenceFocus->loadPreferences($category);
    }

    /**
     * Interface for the User object to calling the UserPreference::setPreference() method in modules/UserPreferences/UserPreference.php
     *
     * @param string $name name of the preference to retreive
     * @param string $category name of the category to retreive, defaults to global scope
     * @return mixed the value of the preference (string, array, int etc)
     * @see UserPreference::getPreference()
     *
     */
    public function getPreference(
        $name,
        $category = 'global'
    ) {


        // for BC
        if (func_num_args() > 2) {
            $user = func_get_arg(2);
            $GLOBALS['log']->deprecated('User::getPreference() should not be used statically.');
        } else {
            $user = $this;
        }

        return $user->_userPreferenceFocus->getPreference($name, $category);
    }

    /**
     * Returns TRUE if user should complete setup wizard for category
     *
     * @param string $category default 'global'
     * @return bool
     */
    public function shouldUserCompleteWizard($category = 'global')
    {
        $systemStatus = apiCheckSystemStatus();
        if ($systemStatus !== true && !$this->allowNonAdminToContinue($systemStatus)) {
            // System isn't ok, so no need to configure it
            // or non-admin can continue is not allowed
            return false;
        }
        $ut = $this->getPreference('ut', $category);
        return !filter_var($ut, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * allow non-admin user continue to use this Sugar instance
     * @param array|bool $systemStatus
     * @param \User $user
     */
    public function allowNonAdminToContinue($systemStatus)
    {
        if ($systemStatus === true) {
            return true;
        }

        if (!is_array($systemStatus) || $this->isAdmin()) {
            return false;
        }

        if (isset($systemStatus['level']) && $systemStatus['level'] == 'admin_only'
            && isset($systemStatus['message']) && $systemStatus['message'] === 'ERROR_LICENSE_SEATS_MAXED'
            && empty($this->getUserExceededAndInvalidLicenseTypes())
        ) {
            return true;
        }

        return false;
    }

    /**
     * get this user's license types either in exceeded limits or invalid categories
     * @return array
     */
    public function getUserExceededAndInvalidLicenseTypes()
    {
        return SubscriptionManager::instance()->getUserExceededAndInvalidLicenseTypes($this);
    }

    /**
     * Interface for the User object to calling the UserPreference::removePreference() method
     * in modules/UserPreferences/UserPreference.php
     *
     * @param string $name name of the preference to remove
     * @param string $category name of the category to remove, defaults to global scope
     * @see UserPreference::removePreference()
     *
     */
    public function removePreference($name, $category = 'global')
    {
        $this->_userPreferenceFocus->removePreference($name, $category);
    }

    /**
     * incrementETag
     *
     * This function increments any ETag seed needed for a particular user's
     * UI. For example, if the user changes their theme, the ETag seed for the
     * main menu needs to be updated, so you call this function with the seed name
     * to do so:
     *
     * UserPreference::incrementETag("mainMenuETag");
     *
     * @param string $tag ETag seed name.
     * @return nothing
     */
    public function incrementETag($tag)
    {
        $val = $this->getETagSeed($tag);
        if ($val == 2147483648) {
            $val = 0;
        }
        $val++;
        $this->setPreference($tag, $val, 0, 'ETag');
    }

    /**
     * getETagSeed
     *
     * This function is a wrapper to encapsulate getting the ETag seed and
     * making sure it's sanitized for use in the app.
     *
     * @param string $tag ETag seed name.
     * @return integer numeric value of the seed
     */
    public function getETagSeed($tag)
    {
        $val = $this->getPreference($tag, 'ETag');
        if ($val == null) {
            $val = 0;
        }
        return $val;
    }


    /**
     * Get WHERE clause that fetches all users counted for licensing purposes
     * @return string
     */
    public static function getLicensedUsersWhere()
    {
        $db = DBManagerFactory::getInstance();

        return sprintf(
            ' deleted != 1 AND user_name IS NOT NULL AND is_group != 1 AND portal_only != 1 AND status = %s AND %s > 0 AND %s',
            $db->quoted('Active'),
            $db->convert('user_name', 'length'),
            self::getSystemUsersWhere()
        );
    }

    /**
     * Get WHERE clause for system users
     * @param string $comp SQL comparison operator
     * @param string $logic SQL logical operator
     * @return string
     */
    public static function getSystemUsersWhere($comp = '!=', $logic = 'AND')
    {
        $db = DBManagerFactory::getInstance();
        $where = ' 1=1 ';
        foreach (self::SUPPORT_USERS as $user) {
            $where .= sprintf(
                ' %s user_name %s %s ',
                $logic,
                $comp,
                $db->quoted($user)
            );
        }
        return $where;
    }

    /**
     * Gets the BWC theme for this user.
     *
     * There are only 2 supported themes at this time: `RTL` and `RacerX`.
     * `RTL` is returned if the current language is an RTL language, `RacerX` is
     * returned otherwise.
     *
     * @return string The theme currently set to this user.
     */
    public function getBWCTheme()
    {
        //FIXME: SC-3358 Should be getting the RTL languages from metadata.
        static $rtlLanguages = ['he_IL', 'ar_SA'];
        $language = !empty($this->preferred_language) ? $this->preferred_language : $GLOBALS['current_language'];
        $theme = in_array($language, $rtlLanguages) ? 'RTL' : 'RacerX';

        return $theme;
    }

    /**
     * Toggles this user's admin status and flushes the ACL cache.
     *
     * @param boolean $admin If `true`, then make this user an admin.
     *   Otherwise, remove admin privileges.
     */
    public function setAdmin($admin)
    {
        $this->is_admin = $admin ? 1 : 0;

        // When we change a user to or from admin status, we have to flush the ACL cache
        // or else the user will not be able to access some admin modules.
        AclCache::getInstance()->clearAll();
        // FIXME TY-1094: investigate if we should enforce admin/portal API user/group mutual exclusivity here
    }

    /**
     * check if the user has admin or admin & dev privilege
     * @return bool
     */
    public function hasAdminAndDevPrivilege(string $module = 'Users') : bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (empty($this->id)) {
            return false;
        }

        if ($this->isUserAllowedModuleAccess($module) && $this->getUserAdminAccesslevel($module) === ACL_ALLOW_ADMIN_DEV) {
            return true;
        }

        return false;
    }

    /**
     *
     * check if the user has module access
     * @param string $module
     * @return bool
     */
    protected function isUserAllowedModuleAccess(string $module) : bool
    {
        if (empty($this->id)) {
            return false;
        }
        return ACLController::moduleSupportsACL($module)
            && ACLAction::getUserAccessLevel($this->id, $module, 'access') >= ACL_ALLOW_ENABLED;
    }

    /**
     * get user's admin access level
     * @param string $module
     * @return int
     */
    protected function getUserAdminAccesslevel(string $module) : int
    {
        if (!ACLController::moduleSupportsACL($module)) {
            return ACL_ALLOW_NONE;
        }

        return ACLAction::getUserAccessLevel($this->id, $module, 'admin');
    }

    public function save($check_notify = false)
    {
        // Check if data supplied is valid to save the record, return if not.
        if (!$this->verify_data()) {
            return $this->id;
        }
        $isUpdate = !empty($this->id) && !$this->new_with_id;

        // this will cause the logged in admin to have the licensed user count refreshed
        if (isset($_SESSION)) {
            unset($_SESSION['license_seats_needed']);
        }

        // fill up license type info for single license type for active user only
        if (!isset($this->license_type)
            && $this->status == 'Active'
            && SubscriptionManager::instance()->isSingleMangoTypeEntitlement()) {
            $defaultLicenseType = SubscriptionManager::instance()->getUserDefaultLicenseType();
            if (!empty($defaultLicenseType)) {
                $this->license_type = json_encode([$defaultLicenseType]);
            }
        }

        // validate license type
        $licenseTypeWasModified = false;
        if (isset($this->license_type)) {
            $licenseTypes = $this->getTopLevelLicenseTypes();
            if ($this->isLicenseTypeModified($licenseTypes)) {
                if (!$this->validateLicenseTypes($licenseTypes)) {
                    throw new SugarApiExceptionInvalidParameter('User::save: Invalid license_type in module: Users');
                }

                // make sure only admin can modify the license type
                global $current_user;
                if (!$current_user->hasAdminAndDevPrivilege()) {
                    throw new SugarApiExceptionNotAuthorized('Not authorized to modify license_type in module: Users');
                }
                $licenseTypeWasModified = true;
            }
            $this->setLicenseType($licenseTypes);
        }

        global $sugar_flavor;
        $admin = Administration::getSettings();
        if (!empty($sugar_flavor) && !empty($admin->settings['license_enforce_user_limit'])) {
            // Begin Express License Enforcement Check
            // this will cause the logged in admin to have the licensed user count refreshed
            if (isset($_SESSION)) {
                unset($_SESSION['license_seats_needed']);
            }
            if ($this->portal_only != 1 && $this->is_group != 1 && $this->status == 'Active'
                && (empty($this->fetched_row)
                    || $this->fetched_row['status'] == 'Inactive'
                    || $this->fetched_row['status'] == ''
                    || $licenseTypeWasModified)) {

                $this->checkIfSaveExceedsLicenseSeats();
            }
        }
        // End Express License Enforcement Check

        // wp: do not save user_preferences in this table, see user_preferences module
        $this->user_preferences = '';

        // if this is an admin user, do not allow is_group or portal_only flag to be set.
        if ($this->is_admin) {
            $this->is_group = 0;
            $this->portal_only = 0;
        }

        if (is_null($this->is_group)) {
            $this->is_group = 0;
        }

        // set some default preferences when creating a new user
        $setNewUserPreferences = empty($this->id) || !empty($this->new_with_id);


        // If the 'Primary' team changed then the team widget has set 'team_id' to a new value and we should
        // assign the same value to default_team because User module uses it for setting the 'Primary' team
        if (!empty($this->team_id)) {
            $this->default_team = $this->team_id;
        }

        // track the current reports to id to be able to use it if it has changed
        $old_reports_to_id = $this->fetched_row['reports_to_id'] ?? '';

        if (empty($this->site_user_id)) {
            if (!$this->id) {
                $this->id = create_guid();
                $this->new_with_id = true;
            }
            $this->site_user_id = getSiteHash($this->id);
        }

        // Update the datetime the consent was granted
        if (!empty($this->cookie_consent) && empty($this->cookie_consent_received_on)) {
            $this->cookie_consent_received_on = TimeDate::getInstance()->nowDb();
        }
        // Wipe the datetime if the consent was revoked
        if (empty($this->cookie_consent) && !empty($this->cookie_consent_received_on)) {
            $this->cookie_consent_received_on = null;
        }

        parent::save($check_notify);

        if ($this->portal_only && !empty($this->portal_user_password)) {
            $this->change_password('', $this->portal_user_password);
        }

        //if this is an import, make sure the related teams get added
        //properly to the team membership
        if ($this->in_import) {
            $this->load_relationship('teams');
            $relatedTeams = $this->teams->get();
            $teamBean = null;
            //add the user to each team
            foreach ($relatedTeams as $team_id) {
                $teamBean = BeanFactory::getBean('Teams', $team_id);
                $teamBean->add_user_to_team($this->id);
            }
        }

        $GLOBALS['sugar_config']['disable_team_access_check'] = true;
        if ($this->status != 'Reserved' && !$this->portal_only) {
            // If this is not an update, then make sure the new user logic is executed.
            if (!$isUpdate) {
                // If this is a new user, make sure to add them to the appriate default teams
                if (!$this->team_exists) {
                    $team = BeanFactory::newBean('Teams');
                    $team->new_user_created($this);
                }
            } elseif (empty($GLOBALS['sugar_config']['noPrivateTeamUpdate'])) {
                //if this is an update, then we need to ensure we keep the user's
                //private team name and name_2 in sync with their name.
                $team_id = $this->getPrivateTeamID();
                if (!empty($team_id)) {
                    $team = BeanFactory::getBean('Teams', $team_id);
                    Team::set_team_name_from_user($team, $this);
                    $team->save();
                }
            }
        }

        // If reports to has changed, call update team memberships to correct the membership tree
        if ($old_reports_to_id != $this->reports_to_id) {
            $this->update_team_memberships($old_reports_to_id);
        }

        // set some default preferences when creating a new user
        if ($setNewUserPreferences) {
            $this->setPreference('reminder_time', 1800);
            if (!$this->getPreference('calendar_publish_key')) {
                $this->setPreference('calendar_publish_key', create_guid());
            }
        }

        $this->savePreferencesToDB();
        //CurrentUserApi needs a consistent timestamp/format of the data modified for hash purposes.
        $this->hashTS = $this->date_modified;

        $oe = BeanFactory::newBean('OutboundEmail');
        $oeSystemOverride = $oe->getUsersMailerForSystemOverride($this->id);

        if ($oeSystemOverride) {
            $oeSystemOverride->populateFromUser($this);

            if (!$oe->isAllowUserAccessToSystemDefaultOutbound() && !empty($this->mail_credentials)) {
                $mailCredentials = json_decode($this->mail_credentials, true);

                $oeSystemOverride->mail_smtpuser = $mailCredentials['mail_smtpuser'];
                if ($oeSystemOverride->mail_authtype === 'oauth2') {
                    $oeSystemOverride->eapm_id = $mailCredentials['eapm_id'];
                    $oeSystemOverride->authorized_account = $mailCredentials['authorized_account'];
                } elseif ($mailCredentials['mail_smtppass_change'] ?? false) {
                        $oeSystemOverride->mail_smtppass = $mailCredentials['mail_smtppass'];
                }
            }

            $oeSystemOverride->save();
        } elseif (!$oe->isAllowUserAccessToSystemDefaultOutbound()) {
            $oe->createUserSystemOverrideAccount($this->id);
        }

        // In case this new/updated user changes the system status, reload it here
        if (!SubscriptionManager::instance()->getFixLicenseProcessState()) {
            apiLoadSystemStatus(true);
        }

        return $this->id;
    }

    /**
     * Runs during save to check that the User being saved would not cause a
     * license overage
     */
    protected function checkIfSaveExceedsLicenseSeats()
    {
        // Allow IDM to handle license checks if IDM licensing mode is enabled
        $idpConfig = new IdpConfig(\SugarConfig::getInstance());
        if ($idpConfig->isIDMModeEnabled() && $idpConfig->getUserLicenseTypeIdmModeLock()) {
            return;
        }

        // Get the license types that are already at capacity in the system
        $licenseTypesAtCapacity = $this->getLicenseTypesAtCapacity();

        // Get the additional license seats that would be occupied after the save
        $additionalLicenseTypesUsed = $this->getLicenseSeatsAddedBySave();

        // If the save would cause an overage of any license type, handle it
        $userLicensesOverCapacity = array_intersect($licenseTypesAtCapacity, $additionalLicenseTypesUsed);
        if (!empty($userLicensesOverCapacity)) {
            $this->handleSaveExceedsLicenseSeats($userLicensesOverCapacity);
        }
    }

    /**
     * Returns a list of any license types that are currently filled to capacity
     * based on the instance's seat counts
     *
     * @return array
     */
    protected function getLicenseTypesAtCapacity()
    {
        return SubscriptionManager::instance()->getUserExceededLicenseTypes($this);
    }

    /**
     * Returns the list of license seats that would be added from the current
     * save. This can happen if the license_type was changed, or if the User
     * status was changed to Active
     *
     * @return mixed
     */
    protected function getLicenseSeatsAddedBySave()
    {
        // Inactive Users do not count toward license seats
        if ($this->status !== 'Active') {
            return [];
        }

        $oldUserLicenses = json_decode($this->oldLicenseType ?? '') ?? [];
        $newUserLicenses = json_decode($this->license_type ?? '') ?? [];

        // When there is a new User, or an existing User's status is changing
        // to Active, all licenses assigned are being added to the seat count
        if (!$this->isUpdate() || $this->oldStatus !== $this->status) {
            return $newUserLicenses;
        }

        // In all other cases, compare the new license set to the old one
        return array_diff($newUserLicenses, $oldUserLicenses);
    }

    /**
     * Throws an error indicating that saving this user would cause a license
     * seat overage
     *
     * @param array $userLicensesOverCapacity
     * @throws SugarApiExceptionLicenseSeatsNeeded
     */
    protected function handleSaveExceedsLicenseSeats(array $userLicensesOverCapacity)
    {
        // Get a comma separated string of the license types that are exceeded
        foreach ($userLicensesOverCapacity as $index => $type) {
            $userLicensesOverCapacity[$index] = self::getLicenseTypeDescription($type);
        }
        $typeString = implode(', ', $userLicensesOverCapacity);

        // Log and throw the error
        $logMsg = 'The number of active %s users is already the maximum number of licenses allowed. ' .
            'Additional users cannot be created, activated, or modified to have these licenses';
        LoggerManager::getLogger()->error(sprintf($logMsg, $typeString));

        if (!empty($this->external_auth_only) || isFromApi()) {
            $msg = sprintf(translate('WARN_LICENSE_TYPE_SEATS_EDIT_MAXED', 'Administration'), $typeString);
            throw new SugarApiExceptionLicenseSeatsNeeded(
                $msg,
                null,
                null,
                0,
                'license_seats_needed'
            );
        }

        $msg = sprintf(translate('WARN_LICENSE_TYPE_SEATS_EDIT_USER', 'Administration'), $typeString);
        if (isset($_REQUEST['action'])
            && ($_REQUEST['action'] == 'MassUpdate' || $_REQUEST['action'] == 'Save')) {
            $sv = new SugarView();
            $sv->init('Users');
            $sv->renderJavascript();
            $sv->displayHeader();
            $sv->errors[] = $msg;
            $sv->displayErrors([], true);
            $msg = '';
        }

        // When action is not set, we're coming from the installer or non-UI source.
        die($msg);
    }

    /**
     * get system subscriptions
     * @return array
     */
    public function getSystemLicenseTypesSelections(): array
    {
        $subscriptions = SubscriptionManager::instance()->getTopLevelSystemSubscriptionKeys();
        $selections = [];
        foreach (array_keys($subscriptions) as $type) {
            if ($type === Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY) {
                $selections[$type] = self::getLicenseTypeDescription($type);
                // get all bundled keys as well
                $bundleTypes = $this->getBundledSubscriptionKeys($type);
                $bundleTypes = array_merge([Subscription::SUGAR_SELL_PREMIER_KEY], array_keys($bundleTypes));
                foreach ($bundleTypes as $type) {
                    $selections[$type] = self::getLicenseTypeDescription($type);
                }
                $idpConfig = new IdpConfig(\SugarConfig::getInstance());
                if ($idpConfig->isIDMModeEnabled() && $idpConfig->getUserLicenseTypeIdmModeLock()) {
                    $type = Subscription::SUGAR_SELL_PREMIER_KEY;
                    $selections[$type] = self::getLicenseTypeDescription($type);
                }
            }
            $selections[$type] = self::getLicenseTypeDescription($type);
        }

        return $selections;
    }

    /**
     * Gets the options for the Import/Export Character Set dropdown
     *
     * @return array the list of Import/Export Character Set dropdown options
     */
    public function getDefaultExportCharsetOptions()
    {
        global $locale;
        return $locale->getCharsetSelect();
    }

    /**
     * Gets the options for the Date Format dropdown
     *
     * @return array the list of Date Format dropdown options
     */
    public function getDateFormatOptions()
    {
        global $sugar_config;
        return $sugar_config['date_formats'] ?? [];
    }

    /**
     * Gets the options for the Time Format dropdown
     *
     * @return array the list of Time Format dropdown options
     */
    public function getTimeFormatOptions()
    {
        global $sugar_config;
        return $sugar_config['time_formats'] ?? [];
    }

    /**
     * Gets the options for the Timezone dropdown
     *
     * @return array the list of Timezone dropdown options
     */
    public function getTimeZoneOptions()
    {
        return TimeDate::getTimezoneList();
    }

    /**
     * Gets the options for the Preferred Currency dropdown
     *
     * @return array the list of Preferred Currency dropdown options
     */
    public function getCurrencyOptions()
    {
        $currency = new ListCurrency();
        $currency->lookupCurrencies();
        $currencyList = [];
        foreach ($currency->list as $item) {
            $currencyList[$item->id] = "{$item->name} : {$item->symbol}";
        }
        return $currencyList;
    }

    /**
     * Gets the options for the Name Display Format dropdown
     *
     * @return array the list of Name Display Format dropdown options
     */
    public function getNameFormatOptions()
    {
        global $locale;
        global $sugar_config;
        return $locale->getUsableLocaleNameOptions($sugar_config['name_formats']);
    }

    /**
     * Gets the options for the PDF Font dropdown
     *
     * @return array the list of PDF Font dropdown options
     */
    public function getFontListOptions()
    {
        $fontManager = new FontManager();
        return $fontManager->getSelectFontList();
    }

    /**
     * Gets the options for the First Day of Week dropdown
     *
     * @return array the list of First Day of Week dropdown options
     */
    public function getFirstDayOfWeekOptions()
    {
        global $app_list_strings;

        $fdowDays = [];
        foreach ($app_list_strings['dom_cal_day_long'] as $day) {
            if ($day != '') {
                $fdowDays[] = $day;
            }
        }
        return $fdowDays;
    }

    /**
     * get license type description
     * @param string $type license type
     * @return string
     */
    public static function getLicenseTypeDescription(string $type): string
    {
        // trying to use customer product name from
        if ($type === Subscription::SUGAR_SELL_PREMIER_KEY) {
            // use bundle key
            $type = Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY;
        }

        $customerProductName = SubscriptionManager::instance()->getCustomerProductNameByKey($type);
        if (!empty($customerProductName)) {
            return $customerProductName;
        }
        global $current_language;
        $mod_strings = return_module_language($current_language, 'Users');
        if ($type === Subscription::SUGAR_SERVE_KEY) {
            return $mod_strings['LBL_LICENSE_SUGAR_SERVE'];
        } elseif ($type === Subscription::SUGAR_SELL_KEY) {
            return $mod_strings['LBL_LICENSE_SUGAR_SELL'];
        } elseif ($type === Subscription::SUGAR_HINT_KEY) {
            return $mod_strings['LBL_LICENSE_SUGAR_HINT'];
        } elseif ($type === Subscription::SUGAR_SELL_BUNDLE_KEY) {
            return 'Sugar SELL';
        } elseif ($type === Subscription::SUGAR_SELL_ESSENTIALS_KEY) {
            return 'Sugar SELL Essentials';
        } elseif ($type === Subscription::SUGAR_SELL_ADVANCED_BUNDLE_KEY) {
            return 'Sugar SELL Advanced';
        } elseif ($type === Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY) {
            return 'Sugar SELL Premier';
        } elseif ($type === Subscription::SUGAR_BASIC_KEY) {
            global $sugar_flavor;
            $mod_strings = return_module_language($current_language, 'Home');
            if (!empty($sugar_flavor)) {
                if ($sugar_flavor === 'ENT') {
                    return $mod_strings['LBL_SUGAR_ENTERPRISE'];
                }
                if ($sugar_flavor === 'PRO') {
                    return $mod_strings['LBL_SUGAR_PROFESSIONAL'];
                }
                if ($sugar_flavor === 'ULT') {
                    return $mod_strings['LBL_SUGAR_ULTIMATE'];
                }
            }
            return $mod_strings['LBL_LICENSE_INVALID_PRODUCT'];
        } else {
            // new non CRM types, such as CONNECT, MAPS, DISCOVERY etc
            if (in_array($type, SubscriptionManager::instance()->getAllSupportedProducts())) {
                return $type;
            }
        }

        $mod_strings = return_module_language($current_language, 'Home');
        return $mod_strings['LBL_LICENSE_TYPE_INVALID'];
    }

    /**
     * @param string $role_name - Must be the exact name of the acl_role
     * @param string $user_id - The user id to check for the role membership, empty string if current user
     * @desc Determine whether or not a user is a member of an ACL Role. This function caches the
     *       results in the session or to prevent running queries after the first time executed.
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     * @return boolean true if the user is a member of the role_name, false otherwise
     */
    public function check_role_membership($role_name, $user_id = '')
    {

        global $current_user;

        if (empty($user_id)) {
            $user_id = $current_user->id;
        }

        // Check the Sugar External Cache to see if this users memberships were cached
        $role_array = sugar_cache_retrieve('RoleMemberships_' . $user_id);

        // If we are pulling the roles for the current user
        if ($user_id == $current_user->id) {
            // If the Session doesn't contain the values
            if (!isset($_SESSION['role_memberships'])) {
                // This means the external cache already had it loaded
                if (!empty($role_array)) {
                    $_SESSION['role_memberships'] = $role_array;
                } else {
                    $_SESSION['role_memberships'] = ACLRole::getUserRoleNames($user_id);
                    $role_array = $_SESSION['role_memberships'];
                }
            } // else the session had the values, so we assign to the role array
            else {
                $role_array = $_SESSION['role_memberships'];
            }
        } else {
            // If the external cache didn't contain the values, we get them and put them in cache
            if (!$role_array) {
                $role_array = ACLRole::getUserRoleNames($user_id);
                sugar_cache_put('RoleMemberships_' . $user_id, $role_array);
            }
        }

        // If the role doesn't exist in the list of the user's roles
        return (!empty($role_array) && ArrayFunctions::in_array_access($role_name, $role_array));
    }

    public function get_summary_text()
    {
        return $this->name;
    }

    /**
     * Authenicates the user; returns true if successful
     *
     * @param string $password MD5-encoded password
     * @return bool
     */
    public function authenticate_user($password)
    {
        $data = self::getUserDataByNameAndPassword($this->user_name, $password);

        if ($data) {
            $this->id = $data['id'];
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retrieves an User bean
     * pre-format name & full_name attribute with first/last
     * loads User's preferences
     * If the picture doesn't exist on the file system, it empties out the picture field
     *
     * @param string $id ID of the User
     * @param bool $encode Encode the result
     * @param bool $deleted Include deleted users
     * @return User|null         Returns the user object unless once is not found, then it returns null
     */
    public function retrieve($id = -1, $encode = true, $deleted = true)
    {
        $ret = parent::retrieve($id, $encode, $deleted);

        //CurrentUserApi needs a consistent timestamp/format of the data modified for hash purposes.
        $this->hashTS = $this->fetched_row['date_modified'] ?? null;

        if ($ret) {
            if (isset($_SESSION)) {
                $this->loadPreferences();
            }

            // make sure that the picture actually exists
            SugarAutoLoader::requireWithCustom('include/download_file.php');
            $download_file = new DownloadFile();
            if (!empty($ret->picture) && !file_exists($download_file->getFilePathFromId($ret->picture))) {
                $ret->picture = '';
            }

            $this->populateEmailCredentials();
        }

        // record old license type for detecting license type modification
        if (!empty($this->license_type)) {
            $this->oldLicenseType = $this->license_type;
        }

        // Record old status for detecting status modification
        if (!empty($this->status)) {
            $this->oldStatus = $this->status;
        }

        if (is_null($this->is_group)) {
            $this->is_group = 0;
        }
        return $ret;
    }

    /**
     * Loads information about the User's system override OutboundEmail
     * configuration
     */
    public function populateEmailCredentials()
    {
        $systemOutboundEmail = BeanFactory::newBean('OutboundEmail');
        $systemOutboundEmail = $systemOutboundEmail->getSystemMailerSettings();
        if (!$systemOutboundEmail->isAllowUserAccessToSystemDefaultOutbound()) {
            $userOverrideOE = $systemOutboundEmail->getUsersMailerForSystemOverride($this->id);
            if ($userOverrideOE != null) {
                $this->mail_credentials = json_encode([
                    'mail_smtpserver' => $systemOutboundEmail->mail_smtpserver,
                    'mail_authtype' => $userOverrideOE->mail_authtype,
                    'mail_smtpuser' => $userOverrideOE->mail_smtpuser,
                    'mail_smtptype' => $userOverrideOE->mail_smtptype,
                    'eapm_id' => $userOverrideOE->eapm_id,
                    'authorized_account' => $userOverrideOE->authorized_account,
                ]);
            }
        }
    }

    /**
     * retrieve user by email
     * @param $email
     * @return User|null
     */
    public function retrieve_by_email_address($email)
    {
        $query = 'SELECT u.id FROM users u
                  INNER JOIN email_addr_bean_rel eabr ON eabr.bean_id = u.id
                  INNER JOIN email_addresses ea ON ea.id = eabr.email_address_id
                  WHERE ea.email_address_caps = ? AND eabr.bean_module = ? AND ea.deleted = 0 AND eabr.deleted = 0 ';
        $stmt = $this->db->getConnection()->executeQuery(
            $query,
            [sugarStrToUpper($email), $this->module_name]
        );
        $id = $stmt->fetchOne();
        // retrieve returns User or null so keep null instead of FALSE for compatibility
        return $id ? $this->retrieve($id) : null;
    }

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    /**
     * Generate a new hash from plaintext password
     * @param string $password
     * @return string
     */
    public static function getPasswordHash($password)
    {
        return Hash::getInstance()->hash($password);
    }

    /**
     * Check that password matches existing hash
     * @param string $password Plaintext password
     * @param string $user_hash DB hash
     * @return boolean
     */
    public static function checkPassword($password, $user_hash)
    {
        return Hash::getInstance()->verify($password, $user_hash);
    }

    /**
     * Check that md5-encoded password matches existing hash
     * @param string $password MD5-encoded password
     * @param string $user_hash DB hash
     * @return boolean
     */
    public static function checkPasswordMD5($password, $user_hash)
    {
        return Hash::getInstance()->verifyMd5($password, $user_hash);
    }

    /**
     * Find user with matching password
     * @param string $name Username
     * @param string $password MD5-encoded password
     * @param string $where Limiting query
     * @return the matching User of false if not found
     */
    public static function findUserPassword($name, $password, $where = '')
    {
        global $db;
        $name = $db->quote($name);
        $query = "SELECT * from users where user_name='$name'";
        if (!empty($where)) {
            $query .= " AND $where";
        }
        $result = $db->limitQuery($query, 0, 1, false);
        if (!empty($result)) {
            $row = $db->fetchByAssoc($result);
            if (self::checkPasswordMD5($password, $row['user_hash'])) {
                return $row;
            }
        }
        return false;
    }

    /**
     * return user data by name with password check
     * @param $name
     * @param $password
     * @return NULL|array
     */
    public static function getUserDataByNameAndPassword($name, $password)
    {
        $qb = DBManagerFactory::getConnection()->createQueryBuilder();
        $qb->select('*')
            ->from('users')
            ->where($qb->expr()->eq('user_name', $qb->createPositionalParameter($name)))
            ->andWhere($qb->expr()->eq('status', "'Active'"))
            ->setMaxResults(1);

        $data = $qb->execute()->fetchAssociative();
        if ($data && self::checkPasswordMD5($password, $data['user_hash'])) {
            return $data;
        } else {
            return null;
        }
    }

    /**
     * Sets new password and resets password expiration timers
     * @param string $new_password
     */
    public function setNewPassword($new_password, $system_generated = '0')
    {
        $user_hash = self::getPasswordHash($new_password);
        $this->setPreference('loginexpiration', '0');
        $this->setPreference('lockout', '');
        $this->setPreference('loginfailed', '0');
        $this->savePreferencesToDB();
        //set new password
        $now = TimeDate::getInstance()->nowDb();
        $query =
            "UPDATE $this->table_name " .
            "SET user_hash={$this->db->quoted($user_hash)}, " .
            " system_generated_password={$this->db->quoted($system_generated)}, " .
            " pwd_last_changed={$this->db->quoted($now)}, date_modified={$this->db->quoted($now)} " .
            "WHERE id={$this->db->quoted($this->id)}";
        $this->db->query($query, true, "Error setting new password for $this->user_name: ");
        $_SESSION['hasExpiredPassword'] = '0';
    }

    /**
     * Attempt to rehash the current user_hash value
     * @param string $password Clear text password
     */
    public function rehashPassword($password)
    {
        if (empty($this->id) || empty($this->user_hash) || empty($password)) {
            return;
        }

        $hashBackend = Hash::getInstance();

        if ($hashBackend->needsRehash($this->user_hash)) {
            if ($newHash = $hashBackend->hash($password)) {
                $this->db->updateParams(
                    $this->table_name,
                    $this->field_defs,
                    ['user_hash' => $newHash],
                    ['id' => $this->id]
                );
                $GLOBALS['log']->info("Rehashed password for user id '{$this->id}'");
            } else {
                $GLOBALS['log']->warn("Error trying to rehash password for user id '{$this->id}'");
            }
        }
    }

    /**
     * Verify that the current password is correct and write the new password to the DB.
     *
     * @param string $user_password - Must be non null and at least 1 character.
     * @param string $new_password - Must be non null and at least 1 character.
     * @param string $system_generated
     * @return boolean - If passwords pass verification and query succeeds, return true, else return false.
     */
    public function change_password($user_password, $new_password, $system_generated = '0')
    {
        global $current_language;
        global $current_user;
        $mod_strings = return_module_language($current_language, 'Users');
        $GLOBALS['log']->debug("Starting password change for $this->user_name");

        if (!isset($new_password) || $new_password == '') {
            $this->error_string = $mod_strings['ERR_PASSWORD_CHANGE_FAILED_1'] . $current_user->user_name . $mod_strings['ERR_PASSWORD_CHANGE_FAILED_2'];
            return false;
        }

        // Check new password against rules set by admin
        if (!$this->check_password_rules($new_password)) {
            $this->error_string = $mod_strings['ERR_PASSWORD_CHANGE_FAILED_1'] . $current_user->user_name . $mod_strings['ERR_PASSWORD_CHANGE_FAILED_3'];
            return false;
        }

        if (!$current_user->isAdminForModule('Users')) {
            //check old password first
            $row = self::getUserDataByNameAndPassword($this->user_name, md5($user_password));
            if (empty($row)) {
                $GLOBALS['log']->warn('Incorrect old password for ' . $this->user_name . '');
                $this->error_string = $mod_strings['ERR_PASSWORD_INCORRECT_OLD_1'] . $this->user_name . $mod_strings['ERR_PASSWORD_INCORRECT_OLD_2'];
                return false;
            }
        }

        $this->setNewPassword($new_password, $system_generated);
        return true;
    }

    /**
     * Check new password against rules set by admin
     * @param string $password
     * @return boolean
     */
    public function check_password_rules($password)
    {
        $length = mb_strlen($password);

        // Min length
        if (!empty($GLOBALS['sugar_config']['passwordsetting']['minpwdlength']) && $GLOBALS['sugar_config']['passwordsetting']['minpwdlength'] > 0 && $length < $GLOBALS['sugar_config']['passwordsetting']['minpwdlength']) {
            return false;
        }

        // Max length
        if (!empty($GLOBALS['sugar_config']['passwordsetting']['maxpwdlength']) && $GLOBALS['sugar_config']['passwordsetting']['maxpwdlength'] > 0 && $length > $GLOBALS['sugar_config']['passwordsetting']['maxpwdlength']) {
            return false;
        }

        // One lower case
        if (!empty($GLOBALS['sugar_config']['passwordsetting']['onelower']) && !preg_match('/[a-z]+/', $password)) {
            return false;
        }

        // One upper case
        if (!empty($GLOBALS['sugar_config']['passwordsetting']['oneupper']) && !preg_match('/[A-Z]+/', $password)) {
            return false;
        }

        // One number
        if (!empty($GLOBALS['sugar_config']['passwordsetting']['onenumber']) && !preg_match('/[0-9]+/', $password)) {
            return false;
        }

        // One special character
        if (!empty($GLOBALS['sugar_config']['passwordsetting']['onespecial']) && !preg_match('/[|}{~!@#$%^&*()_+=-]+/', $password)) {
            return false;
        }

        // Custom regex
        if (!empty($GLOBALS['sugar_config']['passwordsetting']['customregex']) &&
            preg_match('/' . $GLOBALS['sugar_config']['passwordsetting']['customregex'] . '/', $password)) {
            return false;
        }
        return true;
    }

    public function is_authenticated()
    {
        return $this->authenticated;
    }

    public function fill_in_additional_list_fields()
    {
        $this->fill_in_additional_detail_fields();
    }

    public function fill_in_additional_detail_fields()
    {
        // jmorais@dri Bug #56269
        parent::fill_in_additional_detail_fields();
        // ~jmorais@dri

        // Must set team_id for team widget purposes (default_team is primary team id)
        if (empty($this->team_id)) {
            $this->team_id = $this->default_team;
        }

        //set the team info if the team id has already been set.
        //running only if team class exists will prevent breakage during upgrade/flavor conversions
        if (class_exists('Team')) {
            // Set default_team_name for Campaigns WebToLeadCreation
            $this->default_team_name = Team::getTeamName($this->team_id);
        } else {
            //if no team id exists, set the team info to blank
            $this->default_team = '';
            $this->default_team_name = '';
            $this->team_set_id = '';
        }

        $this->_create_proper_name_field();
    }

    public function retrieve_user_id($user_name)
    {
        $userFocus = BeanFactory::newBean('Users');
        $userFocus->retrieve_by_string_fields(['user_name' => $user_name]);
        if (empty($userFocus->id)) {
            return false;
        }

        return $userFocus->id;
    }

    /**
     * @return bool -- returns a list of all users in the system.
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     * @throws SugarApiExceptionNotAuthorized - If coming from an API entry point and
     * creating a duplicate user_name or when a user reports to himself.
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionMissingParameter
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Doctrine\DBAL\Exception
     */
    public function verify_data($ieVerified = true)
    {
        global $mod_strings, $current_user;
        $verified = true;

        $conn = $this->db->getConnection();
        if (!empty($this->id)) {
            // Make sure the user doesn't report to themselves.
            $reports_to_self = 0;
            $check_user = $this->reports_to_id;
            $already_seen_list = [];
            if (!empty($check_user)) {
                $query = 'SELECT reports_to_id
                    FROM users
                    WHERE id = ?';
                while (!empty($check_user)) {
                    if (isset($already_seen_list[$check_user])) {
                        // This user doesn't actually report to themselves
                        // But someone above them does.
                        $reports_to_self = 1;
                        break;
                    }
                    if ($check_user == $this->id) {
                        $reports_to_self = 1;
                        break;
                    }
                    $already_seen_list[$check_user] = 1;
                    $result = $conn->executeQuery($query, [$check_user]);
                    $check_user = $result->fetchOne();
                }
            }

            if ($reports_to_self == 1) {
                $this->error_string .= $mod_strings['ERR_REPORT_LOOP'];
                $verified = false;
                // Due to the amount of legacy code and no clear separation between logic and presentation layers, this
                // is a temporary fix to make sure that users don't report to themselves under API flows.
                if (isFromApi()) {
                    throw new SugarApiExceptionNotAuthorized('ERR_REPORT_LOOP', null, $this->module_name);
                }
            }
        }

        $qb = $conn->createQueryBuilder();
        $query = $qb->select('user_name')
            ->from($this->table_name)
            ->where($qb->expr()->eq('user_name', $qb->createPositionalParameter($this->user_name)))
            ->andWhere('deleted = 0');
        if (!empty($this->id)) {
            $query->andWhere($qb->expr()->neq('id', $qb->createPositionalParameter($this->id)));
        }
        $stmt = $query->execute();
        $dup_users = $stmt->fetchAssociative();

        if (!empty($dup_users)) {
            // Due to the amount of legacy code and no clear separation between logic and presentation layers, this is
            // a temporary fix in order to make sure that duplicate users are not created under API flows.
            if (isFromApi()) {
                throw new SugarApiExceptionNotAuthorized('ERR_USER_NAME_EXISTS', [$this->user_name], $this->module_name);
            }
            $error = string_format(translate('ERR_USER_NAME_EXISTS', $this->module_name), [$this->user_name]);
            $this->error_string .= $error;
            $this->isDuplicateRecord = true;
            $verified = false;
        }
        // license type check
        $licenseType = $this->processLicenseTypes($this->license_type, false);
        if (!empty($licenseType)
            && $this->validateLicenseTypes($licenseType)
            && !$this->hasMangoLicenseTypes($this->license_type)) {
            $types = [];
            foreach ($licenseType as $type) {
                $types[] = static::getLicenseTypeDescription($type);
            }
            $errorMessage = string_format(
                translate('ERR_USER_MISSING_LICENSE_TYPE', $this->module_name),
                [implode(', ', $types)]
            );
            if (isFromApi()) {
                throw new SugarApiExceptionMissingParameter($errorMessage);
            }
            $this->error_string .= $errorMessage;
            $verified = false;
        }

        // check non CRM license types if are already offered in a Bundle
        if (!empty($this->getNonCrmTypesArePartOfSelectedBundle($licenseType))) {
            if (isFromApi()) {
                throw new SugarApiExceptionInvalidParameter('ERR_USER_LICENSE_TYPE_OFFERRED_IN_BUNDLES');
            }
            // dot set $verified to false, we still need to save the value
            $_SESSION['user_save_error'] = translate('ERR_USER_LICENSE_TYPE_OFFERRED_IN_BUNDLES', $this->module_name);
        }

        if (is_admin($current_user)) {
            $query = 'SELECT COUNT(*) AS c FROM users WHERE is_admin = 1 AND deleted = 0';
            $remaining_admins = $conn->fetchOne($query);

            if (($remaining_admins <= 1) && ($this->is_admin != '1') && ($this->id == $current_user->id)) {
                $GLOBALS['log']->debug("Number of remaining administrator accounts: {$remaining_admins}");
                $this->error_string .= $mod_strings['ERR_LAST_ADMIN_1'] . $this->user_name . $mod_strings['ERR_LAST_ADMIN_2'];
                $verified = false;
            }
        }
        ///////////////////////////////////////////////////////////////////////
        ////	InboundEmail verification failure
        if (!$ieVerified) {
            $verified = false;
            $this->error_string .= '<br />' . $mod_strings['ERR_EMAIL_NO_OPTS'];
        }

        return $verified;
    }

    public function get_list_view_data($filter_fields = [])
    {
        $user_fields = parent::get_list_view_data();

        if ($this->is_admin) {
            $user_fields['IS_ADMIN_IMAGE'] = SugarThemeRegistry::current()->getImage('check_inline', '', null, null, '.gif', translate('LBL_CHECKMARK', 'Users'));
        } elseif (!$this->is_admin) {
            $user_fields['IS_ADMIN'] = '';
        }
        if ($this->is_group) {
            $user_fields['IS_GROUP_IMAGE'] = SugarThemeRegistry::current()->getImage('check_inline', '', null, null, '.gif', translate('LBL_CHECKMARK', 'Users'));
        } else {
            $user_fields['IS_GROUP_IMAGE'] = '';
        }


        if ($this->is_admin) {
            $user_fields['IS_ADMIN_IMAGE'] = SugarThemeRegistry::current()->getImage('check_inline', '', null, null, '.gif', translate('LBL_CHECKMARK', 'Users'));
        } elseif (!$this->is_admin) {
            $user_fields['IS_ADMIN'] = '';
        }

        if ($this->is_group) {
            $user_fields['IS_GROUP_IMAGE'] = SugarThemeRegistry::current()->getImage('check_inline', '', null, null, '.gif', translate('LBL_CHECKMARK', 'Users'));
        } else {
            $user_fields['NAME'] = empty($this->name) ? '' : $this->name;
        }

        $user_fields['REPORTS_TO_NAME'] = $this->reports_to_name;

        if (isset($_REQUEST['module']) && $_REQUEST['module'] == 'Teams' &&
            (isset($_REQUEST['record']) && !empty($_REQUEST['record']))) {
            $query = 'SELECT COUNT(*) c FROM team_memberships WHERE deleted=0 AND user_id = ? AND team_id = ? AND explicit_assign = 1';
            $conn = $this->db->getConnection();
            $stmt = $conn->executeQuery($query, [$this->id, $_REQUEST['record']]);
            $a = $stmt->fetchAssociative();

            $user_fields['UPLINE'] = translate('LBL_TEAM_UPLINE', 'Users');

            if ($a['c'] > 0) {
                $user_fields['UPLINE'] = translate('LBL_TEAM_UPLINE_EXPLICIT', 'Users');
            }
        }

        // processing license type data
        $user_fields['LICENSE_TYPE'] = $this->getLicenseTypesDescriptionString();

        return $user_fields;
    }

    public function list_view_parse_additional_sections(&$list_form, $xTemplateSection = null)
    {
        return $list_form;
    }


    /**
     * returns the private team_id of the user, or if an ID is passed, of the
     * target user
     * @param id guid
     * @return string
     */
    public function getPrivateTeam($id = '')
    {
        if (!empty($id)) {
            $user = BeanFactory::getBean('Users', $id);
            return $user->getPrivateTeamID();
        }
        return $this->getPrivateTeamID();
    }


    public function get_my_teams($return_obj = false)
    {

        $query = 'SELECT DISTINCT rel.team_id, teams.name, teams.name_2, rel.implicit_assign' .
            ' FROM team_memberships rel RIGHT JOIN teams ON (rel.team_id = teams.id)' .
            ' WHERE rel.user_id = ? AND rel.deleted = 0 ORDER BY teams.name ASC';
        $stmt = $this->db->getConnection()->executeQuery($query, [$this->id]);

        $out = [];
        $x = 0;
        while ($row = $stmt->fetchAssociative()) {
            if ($return_obj) {
                $out[$x] = BeanFactory::newBean('Teams');
                $out[$x]->retrieve($row['team_id']);
                $out[$x++]->implicit_assign = $row['implicit_assign'];
            } else {
                $out[$row['team_id']] = Team::getDisplayName($row['name'], $row['name_2']);
            }
        }

        return $out;
    }

    public function getAllTeams()
    {
        $q = 'SELECT id, name FROM teams WHERE private = 0 AND deleted = 0';
        $r = $this->db->query($q);

        $ret = [];
        while ($a = $this->db->fetchByAssoc($r)) {
            $ret[$a['id']] = $a['name'];
        }

        return $ret;
    }

    /**
     * When the user's reports to id is changed, this method is called.  This method needs to remove all
     * of the implicit assignements that were created based on this user, then recreated all of the implicit
     * assignments in the new location
     */
    public function update_team_memberships($old_reports_to_id)
    {

        $team = BeanFactory::newBean('Teams');
        $team->user_manager_changed($this->id, $old_reports_to_id, $this->reports_to_id);
    }


    /**
     * getAllUsers
     *
     * Returns all active and inactive users
     * @return Array of all users in the system
     */

    public static function getAllUsers()
    {
        $active_users = get_user_array(false);
        $inactive_users = get_user_array(false, 'Inactive');
        $result = $active_users + $inactive_users;
        asort($result);
        return $result;
    }


    public function getPreferredEmail()
    {
        $ret = [];
        $nameEmail = $this->getUsersNameAndEmail();
        $prefAddr = $nameEmail['email'];
        $fullName = $nameEmail['name'];
        if (empty($prefAddr)) {
            $nameEmail = $this->getSystemDefaultNameAndEmail();
            $prefAddr = $nameEmail['email'];
            $fullName = $nameEmail['name'];
        } // if
        $fullName = from_html($fullName);
        $ret['name'] = $fullName;
        $ret['email'] = $prefAddr;
        return $ret;
    }

    public function getUsersNameAndEmail()
    {
        // Bug #48555 Not User Name Format of User's locale.
        $this->_create_proper_name_field();

        $prefAddr = $this->emailAddress->getPrimaryAddress($this);

        if (empty($prefAddr)) {
            $prefAddr = $this->emailAddress->getReplyToAddress($this);
        }
        return ['email' => $prefAddr, 'name' => $this->name];
    } // fn

    public function getSystemDefaultNameAndEmail()
    {

        $email = BeanFactory::newBean('Emails');
        $return = $email->getSystemDefaultEmail();
        $prefAddr = $return['email'];
        $fullName = $return['name'];
        return ['email' => $prefAddr, 'name' => $fullName];
    } // fn

    /**
     * sets User email default in config.php if not already set by install - i.
     * e., upgrades
     */
    public function setDefaultsInConfig()
    {
        global $sugar_config;
        $sugar_config['email_default_client'] = 'sugar';
        $sugar_config['email_default_editor'] = 'html';
        ksort($sugar_config);
        write_array_to_file('sugar_config', $sugar_config, 'config.php');
        return $sugar_config;
    }

    /**
     * returns User's email address based on descending order of preferences
     *
     * @param string id GUID of target user if needed
     * @return array Assoc array for an email and name
     */
    public function getEmailInfo($id = '')
    {
        $ret = [];
        $user = $this;
        if (!empty($id)) {
            $user = BeanFactory::getBean('Users', $id);
        }

        // from name
        $fromName = $user->getPreference('mail_fromname');
        if (empty($fromName)) {
            // cn: bug 8586 - localized name format
            $fromName = $user->full_name;
        }

        // from address
        $fromaddr = $user->getPreference('mail_fromaddress');
        if (empty($fromaddr)) {
            if (!empty($user->email1) && isset($user->email1)) {
                $fromaddr = $user->email1;
            } elseif (!empty($user->email2) && isset($user->email2)) {
                $fromaddr = $user->email2;
            } else {
                $r = $user->db->query("SELECT value FROM config WHERE name = 'fromaddress'");
                $a = $user->db->fetchByAssoc($r);
                $fromddr = $a['value'];
            }
        }

        $ret['name'] = $fromName;
        $ret['email'] = $fromaddr;

        return $ret;
    }

    /**
     * Get the string representing the user's preferred email client.
     *
     * @return string
     */
    public function getEmailClientPreference()
    {
        if (!isset($GLOBALS['sugar_config']['email_default_client'])) {
            $this->setDefaultsInConfig();
        }

        $clientPref = $this->getPreference('email_link_type');
        $client = (!empty($clientPref)) ? $clientPref : $GLOBALS['sugar_config']['email_default_client'];

        // check for presence of a mobile device, if so use its email client
        if (isset($_SESSION['isMobile'])) {
            $client = 'other';
        }

        return $client;
    }

    /**
     * returns opening <a href=xxxx for a contact, account, etc
     * cascades from User set preference to System-wide default
     * @param attribute the email addy
     * @param focus the parent bean
     * @param contact_id
     * @param return_module
     * @param return_action
     * @param return_id
     * @param class
     * @return string   link
     */
    public function getEmailLink2($emailAddress, &$focus, $contact_id = '', $ret_module = '', $ret_action = 'DetailView', $ret_id = '', $class = '')
    {
        $emailLink = '';
        $client = $this->getEmailClientPreference();

        if ($client === 'sugar' && ACLController::checkAccess('Emails', 'edit')) {
            $email = '';
            $to_addrs_ids = '';
            $to_addrs_names = '';
            $to_addrs_emails = '';

            $fullName = !empty($focus->name) ? $focus->name : '';

            if (empty($ret_module)) {
                $ret_module = $focus->module_dir;
            }
            if (empty($ret_id)) {
                $ret_id = $focus->id;
            }
            if ($focus->object_name == 'Contact') {
                $contact_id = $focus->id;
                $to_addrs_ids = $focus->id;
                // Bug #48555 Not User Name Format of User's locale.
                $focus->_create_proper_name_field();
                $fullName = $focus->name;
                $to_addrs_names = $fullName;
                $to_addrs_emails = $focus->email1;
            }

            $emailLinkUrl = 'contact_id=' . $contact_id .
                '&parent_type=' . $focus->module_dir .
                '&parent_id=' . $focus->id .
                '&parent_name=' . urlencode($fullName) .
                '&to_addrs_ids=' . $to_addrs_ids .
                '&to_addrs_names=' . urlencode($to_addrs_names) .
                '&to_addrs_emails=' . urlencode($to_addrs_emails) .
                '&to_email_addrs=' . urlencode($fullName . '&nbsp;&lt;' . $emailAddress . '&gt;') .
                '&return_module=' . $ret_module .
                '&return_action=' . $ret_action .
                '&return_id=' . $ret_id;

            $eUi = new EmailUI();
            $j_quickComposeOptions = $eUi->generateComposePackageForQuickCreateFromComposeUrl($emailLinkUrl, true);

            $emailLink = "<a href='javascript:void(0);' onclick='SUGAR.quickCompose.init($j_quickComposeOptions);' class='$class'>";
        } else {
            // straight mailto:
            $emailLink = '<a href="mailto:' . $emailAddress . '" class="' . $class . '">';
        }

        return $emailLink;
    }

    /**
     * returns opening <a href=xxxx for a contact, account, etc
     * cascades from User set preference to System-wide default
     * @param attribute the email addy
     * @param focus the parent bean
     * @param contact_id
     * @param return_module
     * @param return_action
     * @param return_id
     * @param class
     * @return string    link
     */
    public function getEmailLink(
        $attribute,
        &$focus,
        $contact_id = '',
        $ret_module = '',
        $ret_action = 'DetailView',
        $ret_id = '',
        $class = ''
    ) {

        $emailLink = '';
        $client = $this->getEmailClientPreference();

        if ($client === 'sugar' && ACLController::checkAccess('Emails', 'edit')) {
            $email = '';
            $to_addrs_ids = '';
            $to_addrs_names = '';
            $to_addrs_emails = '';

            $fullName = !empty($focus->name) ? $focus->name : '';

            if (!empty($focus->$attribute)) {
                $email = $focus->$attribute;
            }


            if (empty($ret_module)) {
                $ret_module = $focus->module_dir;
            }
            if (empty($ret_id)) {
                $ret_id = $focus->id;
            }
            if ($focus->object_name == 'Contact') {
                // Bug #48555 Not User Name Format of User's locale.
                $focus->_create_proper_name_field();
                $fullName = $focus->name;
                $contact_id = $focus->id;
                $to_addrs_ids = $focus->id;
                $to_addrs_names = $fullName;
                $to_addrs_emails = $focus->email1;
            }

            $emailLinkUrl = 'contact_id=' . $contact_id .
                '&parent_type=' . $focus->module_dir .
                '&parent_id=' . $focus->id .
                '&parent_name=' . urlencode($fullName) .
                '&to_addrs_ids=' . $to_addrs_ids .
                '&to_addrs_names=' . urlencode($to_addrs_names) .
                '&to_addrs_emails=' . urlencode($to_addrs_emails) .
                '&to_email_addrs=' . urlencode($fullName . '&nbsp;&lt;' . $email . '&gt;') .
                '&return_module=' . $ret_module .
                '&return_action=' . $ret_action .
                '&return_id=' . $ret_id;

            //Generate the compose package for the quick create options.
            $eUi = new EmailUI();
            $j_quickComposeOptions = $eUi->generateComposePackageForQuickCreateFromComposeUrl($emailLinkUrl, true);
            $emailLink = "<a href='javascript:void(0);' onclick='SUGAR.quickCompose.init($j_quickComposeOptions);' class='$class'>";
        } else {
            // straight mailto:
            $emailLink = '<a href="mailto:' . $focus->$attribute . '" class="' . $class . '">';
        }

        return $emailLink;
    }


    /**
     * gets a human-readable explanation of the format macro
     * @return string Human readable name format
     */
    public function getLocaleFormatDesc()
    {
        $format = [];
        $name = [];
        global $locale;
        global $mod_strings;
        global $app_strings;

        $format['f'] = $mod_strings['LBL_LOCALE_DESC_FIRST'];
        $format['l'] = $mod_strings['LBL_LOCALE_DESC_LAST'];
        $format['s'] = $mod_strings['LBL_LOCALE_DESC_SALUTATION'];
        $format['t'] = $mod_strings['LBL_LOCALE_DESC_TITLE'];

        $name['f'] = $app_strings['LBL_LOCALE_NAME_EXAMPLE_FIRST'];
        $name['l'] = $app_strings['LBL_LOCALE_NAME_EXAMPLE_LAST'];
        $name['s'] = $app_strings['LBL_LOCALE_NAME_EXAMPLE_SALUTATION'];
        $name['t'] = $app_strings['LBL_LOCALE_NAME_EXAMPLE_TITLE'];

        $macro = $locale->getLocaleFormatMacro($this);

        $ret1 = '';
        $ret2 = '';
        for ($i = 0; $i < strlen($macro); $i++) {
            if (array_key_exists($macro[$i], $format)) {
                $ret1 .= '<i>' . $format[$macro[$i]] . '</i>';
                $ret2 .= '<i>' . $name[$macro[$i]] . '</i>';
            } else {
                $ret1 .= $macro[$i];
                $ret2 .= $macro[$i];
            }
        }
        return $ret1 . '<br />' . $ret2;
    }


    public function getPrivateTeamID()
    {
        return self::staticGetPrivateTeamID($this->id);
    }

    public static function staticGetPrivateTeamID($user_id)
    {
        $conn = DBManagerFactory::getConnection();
        $query = $conn->getDatabasePlatform()->modifyLimitQuery(
            'SELECT id FROM teams WHERE associated_user_id = ? AND deleted = 0',
            1
        );
        return $conn->executeQuery($query, [$user_id])->fetchOne() ?: null;
    }
    /*
     *
     * Here are the multi level admin access check functions.
     *
     */
    /**
     * Helper function to remap some modules around ACL wise
     *
     * @return string
     */
    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    protected function _fixupModuleForACL($module)
    {
        if ($module == 'ContractTypes') {
            $module = 'Contracts';
        }
        static $productModules = [
            'ProductBundles',
            'ProductBundleNotes',
            'ProductTemplates',
            'ProductTypes',
            'ProductCategories',
        ];
        if (in_array($module, $productModules)) {
            $module = 'Products';
        }

        return $module;
    }
    /**
     * Helper function that enumerates the list of modules and checks if they are an admin/dev.
     * The code was just too similar to copy and paste.
     *
     * @return array
     */
    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    protected function _getModulesForACL($type = 'dev')
    {
        $isDev = $type == 'dev';
        $isAdmin = $type == 'admin';

        global $beanList;
        $myModules = [];

        if (!is_array($beanList)) {
            return $myModules;
        }

        $actions = ACLAction::getUserActions($this->id);

        foreach ($beanList as $module => $val) {
            // Remap the module name
            $module = $this->_fixupModuleForACL($module);
            if (in_array($module, $myModules)) {
                // Already have the module in the list
                continue;
            }

            if (in_array($module, static::$ignoredModuleList)) {
                // You can't develop on these modules.
                continue;
            }

            $key = 'module';
            // The tracker modules have special case ACL mappings
            // in $GLOBALS['ACLActions'] that we need to account for.
            // TODO: In the future these should be migrated to a custom ACL strategy for those modules.
            if (in_array($module, ['Tracker', 'TrackerPerfs', 'TrackerQueries', 'TrackerSessions'])) {
                $focus = BeanFactory::newBean($module);
                if ($focus instanceof SugarBean) {
                    $key = $focus->acltype;
                }
            }

            if (($this->isAdmin() && isset($actions[$module][$key]))
                || (isset($actions[$module][$key]['admin']['aclaccess']) &&
                    (($isDev && $actions[$module][$key]['admin']['aclaccess'] == ACL_ALLOW_DEV) ||
                        ($isAdmin && $actions[$module][$key]['admin']['aclaccess'] == ACL_ALLOW_ADMIN) ||
                        $actions[$module][$key]['admin']['aclaccess'] == ACL_ALLOW_ADMIN_DEV))
            ) {
                $myModules[] = $module;
            }
        }

        return $myModules;
    }

    /**
     * Is this user a system wide admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        if (isset($this->is_admin)
            && ($this->is_admin == '1' || $this->is_admin === 'on')) {
            return true;
        }
        return false;
    }

    /**
     * Is this user a developer for any module
     *
     * @return bool
     */
    public function isDeveloperForAnyModule()
    {
        if (empty($this->id)) {
            // empty user is no developer
            return false;
        }
        if ($this->isAdmin()) {
            return true;
        }
        if (safeCount($this->getDeveloperModules()) > 0) {
            return true;
        }
        return false;
    }

    /**
     * List the modules a user has developer access to
     *
     * @return array
     */
    public function getDeveloperModules()
    {
        if ($this->id === null) {
            return [];
        }
        $cache = Container::getInstance()->get(AclCacheInterface::class);
        $modules = $cache->retrieve($this->id, 'developer_modules');
        if ($modules === null) {
            $modules = $this->_getModulesForACL('dev');
            $cache->store($this->id, 'developer_modules', $modules);
        }

        return $modules;
    }

    /**
     * Is this user a developer for the specified module
     *
     * @return bool
     */
    public function isDeveloperForModule($module)
    {
        if (empty($this->id)) {
            // empty user is no developer
            return false;
        }
        if ($this->isAdmin()) {
            return true;
        }

        $devModules = $this->getDeveloperModules();

        $module = $this->_fixupModuleForACL($module);
        if ($this->isWorkFlowModule($module) && safeCount($devModules) > 0) {
            return true;
        }

        if (in_array($module, $devModules)) {
            return true;
        }

        return false;
    }

    /**
     * List the modules a user has admin access to
     *
     * @return array
     */
    public function getAdminModules()
    {
        if ($this->id === null) {
            return [];
        }
        $cache = Container::getInstance()->get(AclCacheInterface::class);
        $modules = $cache->retrieve($this->id, 'admin_modules');
        if ($modules === null) {
            $modules = $this->_getModulesForACL('admin');
            $cache->store($this->id, 'admin_modules', $modules);
        }

        return $modules;
    }

    /**
     * Is this user an admin for the specified module
     *
     * @return bool
     */
    public function isAdminForModule($module)
    {
        if (empty($this->id)) {
            // empty user is no admin
            return false;
        }
        if ($this->isAdmin()) {
            return true;
        }

        $adminModules = $this->getAdminModules();

        $module = $this->_fixupModuleForACL($module);
        if ($this->isWorkFlowModule($module) && safeCount($adminModules) > 0) {
            return true;
        }

        if (in_array($module, $adminModules)) {
            return true;
        }

        return false;
    }

    /**
     * Check if module is workflow-related
     *
     * @param string $module Module name
     * @return bool
     */
    protected function isWorkFlowModule($module)
    {
        switch ($module) {
            case 'Expressions':
            case 'WorkFlow':
            case 'WorkFlowActions':
            case 'WorkFlowActionShells':
            case 'WorkFlowAlerts':
            case 'WorkFlowAlertShells':
            case 'WorkFlowTriggerShells':
            case 'pmse_Project':
            case 'pmse_Inbox':
            case 'pmse_Emails_Templates':
            case 'pmse_Business_Rules':
                return true;
        }

        return false;
    }

    /**
     * Whether or not based on the user's locale if we should show the last name first.
     *
     * @return bool
     */
    public function showLastNameFirst()
    {
        global $locale;
        $localeFormat = $locale->getLocaleFormatMacro($this);
        if (strpos($localeFormat, 'l') > strpos($localeFormat, 'f')) {
            return false;
        } else {
            return true;
        }
    }

    public function create_new_list_query(
        $order_by,
        $where,
        $filter = [],
        $params = [],
        $show_deleted = 0,
        $join_type = '',
        $return_array = false,
        $parentbean = null,
        $singleSelect = false,
        $ifListForExport = false
    ) {
        //call parent method, specifying for array to be returned
        $ret_array = parent::create_new_list_query(
            $order_by,
            $where,
            $filter,
            $params,
            $show_deleted,
            $join_type,
            true,
            $parentbean,
            $singleSelect,
            $ifListForExport
        );

        //if this is being called from webservices, then run additional code
        if (!empty($GLOBALS['soap_server_object'])) {
            //if this is a single select, then secondary queries are being run that may result in duplicate rows being returned through the
            //left joins with meetings/tasks/call.  We need to change the left joins to include a null check (bug 40250)
            if ($singleSelect) {
                //retrieve the 'from' string and make lowercase for easier manipulation
                $left_str = strtolower($ret_array['from']);
                $lefts = explode('left join', $left_str);
                $new_left_str = '';

                //explode on the left joins and process each one
                foreach ($lefts as $ljVal) {
                    //grab the join alias
                    $onPos = strpos($ljVal, ' on');
                    if ($onPos === false) {
                        $new_left_str .= ' ' . $ljVal . ' ';
                        continue;
                    }
                    $spacePos = strrpos(substr($ljVal, 0, $onPos), ' ');
                    $alias = substr($ljVal, $spacePos, $onPos - $spacePos);

                    //add null check to end of the Join statement
                    // Bug #46390 to use id_c field instead of id field for custom tables
                    if (substr($alias, -5) != '_cstm') {
                        $ljVal = '  LEFT JOIN ' . $ljVal . ' and ' . $alias . '.id is null ';
                    } else {
                        $ljVal = '  LEFT JOIN ' . $ljVal . ' and ' . $alias . '.id_c is null ';
                    }

                    //add statement into new string
                    $new_left_str .= $ljVal;
                }
                //replace the old string with the new one
                $ret_array['from'] = $new_left_str;
            }
        }

        //return array or query string
        if ($return_array) {
            return $ret_array;
        }

        return $ret_array['select'] . $ret_array['from'] . $ret_array['where'] . $ret_array['order_by'];
    }

    /**
     * Get user first day of week.
     *
     * @param [User] $user user object, current user if not specified
     * @return int : 0 = Sunday, 1 = Monday, etc...
     */
    public function get_first_day_of_week()
    {
        $fdow = $this->getPreference('fdow');
        if (empty($fdow)) {
            $fdow = 0;
        }

        return $fdow;
    }

    /**
     * Method for password generation
     *
     * @static
     * @return string password
     */
    public static function generatePassword()
    {
        $res = $GLOBALS['sugar_config']['passwordsetting'];
        $charBKT = '';
        //chars to select from
        $LOWERCASE = 'abcdefghijklmnpqrstuvwxyz';
        $NUMBER = '0123456789';
        $UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $SPECIAL = '~!@#$%^&*()_+=-{}|';
        $condition = 0;
        $charBKT .= $UPPERCASE . $LOWERCASE . $NUMBER;
        $password = '';

        // Count the number of requirements
        if ($res['onenumber'] == '1') {
            $condition += 1;
        }
        if ($res['onelower'] == '1') {
            $condition += 1;
        }
        if ($res['oneupper'] == '1') {
            $condition += 1;
        }
        if ($res['onespecial'] == '1') {
            $condition += 1;
        }

        // if there is more requirements than the minimum length, minimum length= number of requirements
        $length = $res['minpwdlength'] <= $condition ? $condition : $res['minpwdlength'];
        if ($length < 6) {
            $length = '6';
        }

        // Create random characters for the ones that doesnt have requirements
        for ($i = 0; $i < $length - $condition; $i++) {  // loop and create password
            $password = $password . substr($charBKT, random_int(0, mt_getrandmax()) % strlen($charBKT), 1);
        }
        if ($res['onelower'] == '1') { // If a lower caracter is required, i add one in the password
            if (strlen($password) != '0') { // If there is other characters in the password, I insert one in a random position
                $password = substr_replace($password, substr($LOWERCASE, random_int(0, mt_getrandmax()) % strlen($LOWERCASE), 1), random_int(0, mt_getrandmax()) % strlen($password), 0);
            } else { // otherwise i put one in first position
                $password = $password . substr($LOWERCASE, random_int(0, mt_getrandmax()) % strlen($LOWERCASE), 1);
            }
        }
        if ($res['onenumber'] == '1') {
            if (strlen($password) != '0') {
                $password = substr_replace($password, substr($NUMBER, random_int(0, mt_getrandmax()) % strlen($NUMBER), 1), random_int(0, mt_getrandmax()) % strlen($password), 0);
            } else {
                $password = $password . substr($NUMBER, random_int(0, mt_getrandmax()) % strlen($NUMBER), 1);
            }
        }
        if ($res['oneupper'] == '1') {
            if (strlen($password) != '0') {
                $password = substr_replace($password, substr($UPPERCASE, random_int(0, mt_getrandmax()) % strlen($UPPERCASE), 1), random_int(0, mt_getrandmax()) % strlen($password), 0);
            } else {
                $password = $password . substr($UPPERCASE, random_int(0, mt_getrandmax()) % strlen($UPPERCASE), 1);
            }
        }
        if ($res['onespecial'] == '1') {
            if (strlen($password) != '0') {
                $password = substr_replace($password, substr($SPECIAL, random_int(0, mt_getrandmax()) % strlen($SPECIAL), 1), random_int(0, mt_getrandmax()) % strlen($password), 0);
            } else {
                $password = $password . substr($SPECIAL, random_int(0, mt_getrandmax()) % strlen($SPECIAL), 1);
            }
        }

        return $password;
    }

    /**
     * Send new password or link to user. Does not support HTML body due to security reasons.
     *
     * @param string $templateId Id of email template
     * @param array $additionalData additional params: link, url, password
     * @return array status: true|false, message: error message, if status = false and message = '' it means that send method has returned false
     */
    public function sendEmailForPassword($templateId, array $additionalData = [])
    {
        global $current_user,
        $app_strings;

        $mod_strings = return_module_language('', 'Users');

        $result = [
            'status' => false,
            'message' => '',
        ];

        $emailTemplate = BeanFactory::newBean('EmailTemplates');
        $emailTemplate->disable_row_level_security = true;

        if ($emailTemplate->retrieve($templateId) == '') {
            $result['message'] = $mod_strings['LBL_EMAIL_TEMPLATE_MISSING'];
            return $result;
        }

        $emailTemplate->body = $this->replaceInstanceVariablesInEmailTemplates($emailTemplate->body, $additionalData);

        try {
            $mailer = MailerFactory::getSystemDefaultMailer();

            // set the subject
            $mailer->setSubject($emailTemplate->subject);

            // set the plain-text body
            $mailer->setTextBody($emailTemplate->body);

            // make sure there is at least one message part (but only if the current user is an admin)...

            // even though $textBody is already set, resetting it verifies that $mailer actually got it
            $textBody = $mailer->getTextBody();

            if ($current_user->is_admin && !$mailer->hasMessagePart($textBody)) {
                throw new MailerException('No email body was provided', MailerException::InvalidMessageBody);
            }

            // get the recipient's email address
            $itemail = $this->emailAddress->getPrimaryAddress($this);

            if (!empty($itemail)) {
                // add the recipient
                $mailer->addRecipientsTo(new EmailIdentity($itemail));

                $emailId = create_guid();
                $mailer->setMessageId($emailId);

                // if send doesn't raise an exception then set the result status to true
                $mailer->send();
                $result['status'] = true;

                if (!isset($additionalData['link']) || $additionalData['link'] == false) {
                    $this->setNewPassword($additionalData['password'], '1');
                }
            } else {
                // this exception is ignored as part of the default case in the switch statement in the catch block
                // but it adds documentation as to what is happening
                throw new MailerException('There are no recipients', MailerException::FailedToSend);
            }
        } catch (MailerException $me) {
            switch ($me->getCode()) {
                case MailerException::FailedToConnectToRemoteServer:
                    if ($current_user->is_admin) {
                        // the smtp host may not be empty, but this is the best error message for now
                        $result['message'] = $mod_strings['ERR_SERVER_SMTP_EMPTY'];
                    } else {
                        // status=failed to send, but no message is returned to non-admin users
                    }

                    break;
                case MailerException::InvalidMessageBody:
                    // this exception will only be raised if the current user is an admin, so there is no need to
                    // worry about catching it in a non-admin case and handling the error message accordingly

                    // both the plain-text and HTML parts are empty, but this is the best error message for now
                    $result['message'] = $app_strings['LBL_EMAIL_TEMPLATE_EDIT_PLAIN_TEXT'];

                    break;
                default:
                    // status=failed to send, but no message is returned
                    break;
            }
        }

        return $result;
    }

    // Bug #48014 Must to send password to imported user if this action is required
    public function afterImportSave()
    {
        if ($this->user_hash == false
            && !$this->is_group
            && !$this->portal_only
            && isset($GLOBALS['sugar_config']['passwordsetting']['SystemGeneratedPasswordON'])
            && $GLOBALS['sugar_config']['passwordsetting']['SystemGeneratedPasswordON']
        ) {
            $backUpPost = $_POST;
            $_POST = [
                'userId' => $this->id,
            ];
            ob_start();
            require 'modules/Users/GeneratePassword.php';
            $result = ob_get_clean();
            $_POST = $backUpPost;
            return $result == true;
        }
    }

    /**
     * @static
     * This function to determine if a given user id is a manager.  A manager is defined as someone who has direct reports
     *
     * @param String user_id The id of the user to check
     * @param boolean include_deleted Boolean value indicating whether or not to include deleted records (defaults to FALSE)
     * @return boolean TRUE if user id is a manager; FALSE otherwise
     */
    public static function isManager($user_id, $include_deleted = false)
    {
        $qb = DBManagerFactory::getConnection()->createQueryBuilder();
        $expr = $qb->expr();
        $where = $expr->and(
            $expr->eq('reports_to_id', $qb->createPositionalParameter($user_id)),
            $expr->eq('status', $qb->createPositionalParameter('Active'))
        );
        if (!$include_deleted) {
            $where = $where->with($expr->eq('deleted', 0));
        }
        $count = $qb->select('count(id)')->from('users')->where($where)->execute()->fetchOne();
        return $count > 0;
    }

    /**
     * @static
     * This function returns an array of reportees and their corresponding reportee count, if additional_fields are
     * passed in, the return will contain the whole row vs just the key => total value pair that is returned when no
     * additional_fields are defined
     *
     * @param String $userId The id of the user to check
     * @param boolean $includeDeleted indicating whether or not to include deleted records (defaults to false)
     * @param array $additionalFields
     * @return array Array of reportee IDs and their leaf count
     */
    public static function getReporteesWithLeafCount($userId, $includeDeleted = false, $additionalFields = [])
    {
        $qb = DBManagerFactory::getConnection()->createQueryBuilder();

        $whereNonDeleted = $qb->expr()
            ->and(
                "u.status = 'Active'",
                'u.deleted = 0'
            );
        if (!$includeDeleted) {
            $whereCondition = $whereNonDeleted;
        } else {
            $whereDeleted = $qb->expr()
                ->and('u.deleted = 1');
            $whereCondition = $qb->expr()
                ->or($whereNonDeleted, $whereDeleted);
        }

        $qb->select(['u.id', 'sum(CASE WHEN u2.id IS NULL THEN 0 ELSE 1 END) total'])
            ->from('users', 'u')
            ->where($qb->expr()->eq('u.reports_to_id', $qb->createPositionalParameter($userId)))
            ->andWhere($whereCondition)
            ->groupBy('u.id');

        $joinWhereNonDeleted = $qb->expr()
            ->and(
                "u2.status = 'Active'",
                'u2.deleted = 0'
            );

        if (!$includeDeleted) {
            $joinWhereCondition = $joinWhereNonDeleted;
        } else {
            $joinWhereDeleted = $qb->expr()->and('u2.deleted = 1');
            $joinWhereCondition = $qb->expr()->or($joinWhereNonDeleted, $joinWhereDeleted);
        }

        $joinWhere = $qb->expr()->and(
            'u.id = u2.reports_to_id',
            $joinWhereCondition
        );

        $qb->leftJoin('u', 'users', 'u2', $joinWhere);

        foreach ($additionalFields as $field) {
            $qb->addSelect('u.' . $field);
            $qb->addGroupBy('u.' . $field);
        }

        $stmt = $qb->execute();

        $result = [];
        while ($row = $stmt->fetchAssociative()) {
            $result[$row['id']] = empty($additionalFields) ? $row['total'] : $row;
        }

        return $result;
    }

    /**
     * @static
     * This function returns an array of reportee IDs that are managers
     *
     * @param String user_id The id of the user to check
     * @param boolean included_deleted Boolean Value indicating whether or not to include deleted records (defaults to false)
     * @return array Array of manager reportee IDs
     */
    public static function getReporteeManagers($user_id, $include_deleted = false)
    {
        $returnArray = [];
        $reportees = User::getReporteesWithLeafCount($user_id, $include_deleted);
        foreach ($reportees as $key => $value) {
            if ($value > 0) {
                $returnArray[] = $key;
            }
        }
        return $returnArray;
    }

    /**
     * @static
     * This function returns an array of reportee IDs that are sales reps
     *
     * @param String user_id The id of the user to check
     * @param boolean included_deleted Boolean Value indicating whether or not to include deleted records (defaults to false)
     * @return array Array of rep reportee IDs
     */
    public static function getReporteeReps($user_id, $include_deleted = false)
    {
        $returnArray = [];
        $reportees = User::getReporteesWithLeafCount($user_id, $include_deleted);
        foreach ($reportees as $key => $value) {
            if ($value == 0) {
                $returnArray[] = $key;
            }
        }
        return $returnArray;
    }

    /**
     * @static
     * This function is used to determine if a given user id is a top level manager.  A top level manager is defined as someone
     * who has direct reports, but does not have to report to anyone (reports_to_id is null).
     *
     * This is functionally equivalent to User::isManager($user->id) && empty($user->reports_to_id)
     *
     * @param String user_id The id of the user to check
     * @param boolean include_deleted Boolean value indicating whether or not to include deleted records of reportees (defaults to FALSE)
     * @return boolean TRUE if user id is a top level manager; FALSE otherwise
     */
    public static function isTopLevelManager($user_id, $include_deleted = false)
    {
        if (User::isManager($user_id, $include_deleted)) {
            $stmt = DBManagerFactory::getConnection()->executeQuery(
                'SELECT reports_to_id FROM users WHERE id = ?',
                [$user_id]
            );
            $reports_to_id = $stmt->fetchOne();
            return empty($reports_to_id);
        }
        return false;
    }

    /**
     * {@inheritDoc}
     *
     * Special case override for Users module otherwise we incur the
     * unnecessary check for user_preferences field for all SugarBean instances.
     *
     * @todo loop through vardefs instead
     * @internal runs into an issue when populating from field_defs for users - corrupts user prefs
     */
    public function populateFromRow(array $row, $convert = false, $getMoreData = true)
    {
        unset($row['user_preferences']);
        return parent::populateFromRow($row, $convert);
    }

    /**
     * Replace instance variables in email templates for a particular message part.
     *
     * @param string $body required The plain-text or HTML part.
     * @param array $additionalData Additional parameters: link, url, password.
     * @return string
     */
    private function replaceInstanceVariablesInEmailTemplates($body, $additionalData = [])
    {
        global $sugar_config;

        if (isset($additionalData['link']) && $additionalData['link'] == true) {
            $body = str_replace('$contact_user_link_guid', $additionalData['url'], $body);
        } else {
            $body = str_replace('$contact_user_user_hash', $additionalData['password'], $body);
        }

        // Bug 36833 - Add replacing of special value $instance_url
        $body = str_replace('$config_site_url', $sugar_config['site_url'], $body);

        $body = str_replace('$contact_user_user_name', $this->user_name, $body);
        $usrTime = new TimeDate($this);
        $body = str_replace('$contact_user_pwd_last_changed', $usrTime->getNow(true)->asDb(false), $body);


        return $body;
    }

    /**
     * Returns a hash value of the User
     *
     * @return string The User hash value
     */
    public function getUserMDHash()
    {
        // Add the tab hash to include the change of tabs (e.g. module order) as a part of the user hash
        $tabs = new TabController();
        $tabHash = $tabs->getMySettingsTabHash();
        // User hash must depend on user wizard completion and ability for
        // users to set their number of pinned modules
        $isUserWizardCompleted = intval($this->shouldUserCompleteWizard());
        $pinnedModulesAllowed = intval($tabs->get_users_pinned_modules());
        return md5($this->id . $isUserWizardCompleted . $pinnedModulesAllowed . $this->hashTS . $tabHash);
    }

    public function setupSession()
    {
        if (!isset($_SESSION[$this->user_name . '_get_developer_modules_for_user'])) {
            $this->getDeveloperModules();
        }
        if (!isset($_SESSION[$this->user_name . '_get_admin_modules_for_user'])) {
            $this->getAdminModules();
        }
    }

    /**
     * Checks if the passed email is primary.
     *
     * @param string $email
     * @return bool Returns TRUE if the passed email is primary.
     */
    public function isPrimaryEmail($email)
    {
        if (!empty($this->email1) && !empty($email) && strcasecmp($this->email1, $email) == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Updates last_login field with current timestamp.
     * Executes User::save internally.
     *
     * @return void
     */
    public function updateLastLogin()
    {
        // need to call a direct db query
        // if we do not the email address is removed
        $this->last_login = TimeDate::getInstance()->nowDb();
        $this->db->updateParams(
            $this->table_name,
            $this->field_defs,
            ['last_login' => $this->last_login],
            ['id' => $this->id]
        );
        return $this->last_login;
    }

    //TODO Update to use global cache

    /**
     * This is a helper function to return an Array of users depending on the parameters passed into the function.
     * This function uses the get_register_value function by default to use a caching layer where supported.
     *
     * @param bool $add_blank Boolean value to add a blank entry to the array results, true by default
     * @param string $status String value indicating the status to filter users by, "Active" by default
     * @param string $user_id String value to specify a particular user id value (searches the id column of users table), blank by default
     * @param bool $use_real_name Boolean value indicating whether or not results should include the full name or just user_name, false by default
     * @param String $user_name_filter String value indicating the user_name filter (searches the user_name column of users table) to optionally search with, blank by default
     * @param string $portal_filter String query filter for portal users (defaults to searching non-portal users), change to blank if you wish to search for all users including portal users
     * @param bool $from_cache Boolean value indicating whether or not to use the get_register_value function for caching, true by default
     * @param array $order_by array of (0 => 'field_name', 1 => 'order_direction')
     * @return array Array of users matching the filter criteria that may be from cache (if similar search was previously run)
     */
    public function getUserArray(
        $add_blank = true,
        $status = 'Active',
        $user_id = '',
        $use_real_name = false,
        $user_name_filter = '',
        $portal_filter = ' AND portal_only=0 ',
        $from_cache = true,
        $order_by = []
    ) {

        global $locale, $dictionary, $current_user;

        if (empty($locale)) {
            $locale = Localization::getObject();
        }
        $params = [];
        $db = DBManagerFactory::getInstance();

        // Pre-build query for use as cache key
        // Including deleted users for now.
        if (empty($status)) {
            $query = 'SELECT id, first_name, last_name, user_name FROM users ';
            $where = '1=1' . $portal_filter;
        } else {
            $query = 'SELECT id, first_name, last_name, user_name FROM users ';
            $where = 'status = ?' . $portal_filter;
            $params[] = $status;
        }

        $user = BeanFactory::newBean('Users');
        $options = ['action' => 'list'];
        $user->addVisibilityFrom($query, $options);
        $query .= " WHERE $where ";
        $user->addVisibilityWhere($query, $options);

        if (!empty($user_name_filter)) {
            $user_name_filter = $db->quote($user_name_filter);
            $query .= ' AND user_name LIKE ? ';
            $params[] = $user_name_filter . '%';
        }
        if (!empty($user_id)) {
            $query .= ' OR id = ?';
            $params[] = $user_id;
        }

        $orderQuery = [];
        foreach ($order_by as $order) {
            $field = $order[0];
            if (empty($field) || empty($dictionary['User']['fields'][$field])) {
                continue;
            }
            $direction = strtoupper($order[1]);
            if (!in_array($direction, ['ASC', 'DESC'])) {
                $direction = 'ASC';
            }
            $orderQuery[] = "$field $direction";
        }

        if (empty($orderQuery)) {
            // get the user preference for name formatting, to be used in order by
            if (!empty($current_user) && !empty($current_user->id)) {
                $formatString = $current_user->getPreference('default_locale_name_format');

                // create the order by string based on position of first and last name in format string
                $firstNamePos = strpos($formatString, 'f');
                $lastNamePos = strpos($formatString, 'l');
                if ($firstNamePos !== false || $lastNamePos !== false) {
                    // it is possible for first name to be skipped, check for this
                    if ($firstNamePos === false) {
                        $orderQuery[] = 'last_name ASC';
                    } else {
                        $orderQuery[] = ($lastNamePos < $firstNamePos) ?
                            'last_name, first_name ASC' : 'first_name, last_name ASC';
                    }
                }
            } else {
                $orderQuery[] = 'user_name ASC';
            }
        }

        $query .= ' ORDER BY ' . implode(', ', $orderQuery);

        if ($from_cache) {
            $key_name = $query . $status . $user_id . $use_real_name . $user_name_filter . $portal_filter;
            $key_name = md5($key_name);
            $user_array = get_register_value('user_array', $key_name);
        }

        if (empty($user_array)) {
            $temp_result = [];

            $stmt = $db->getConnection()->executeQuery($query, $params);

            // Get the id and the name.
            while ($row = $stmt->fetchAssociative()) {
                if ($use_real_name == true || showFullName()) {
                    // We will ALWAYS have both first_name and last_name (empty value if blank in db)
                    if (isset($row['last_name'])) {
                        $temp_result[$row['id']] = $locale->formatName('Users', $row);
                    } else {
                        $temp_result[$row['id']] = $row['user_name'];
                    }
                } else {
                    $temp_result[$row['id']] = $row['user_name'];
                }
            }

            $user_array = $temp_result;
            if ($from_cache) {
                set_register_value('user_array', $key_name, $temp_result);
            }
        }

        if ($add_blank) {
            $user_array[''] = '';
        }

        return $user_array;
    }

    /**
     * Filter list of fields and remove/blank fields that we can not access
     * Modifies the list directly.
     * @param array $list list of fields, keys are field names
     * @param array $context
     * @param array options Filtering options:
     * - blank_value (bool) - instead of removing inaccessible field put '' there
     * - add_acl (bool) - instead of removing fields add 'acl' value with access level
     * - suffix (string) - strip suffix from field names
     * - min_access (int) - require this level of access for field
     * - use_value (bool) - look for field name in value, not in key of the list
     */
    public function ACLFilterFieldList(&$list, $context = [], $options = [])
    {
        global $current_user;

        parent::ACLFilterFieldList($list, $context, $options);
        if (self::isTrialDemoUser($this->user_name)) {
            if (isset($list['user_name']['acl']) && $list['user_name']['acl'] > 1) {
                // make it read only for demo users
                $list['user_name']['acl'] = 1;
            }
        }
    }

    /**
     * Gets the time zone for the given user.
     *
     * @param User $user
     * @return DateTimeZone the user's timezone
     */
    public function getTimezone()
    {
        $gmtTZ = new DateTimeZone('UTC');
        $userTZName = TimeDate::userTimezone($this);
        if (!empty($userTZName)) {
            $tz = new DateTimeZone($userTZName);
        } else {
            $tz = $gmtTZ;
        }
        return $tz;
    }

    /**
     * {@inheritDoc}
     */
    public function mark_deleted($id)
    {
        parent::mark_deleted($id);

        Container::getInstance()->get(Listener::class)->userDeleted($id);
    }

    /**
     * set license types in json-encoded string format
     * @param array $types array of types to pass in
     * @param bool $buildCache flag to rebuild metadata cache
     */
    public function setLicenseType(array $types = [], bool $buildCache = true, bool $removeDupes = true)
    {
        global $current_user;
        if ((!empty($current_user) && $current_user->hasAdminAndDevPrivilege()) || $this->id == '1') {
            if (empty($types)) {
                $this->license_type = null;
            } else {
                if ($removeDupes) {
                    $types = $this->removeDuplicates($types);
                }
                $this->license_type = json_encode(array_values($types));
            }
            if ($this->isLicenseTypeModified($types) && !($GLOBALS['installing'] ?? false) && $buildCache) {
                // need to refresh this user's metatdata cache
                $this->update_date_modified = true;
                // need to reset access control
                if ($current_user->id === $this->id) {
                    AccessControlManager::instance()->resetAccessControl();
                }
                // License Type has been changed, clear the report cache
                $default_language = SugarConfig::getInstance()->get('default_language') ?? 'en_us';
                $current_language = !empty($current_user->preferred_language) ?
                    $current_user->preferred_language : $default_language;
                foreach (array_unique([$this->id, $current_user->id]) as $id) {
                    foreach (['modules/modules_def_', 'modules/modules_def_fiscal_'] as $prefix) {
                        $cacheFile = sugar_cached($prefix . $current_language . '_' . md5((string)$id) . '.js');
                        if (file_exists($cacheFile)) {
                            unlink($cacheFile);
                        }
                    }
                }
            }
        }
    }

    /**
     * remove duplicates from license type
     * @param array $licenseTypes
     * @return array
     */
    protected function removeDuplicates(array $licenseTypes): array
    {
        if (safeCount($licenseTypes) <= 1) {
            return $licenseTypes;
        }

        $duplicates = $this->getNonCrmTypesArePartOfSelectedBundle($licenseTypes);
        if (empty($duplicates)) {
            return $licenseTypes;
        }
        return array_filter($licenseTypes, function (string $type) use ($duplicates): bool {
            return !safeInArray($type, $duplicates);
        });
    }

    /**
     * assign all system license types to this user
     */
    public function assignAllLicenseTypes(): void
    {
        $syslicenseTypes = $this->getTopLevelSystemSubscriptionKeys();
        $this->setLicenseType($syslicenseTypes);
    }

    /**
     * check if license type has been modified
     * @param array $newTypes new type
     * @return bool
     */
    public function isLicenseTypeModified(array $newTypes): bool
    {
        if (empty($this->oldLicenseType) && empty($newTypes)) {
            return false;
        }
        $oldType = $this->getOldTypes();

        $newTypes = $this->getNewTypes($newTypes);

        if (!empty(array_diff($oldType, $newTypes)) || !empty(array_diff($newTypes, $oldType))) {
            return true;
        }

        return false;
    }

    /**
     * check if need to update license type
     * @param array $newTypes
     * @return bool
     */
    public function needToUpdateLicenseType(array $newTypes): bool
    {
        if (empty($this->oldLicenseType)) {
            return true;
        }

        return $this->isLicenseTypeModified($newTypes);
    }

    /**
     * get license type. json-decoded to array
     * @param bool $getAll
     * @return array
     */
    protected function getLicenseTypes(bool $getAll = true): array
    {
        // support user has the full privilidge to access all flavors
        if (in_array($this->user_name, [self::SUPPORT_USER_NAME, self::SUPPORT_PORTAL_USER])) {
            if ($getAll) {
                return $this->removeDuplicates($this->getAllSystemSubscriptionKeys());
            }
            return $this->removeDuplicates($this->getTopLevelSystemSubscriptionKeys());
        }

        return $this->removeDuplicates($this->processLicenseTypes($this->license_type, $getAll));
    }

    /**
     * get all license types
     *
     * @return array
     */
    public function getAllLicenseTypes(): array
    {
        return $this->getLicenseTypes(true);
    }

    /**
     * get top level license types
     *
     * @return array
     */
    public function getTopLevelLicenseTypes(): array
    {
        return $this->getLicenseTypes(false);
    }

    /**
     * get license types' description in string
     *
     * @return string
     */
    protected function getLicenseTypesDescriptionString(): string
    {
        if ($this->status !== 'Active' && empty($this->getTopLevelLicenseTypes())) {
            // inactive user with empty license type
            $desc = 'No License Assigned';
        } else {
            $userSubscriptions = SubscriptionManager::instance()->getTopLevelUserSubscriptions($this);
            $desc = implode('<br>', array_map(function (string $type): string {
                return htmlspecialchars(self::getLicenseTypeDescription($type), ENT_COMPAT);
            }, $userSubscriptions));
            $invalidLicenseTypes = SubscriptionManager::instance()->getUserInvalidSubscriptions($this);
            if (!empty($invalidLicenseTypes)) {
                $desc .= '<p class="error">' . implode('<br>', array_map(function (string $type): string {
                    return htmlspecialchars(self::getLicenseTypeDescription($type), ENT_COMPAT);
                }, $invalidLicenseTypes)) . '</p>';
            }
        }
        return $desc;
    }

    /**
     * process license type in different format. string, json-encoded string, array.
     *
     * @param mixed $licenseTypes
     * @return array
     */
    public function processLicenseTypes($licenseTypes, $getAll = true): array
    {
        if (empty($licenseTypes)) {
            return [];
        }

        if (!is_string($licenseTypes) && !is_array($licenseTypes)) {
            throw new SugarApiExceptionInvalidParameter('Invalid license_type format in module: Users');
        }

        if (is_array($licenseTypes)) {
            // remove empty license types
            $types = [];
            foreach ($licenseTypes as $type) {
                if (!empty($type)) {
                    $types[] = $type;
                    if ($getAll && Subscription::isBundleKey($type)) {
                        $foundBundles = SubscriptionManager::instance()->getBundledSubscriptionsByKey($type);
                        if (!empty($foundBundles)) {
                            $types = array_merge($types, array_keys($foundBundles));
                        }
                    }
                }
            }
            return array_unique(array_values($types));
        }

        // remove '&quot;', in listview retrieveal, it does encode automatically
        $licenseTypes = str_replace('&quot;', '"', $licenseTypes);
        // string may be in json_econded format
        $value = json_decode($licenseTypes, true);

        if (is_array($value)) {
            return array_values($value);
        }

        throw new SugarApiExceptionInvalidParameter('Invalid license_type in processLicenseTypes');
    }

    /**
     * validate license types. Only allow system entitled license types to go through
     *
     * @param array $licenseTypes
     * @param bool $allowEmpty
     *
     * @return bool
     */
    public function validateLicenseTypes(array $licenseTypes, bool $allowEmpty = true): bool
    {
        if (empty($licenseTypes) && !$allowEmpty) {
            return false;
        }

        $isSellPremier = safeInArray(Subscription::SUGAR_SELL_PREMIER_KEY, $licenseTypes);
        if ($isSellPremier && !safeInArray(Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY, $this->getTopLevelSystemSubscriptionKeys())) {
            if (!empty($GLOBALS['log'])) {
                $type = Subscription::SUGAR_SELL_PREMIER_KEY;
                $GLOBALS['log']->fatal("invalid license type : $type");
                $GLOBALS['log']->fatal('system license keys: ' . json_encode($this->getTopLevelSystemSubscriptionKeys()));
                $GLOBALS['log']->fatal('user license keys: ' . json_encode($licenseTypes));
            }
            return false;
        }

        $foundSubs = [];
        if ($isSellPremier) {
            $foundSubs = $this->getBundledSubscriptionKeys(Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY);
        }
        foreach ($licenseTypes as $type) {
            if (!safeInArray($type, $this->getTopLevelSystemSubscriptionKeys())) {
                if (!$isSellPremier) {
                    if (!empty($GLOBALS['log'])) {
                        $GLOBALS['log']->fatal("invalid license type : $type");
                        $GLOBALS['log']->fatal('system license keys: ' . json_encode($this->getTopLevelSystemSubscriptionKeys()));
                        $GLOBALS['log']->fatal('user license keys: ' . json_encode($licenseTypes));
                    }
                    return false;
                } elseif ($type !== Subscription::SUGAR_SELL_PREMIER_KEY && !safeInArray($type, $foundSubs)) {
                    if (!empty($GLOBALS['log'])) {
                        $GLOBALS['log']->fatal("invalid license type : $type");
                        $GLOBALS['log']->fatal('system license keys: ' . json_encode($this->getTopLevelSystemSubscriptionKeys()));
                        $GLOBALS['log']->fatal('user license keys: ' . json_encode($licenseTypes));
                    }
                    return false;
                }
            }
        }

        return true;
    }

    /**
     *
     * get bundled subscriptions for a given key
     * @param string $key
     * @return array
     */
    protected function getBundledSubscriptionKeys(string $key) : array
    {
        $subs = SubscriptionManager::instance()->getSystemSubscriptionByKey(Subscription::SUGAR_SELL_PREMIER_BUNDLE_KEY);
        return array_keys($subs[Addon::BUNDLED_PRODUCTS_KEY] ?? []);
    }

    /**
     * get CRM license types
     * @param array $licenseTypes
     * @return array
     */
    protected function getCrmBundleLicenseTypes(array $licenseTypes): array
    {
        $ret = [];
        if (empty($licenseTypes)) {
            return [];
        }

        foreach ($licenseTypes as $type) {
            if (Subscription::isMangoKey($type) && Subscription::isBundleKey($type)) {
                $ret[] = $type;
            }
        }

        return $ret;
    }

    /**
     * get CRM license types
     * @param array $licenseTypes
     * @return array
     */
    protected function getNonCrmLicenseTypes(array $licenseTypes): array
    {
        $ret = [];
        if (empty($licenseTypes)) {
            return [];
        }

        foreach ($licenseTypes as $type) {
            if (!Subscription::isMangoKey($type)) {
                $ret[] = $type;
            }
        }

        return $ret;
    }

    /**
     * get system subscription keys
     * @param bool $getAll to get All subscription keys, not only top level if it is true
     * @return array
     */
    protected function getAllSystemSubscriptionKeys(bool $getAll = true): array
    {
        return array_keys(SubscriptionManager::instance()->getAllSystemSubscriptionKeys());
    }

    /**
     * get top level system subscription keys
     * @return array
     */
    protected function getTopLevelSystemSubscriptionKeys(): array
    {
        return array_keys(SubscriptionManager::instance()->getTopLevelSystemSubscriptionKeys());
    }

    /**
     * Can user be authenticated?
     *
     * @return bool
     */
    public function canBeAuthenticated(): bool
    {
        return !empty($this->user_name) || $this->external_auth_only;
    }

    /**
     * Checks if a user has a license by name
     * @param string $license System license key like SUGAR_SERVE or SUGAR SELL
     * @param bool $prepend Whether to add the SUGAR_ prefix
     * @return boolean
     */
    final public function hasLicense(string $licenseKey, $prepend = true): bool
    {
        if ($prepend && stripos($licenseKey, 'sugar_') === false) {
            $license = 'SUGAR_' . strtoupper($licenseKey);
        }

        $allKeys = SubscriptionManager::instance()->getSubscriptionKeysContains($licenseKey);
        foreach ($allKeys as $key) {
            if (in_array($key, $this->getAllLicenseTypes())) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the user has all the required licenses
     *
     * @param array $requiredLicenses
     *
     * @return bool
     */
    final public function hasLicenses(array $requiredLicenses): bool
    {
        if (empty($this) || empty($this->id)) {
            return false;
        }

        $sm = SubscriptionManager::instance();
        $licenses = $sm->getAllImpliedSubscriptions($sm->getAllUserSubscriptions($this));

        return !array_diff($requiredLicenses, $licenses);
    }

    protected function getDeleteUpdateParams(string $date = null, string $userId = null)
    {
        $params = parent::getDeleteUpdateParams($date, $userId);
        $params['status'] = 'Inactive';
        $params['employee_status'] = 'Terminated';

        return $params;
    }

    /**
     * check if license types contain at least one Mango license type
     * @param mixed $licenseTypes
     * @return bool
     */
    protected function hasMangoLicenseTypes($licenseTypes): bool
    {
        if (empty($licenseTypes)) {
            return true;
        }

        if (is_string($licenseTypes)) {
            $licenseTypes = $this->processLicenseTypes($licenseTypes);
        }
        foreach ($licenseTypes as $type) {
            if (Subscription::isMangoKey($type)) {
                return true;
            }
        }
        return false;
    }

    /**
     * get non CRM types which already offered in Bundles
     * @return array
     */
    public function getNonCrmTypesArePartOfSelectedBundle(array $licenseTypes): array
    {
        $crmBundleKeys = $this->getCrmBundleLicenseTypes($licenseTypes);
        $nonCrmKeys = $this->getNonCrmLicenseTypes($licenseTypes);
        $ret = [];
        foreach ($nonCrmKeys as $nonCrmKey) {
            foreach ($crmBundleKeys as $crmKey) {
                $bundled = SubscriptionManager::instance()->getBundledSubscriptionsByKey($crmKey);
                if (!empty($bundled) && array_key_exists($nonCrmKey, $bundled)) {
                    $ret[] = $nonCrmKey;
                }
            }
        }
        return $ret;
    }

    /**
     * Get user lists for batch update
     * @param bool $emptyLicenseTypeOnly only for empty value
     * @return array
     * @throws SugarQueryException
     */
    public function retrieveUsersUsingLicenseTypes(bool $emptyLicenseTypeOnly = false): array
    {
        if (!$this->isAdmin()) {
            if ($GLOBALS['log']) {
                $GLOBALS['log']->fatal('non-admin user is not allow to do retrieve users');
            }
            return [];
        }
        $query = new SugarQuery();
        $query->from($this);
        $query->select([
            'id',
            'license_type',
        ]);

        $query->where()
            ->in('status', ['Active', 'Inactive'])
            ->equals('deleted', 0);

        if ($emptyLicenseTypeOnly) {
            $query->where()->isNull('license_type');
        }

        return $query->execute();
    }

    /**
     * update all users to the same license types, this is only for single license type case
     * @param array $licenseTypes
     * @param bool $buildCache
     * @throws SugarApiExceptionNotFound
     * @throws SugarQueryException
     */
    public function updateUsersLicenseTypes(array $licenseTypes = [], bool $buildCache = true)
    {
        if (empty($licenseTypes)) {
            $licenseTypes = [SubscriptionManager::instance()->getUserDefaultLicenseType()];
        }
        // get top level system keys
        $systemEntitlements = $this->getTopLevelSystemSubscriptionKeys();
        $users = $this->retrieveUsersUsingLicenseTypes();
        foreach ($users as $userData) {
            $userBean = BeanFactory::getBean('Users', $userData['id']);
            if ($userBean && $userBean->needToUpdateLicenseType($licenseTypes)) {
                $existLicenseTypes = $userBean->getTopLevelLicenseTypes();
                // retain all products, such as discover, connect, hint, etc if those are also in new system entitlements
                $retainedTypes = array_intersect($existLicenseTypes, $systemEntitlements);
                $newLicenseTypes = array_unique(array_merge($retainedTypes, $licenseTypes));
                $userBean->setLicenseType($newLicenseTypes, $buildCache);
                $userBean->save();
            }
        }
    }

    /**
     * Gets the list of product codes in use by the User
     *
     * @return array
     */
    public function getProductCodes()
    {
        $productCodes = [];
        $productsData = $this->getProductsData();
        foreach ($productsData as $productsDatum) {
            if (!empty($productsDatum['product_code'])) {
                $productCodes[] = $productsDatum['product_code'];
            }
        }
        return array_unique($productCodes);
    }

    /**
     * Gets the array of data for the top-level products the User has active
     *
     * @return array the array of products data for the user
     */
    public function getProductsData()
    {
        global $sugar_flavor;
        $subscriptionManager = $this->getSubscriptionManager();
        $productKeys = $subscriptionManager->getTopLevelUserSubscriptions($this);

        $products = [];
        foreach ($productKeys as $productKey) {
            $productCode = $productKey;
            if ($productKey === Subscription::SUGAR_BASIC_KEY) {
                // The basic key corresponds to the instance flavor, which
                // should be ENT, PRO, or ULT. If it is not one of those,
                // we fall back to ENT
                $productCode = in_array($sugar_flavor, ['ENT', 'PRO', 'ULT']) ? $sugar_flavor : 'ENT';
            }
            $products[$productKey] = [
                'product_code' => $productCode,
                'product_name' => User::getLicenseTypeDescription($productKey),
            ];
        }

        return $products;
    }

    /**
     * Returns the SubscriptionManager instance
     *
     * @return SubscriptionManager
     */
    protected function getSubscriptionManager()
    {
        return SubscriptionManager::instance();
    }

    /**
     * update all users with empty license type to new license types
     * @param array $licenseTypes
     * @throws SugarApiExceptionNotFound
     * @throws SugarQueryException
     */
    public function updateUsersEmptyLicenseTypes(array $licenseTypes = [])
    {
        if (empty($licenseTypes)) {
            $licenseTypes = [SubscriptionManager::instance()->getUserDefaultLicenseType()];
        }
        $users = $this->retrieveUsersUsingLicenseTypes(true);
        foreach ($users as $userData) {
            $userBean = BeanFactory::getBean('Users', $userData['id']);
            if ($userBean && $userBean->status !== 'Active') {
                $userBean->setLicenseType($licenseTypes);
                $userBean->save();
            }
        }
    }

    /**
     * update license type after license entitlement changes
     * @param array $newAddedKeys new added keys
     * @param array $removedKeys removed keys
     * @param bool $forceToUpdate force to update
     * @throws SugarApiExceptionNotFound
     * @throws SugarQueryException
     */
    public function updateUsersLicenseTypesAfterSubscriptionChanges(array $newAddedKeys = [], array $removedKeys = [], bool $forceToUpdate = false): void
    {
        global $current_user;
        if (empty($current_user) || !is_admin($current_user)) {
            return;
        }

        $oldKeys = SubscriptionManager::instance()->getOldSystemSubscriptionKeys();
        $currentKeys = SubscriptionManager::instance()->getTopLevelSystemSubscriptionKeys();
        $newAddedKeys = array_diff_key($currentKeys, $oldKeys);
        $removedKeys = array_diff_key($oldKeys, $currentKeys);
        if (empty($newAddedKeys) && empty($removedKeys) && !$forceToUpdate) {
            return;
        }


        // update empty license type first if the entitlement only has Single Mango type
        if (SubscriptionManager::instance()->isSingleMangoTypeEntitlement()) {
            $current_user->updateUsersLicenseTypes();
        } elseif (!empty($removedKeys) || $forceToUpdate) {
            $this->massUpdateLicenseTypes();
        }
    }

    /**
     * mass update license types using current entitlement
     * @param bool $rebuildCache
     * @throws SugarApiExceptionNotFound
     * @throws SugarQueryException
     */
    protected function massUpdateLicenseTypes(bool $rebuildCache = true): void
    {
        $defaultLicenseType = SubscriptionManager::instance()->getUserDefaultLicenseType();
        $systemLicenseTypes = $this->getTopLevelSystemSubscriptionKeys();
        $users = $this->retrieveUsersUsingLicenseTypes(false);
        foreach ($users as $userData) {
            $userBean = BeanFactory::getBean('Users', $userData['id']);
            if ($userBean && $userBean->needToUpdateLicenseType([$defaultLicenseType])) {
                $licenseTypes = $userBean->getTopLevelLicenseTypes();
                // find out new valid license types
                $newLicenseTypes = array_values(array_unique(array_intersect($licenseTypes, $systemLicenseTypes)));
                // migrate to SELL subscription
                if (empty(Subscription::getSellKey($newLicenseTypes)) && !empty(Subscription::getSellKey($licenseTypes))) {
                    $nextSellKey = Subscription::getSellKey($systemLicenseTypes);
                    if (!empty($nextSellKey)) {
                        $newLicenseTypes[] = $nextSellKey;
                    }
                }
                if (empty($newLicenseTypes) && SubscriptionManager::instance()->isSingleMangoTypeEntitlement()) {
                    // assigning default license type for single mango instance
                    $newLicenseTypes = [$defaultLicenseType];
                }
                if (!empty(array_diff($licenseTypes, $newLicenseTypes)) || !empty(array_diff($licenseTypes, $newLicenseTypes))) {
                    $userBean->setLicenseType($newLicenseTypes, $rebuildCache);
                    $userBean->save();
                }
            }
        }
    }

    /**
     * migrate sell license type
     *
     * @param bool $rebuildCache to rebuild metadata cache
     * @throws SugarApiExceptionNotFound
     * @throws SugarQueryException
     */
    public function migrateUsersLicenseTypes(bool $rebuildCache = true): void
    {
        global $current_user;
        if (empty($current_user) || !is_admin($current_user)) {
            return;
        }

        // update license type first if the entitlement has only Single Mango type
        if (SubscriptionManager::instance()->isSingleMangoTypeEntitlement()) {
            $this->updateUsersLicenseTypes([], $rebuildCache);
        } else {
            $this->massUpdateLicenseTypes($rebuildCache);
        }
    }

    /**
     * Fix users' license types mismatching with system subscriptions
     * if IDM license feature is disabled and we have never tried or after long period of time
     * @return bool
     */
    public function fixLicenseTypeMismatch(): bool
    {
        if (SubscriptionManager::instance()->getFixLicenseProcessState()) {
            return false;
        }
        global $current_user;
        if (empty($current_user) || !$current_user->isLicenseTypeFixable()) {
            return false;
        }

        $admin = Administration::getSettings('license');
        $currentTime = time();
        if (!empty($admin->settings['license_last_fix_mismatch'])) {
            $lastTimeFix = $admin->settings['license_last_fix_mismatch'];
            if ($currentTime - $lastTimeFix < 600) {
                // lock the action for 10 minutes
                return false;
            }
        }

        // check IDM license feature
        global $current_user;
        $idpConfig = new IdpConfig(\SugarConfig::getInstance());
        if ($idpConfig->isIDMModeEnabled() && $idpConfig->getUserLicenseTypeIdmModeLock()) {
            return false;
        }

        SubscriptionManager::instance()->setFixLicenseProcessState(true);
        $admin->saveSetting('license', 'last_fix_mismatch', "$currentTime");

        $existUser = $current_user;
        if (!is_admin($current_user)) {
            $current_user = $this->getSystemUser();
        }
        // download data and apply them

        Container::getInstance()->get(SubscriptionPrefetcher::class)->run();
        try {
            SubscriptionManager::instance()->applyDownloadedLicense(true);
        } catch (SugarApiExceptionLicenseSeatsNeeded $e) {
            // allow admin to continue
            if (isset($GLOBALS['current_user']) && is_admin($GLOBALS['current_user'])) {
                $this->loginSuccess = true;
                return false;
            }
        } catch (Exception $ex) {
            throw $ex;
        }

        $current_user = $existUser;
        SubscriptionManager::instance()->setFixLicenseProcessState(false);
        return true;
    }

    /**
     * only Active and non support user are fixable
     * @return bool
     */
    public function isLicenseTypeFixable(): bool
    {
        return !empty($this->id) && $this->deleted == 0 && $this->status === 'Active' && !self::isSupportUser($this);
    }


    /**
     * Checks if the user can receive push notifications. If a an optional preference is provided, also check
     * that the preference is turned on.
     * @param string $preference
     * @return bool
     * @throws SugarQueryException
     */
    public function canReceivePushNotifications($preference = '')
    {
        $service = PushNotificationService::getService();
        $pushEnabledForUser = $service && $this->hasRegisteredDevices();

        if (!empty($preference)) {
            $pushEnabledForUser = $pushEnabledForUser && isTruthy($this->getPreference($preference));
        }

        return $pushEnabledForUser;
    }


    /**
     * @inheritdoc
     *
     * Sets default values for certain fields based on system configuration, if
     * a default value is not already provided
     */
    public function patchVardefs($vardefs)
    {
        // Set defaults for preference fields
        $vardefs['fields'] = $this->setPreferenceFieldDefaults($vardefs['fields']);

        // Set defaults for the mail_credentials field
        $mailCredentialsField = $vardefs['fields']['mail_credentials'] ?? null;

        if ($mailCredentialsField &&
            $this->systemOverrideMailCredentialsRequired() &&
            empty($mailCredentialsField['default'])
        ) {
            $systemDefaultConfiguration = $this->getSystemMailerConfiguration();
            $default = [
                'mail_smtpserver' => $systemDefaultConfiguration->getHost(),
                'mail_authtype' => $systemDefaultConfiguration->getAuthType(),
            ];

            if ($default['mail_authtype'] === 'oauth2') {
                $default['mail_smtptype'] = $systemDefaultConfiguration->getSmtpType();
            }

            $vardefs['fields']['mail_credentials']['default'] = $default;
        }

        return $vardefs;
    }

    /**
     * Helper function to determine when a User needs to provide their own
     * credentials for their system mailer override account
     *
     * @return bool true if credentials are required
     */
    protected function systemOverrideMailCredentialsRequired()
    {
        $systemDefaultConfiguration = OutboundEmailConfigurationPeer::getSystemDefaultMailConfiguration();
        $systemOutboundEmail = BeanFactory::newBean('OutboundEmail');
        $systemOutboundEmail = $systemOutboundEmail->getSystemMailerSettings();
        return !empty($systemDefaultConfiguration->getHost()) &&
            !$systemOutboundEmail->isAllowUserAccessToSystemDefaultOutbound() &&
            $systemDefaultConfiguration->isAuthenticationRequired();
    }

    protected function getSystemMailerConfiguration()
    {
        return OutboundEmailConfigurationPeer::getSystemDefaultMailConfiguration();
    }

    /**
     * Gets the language to use for localizing strings for the user. First preference is the user's
     * preferred language, with a fallback to the instance's default language.
     * @return string
     */
    public function getUserLanguageWithFallback()
    {
        return !empty($this->preferred_language) ? $this->preferred_language : getValueFromConfig('default_language');
    }

    /**
     * Gets the User's appearance preference; light, dark or null. If it's not set (null), fallback to 'system_default'.
     * @return string
     */
    public function getUserPrefAppearanceDefault()
    {
        return $this->getPreference('appearance') ?? 'system_default';
    }

    /**
     * Retrieves the User's last state data from the database
     *
     * @param string $platform platform, or base if not specified
     * @return array|null the last state data if found; null otherwise
     * @throws \Doctrine\DBAL\Exception
     */
    public function retrieveLastStates($platform = 'base')
    {
        // Get the last state data from the DB
        $qb = DBManagerFactory::getInstance()->getConnection()->createQueryBuilder();
        $qb->from('users_last_states')
            ->select('last_state')
            ->where($qb->expr()->eq('user_id', $qb->createPositionalParameter($this->id)))
            ->andWhere($qb->expr()->eq('platform', $qb->createPositionalParameter($platform)));
        $result = $qb->execute()->fetch();

        // Unpack the result and return it
        if (!empty($result)) {
            return unserialize(base64_decode($result['last_state']), ['allowed_classes' => false]);
        }
        return null;
    }

    /**
     * Updates key/value pairs in the User's last state data
     *
     * @param $changes array the key/value pairs to update in the User's last state data
     * @param string $platform platform, or base if not specified
     * @return array the updated last state data for the User
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateLastStates($changes, $platform = 'base')
    {
        // Get the last state data currently stored in the DB for the user
        $lastStates = $this->retrieveLastStates($platform);
        $dataExists = $lastStates !== null;
        if (!$dataExists) {
            $lastStates = [];
        }

        // Make the key/value changes to the last state data
        foreach (safeIsIterable($changes) ? $changes : [] as $key => $value) {
            $lastStates[$key] = $value;
        }

        // Save the updated last state data to the DB
        $qb = DBManagerFactory::getInstance()->getConnection()->createQueryBuilder();
        if (!$dataExists) {
            $qb->insert('users_last_states')
                ->values([
                    'id' => $qb->createPositionalParameter(\Sugarcrm\Sugarcrm\Util\Uuid::uuid1()),
                    'user_id' => $qb->createPositionalParameter($this->id),
                    'last_state' => $qb->createPositionalParameter(base64_encode(serialize($lastStates))),
                    'platform' => $qb->createPositionalParameter($platform),
                ])
                ->execute();
        } else {
            $qb->update('users_last_states')
                ->set('last_state', $qb->createPositionalParameter(base64_encode(serialize($lastStates))))
                ->where($qb->expr()->eq('user_id', $qb->createPositionalParameter($this->id)))
                ->andWhere($qb->expr()->eq('platform', $qb->createPositionalParameter($platform)))
                ->execute();
        }

        // Return the updated last state data
        return $lastStates;
    }

    /**
     * @return array|mixed
     */
    protected function getOldTypes()
    {
        if (empty($this->oldLicenseType)) {
            $oldType = [SubscriptionManager::instance()->getUserDefaultLicenseType()];
        } else {
            $oldType = json_decode($this->oldLicenseType, true);
        }
        return $oldType;
    }

    /**
     * @param array $newTypes
     * @return array
     */
    protected function getNewTypes(array $newTypes): array
    {
        if (empty($newTypes) && $this->status === 'Active') {
            $newTypes = [SubscriptionManager::instance()->getUserDefaultLicenseType()];
        }
        return $newTypes;
    }

    /**
     * Given a set of field definitions, sets the default values for User
     * preference fields
     *
     * @param array $fieldDefs the list of field definitions
     * @return array the field definitions updated with default values
     */
    public function setPreferenceFieldDefaults(array $fieldDefs)
    {
        $preferenceHelper = $this->getPreferenceHelper();
        foreach ($fieldDefs as $fieldName => $fieldDef) {
            $isUserPreferenceField = $fieldDef['user_preference'] ?? false;
            if ($isUserPreferenceField && !isset($fieldDef['default'])) {
                $newDefault = $preferenceHelper->getPreferenceField($this, $fieldName);
                if (isset($newDefault)) {
                    $fieldDefs[$fieldName]['default'] = $newDefault;
                }
            }
        }
        return $fieldDefs;
    }

    /**
     * Returns a helper for working with User preference fields
     *
     * @return UserPreferenceFieldsHelper
     */
    public function getPreferenceHelper()
    {
        return new UserPreferenceFieldsHelper();
    }

    /**
     * Get the Email Link Dropdown
     *
     * @return array
     */
    public function getEmailLinkDropdown(): array
    {
        global $app_list_strings;

        $domEmailLinkType = $app_list_strings['dom_email_link_type'];

        if (!$domEmailLinkType || !is_array($domEmailLinkType)) {
            return [];
        }

        // If the system outbound email account is not configured, remove the Sugar Client option
        if (!$this->isSystemEmailConfigured()) {
            $domEmailLinkType = array_filter($domEmailLinkType, function ($key) {
                return $key !== 'sugar';
            }, ARRAY_FILTER_USE_KEY);
        }

        return $domEmailLinkType;
    }

    /**
     * Checks whether the system email settings have been configured
     *
     * @return bool true if the system email settings are configured
     */
    protected function isSystemEmailConfigured()
    {
        $systemEmailConfig = OutboundEmailConfigurationPeer::getSystemDefaultMailConfiguration();
        return $systemEmailConfig instanceof OutboundSmtpEmailConfiguration && !empty($systemEmailConfig->getHost());
    }
}
