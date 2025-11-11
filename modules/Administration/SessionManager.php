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
/*********************************************************************************
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
// Expression is a general object for expressions, filters, and calculations
class SessionManager extends SugarBean
{
    public $new_schema = true;
    public $id;
    /*
     * the session_id we are tracking
     */
    public $session_id;
    /*
     * The type of session this row represents, portal...
     */
    public $session_type;
    public $last_request_time;
    public $date_entered;
    public $date_modified;
    public $is_violation;
    public $num_active_sessions;

    public $table_name = 'session_active';
    public $history_table_name = 'session_history';
    public $object_name = 'SessionManager';
    public $module_name = 'SessionManager';
    public $module_dir = 'Administration';
    public $disable_custom_fields = true;
    public $column_fields = ['id', 'session_id', 'last_request_time'];

    public function __construct()
    {
        parent::__construct();
        $this->disable_row_level_security = true;
    }

    public function archiveSession($session_id)
    {

        $valid_session = SessionManager::getValidSession($session_id);
        if ($valid_session != null) {
            $valid_session->archive();
        }
    }

    /*
     * Move the session of the active table and into the history table
     */
    public function archive()
    {
        //remove from the active table
        $query = "DELETE FROM $this->table_name WHERE id = '$this->id'";
        $result = $this->db->query($query);

        //now save to the archvie table
        $this->table_name = $this->history_table_name;
        unset($this->id);
        $this->save();
        $this->table_name = 'session_active';
    }

    /*
     * Whether or not we can add another session
     *
     * return true if we can add a session, false if there are no available slots
     */
    public function canAddSession()
    {
        $ncc_config = [];
        $this->archiveInactiveSessions();

        //we may not even have to check b/c the license could have the
        //license_enforce_portal_user_limit set to 0
        if ($this->getEnforcePortalUserLimit()) {
            $num_active = $this->getNumActiveSessions();
            $num_users = $this->getNumPortalUsers();

            $num = $num_users;

            $config = SugarAutoLoader::existingCustomOne('modules/Administration/ncc_config.php');
            if ($config) {
                require $config;
                $num = $ncc_config['value'];
            }

            if (!isset($num)) {
                $num = 1.2;
            }
            $num = $num * $num_users;

            $GLOBALS['log']->debug('Number of valid concurrent sessions: ' . $num);
            if ($num_active < $num) {
                return true;
            } else {
                return false;
            }
        } else {
            //if we are not enforcing the portal user limit then
            //do not worry about how many active sessions we can have, just assume we can add one.
            return true;
        }
    }

    /*
     * Retrieves the number of currently active sessions
     */
    public function getNumActiveSessions()
    {
        return $this->db->getConnection()->executeQuery(
            sprintf('SELECT count(*) FROM %s', $this->table_name)
        )->fetchOne();
    }

    /*
     * Move sessions that are no longer active from the active table
     * and into the history table
     */
    public function archiveInactiveSessions()
    {
        $time_diff = $this->getTimeDiff();
        $return_list = $this->get_full_list('', "$this->table_name.last_request_time < " . db_convert("'$time_diff'", 'datetime'));
        if (!empty($return_list)) {
            foreach ($return_list as $session) {
                $session->archive();
            }
        }
    }

    /*
     * Determine whether the session is still valid
     *
     * @param session_id
     *
     * return   true if session is still valid, false otherwise
     */
    public static function getValidSession($session_id)
    {
        $GLOBALS['log']->debug('Checking session validity');
        $sessionManager = new SessionManager();
        $session = $sessionManager->retrieve_by_string_fields(['session_id' => $session_id]);
        if ($session != null) {
            $GLOBALS['log']->debug('Time Diff: ' . $sessionManager->getTimeDiff());
            $GLOBALS['log']->debug('LAST REQUEST TIME: ' . $session->last_request_time);
            if ($session->last_request_time > $sessionManager->getTimeDiff()) {
                $GLOBALS['log']->debug('Session Time Succeeded');
                return $session;
            }
        } else {
            return null;
        }
    }

    /*
     * Return GMT date that represents the cutoff for expiring sessions
     *
     * The date returned is "now" - X seconds, where X is the session timeout
     * return @string
     */
    public function getTimeDiff()
    {
        $admin = Administration::getSettings('system');

        if (isset($admin->settings['system_session_timeout'])) {
            $session_timeout = abs($admin->settings['system_session_timeout']);
        } else {
            $session_timeout = abs(ini_get('session.gc_maxlifetime'));
        }
        $GLOBALS['log']->debug('System Session Timeout: ' . $session_timeout);

        global $timedate;
        $now = $timedate->getNow();
        return $timedate->asDb($now->get("-{$session_timeout} seconds"));
    }

    /*
     * Return the number of allowed portal users
     * as defined by the license
     */
    public function getNumPortalUsers()
    {
        $admin = Administration::getSettings('license');
        if (!isset($admin->settings['license_num_portal_users'])) {
            return 0;
        }
        return $admin->settings['license_num_portal_users'];
    }

    /**
     * Return boolean indicating whether or not portal user limits are enforced
     *
     */
    public function getEnforcePortalUserLimit()
    {
        $admin = Administration::getSettings('license');
        return isset($admin->settings['license_enforce_portal_user_limit']) && $admin->settings['license_enforce_portal_user_limit'] == '1' ? true : false;
    }

    /*
     * Overload the save function so we can log the number of currently active sessions and if this
     * session is in violation of the license
     */
    public function save($check_notify = false)
    {
        if ($this->table_name != $this->history_table_name && empty($this->id)) {
            $this->num_active_sessions = $this->getNumActiveSessions();

            $num_users = $this->getNumPortalUsers();
            $this->is_violation = 0;
            if ($this->num_active_sessions > $num_users) {
                $this->is_violation = 1;
            }
        }
        return parent::save($check_notify);
    }
}
