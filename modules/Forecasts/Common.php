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
 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
class Common
{
    public $db;
    public $all_users = [];  //array of userid's and reports_to_id
    public $my_managers = [];  //array of users current user reports to. direct and indirect
    public $my_downline = [];  //array of users reporting to current user. direct and indirect.
    public $my_direct_downline = [];  //array of users directly reporting to current user
    public $current_user;  //logged in user's id
    public $my_direct_reports = [];
    public $my_timeperiods = [];
    public $my_name;

    public $start_date;
    public $end_date;
    public $timeperiod_name;
    public $timedate;

    //class constructor.
    public function __construct()
    {
        global $db;
        $this->db = $db;
        if (empty($this->db)) {
            $this->db = DBManagerFactory::getInstance();
        }

        $this->timedate = TimeDate::getInstance();
    }

    public function get_timeperiod_start_end_date($timeperiod_id)
    {

        $query = "SELECT id, start_date, end_date, name from timeperiods where id = '$timeperiod_id'";
        $result = $this->db->query($query, true, 'Error fetching timeperiod:');
        $row = $this->db->fetchByAssoc($result);
        if ($row != null) {
            $this->start_date = $this->timedate->to_display_date($row['start_date'], false, false);
            $this->end_date = $this->timedate->to_display_date($row['end_date'], false, false);
            $this->timeperiod_name = $row['name'];
        }
    }

    //set the current user.
    public function set_current_user($theUser)
    {
        //when the user id associated with the instance is changed
        //re-initialize the variable.
        $this->all_users = [];
        $this->my_direct_reports = [];

        $this->current_user = $theUser;
    }


    //flatten the reporting structure.
    public function setup()
    {
        $this->get_all_users();
        $this->get_my_managers();
    }

    //flatten reporting hierarchy, load all  users and their manager's ID.
    public function get_all_users()
    {
        global $locale;
        $query = "SELECT id, first_name, last_name, reports_to_id FROM users WHERE deleted=0 and status = 'Active'";
        $result = $this->db->query($query, true, ' Error filling in users list: ');

        while (($row = $this->db->fetchByAssoc($result)) != null) {
            //Add to all users array
            if ($row['id'] == $row['reports_to_id']) {
                $this->all_users[$row['id']] = null;
            } else {
                $this->all_users[$row['id']] = $row['reports_to_id'];

                //Add to my direct reports array.
                if (isset($row['reports_to_id']) && $this->current_user == $row['reports_to_id']) {
                    $this->my_direct_reports[$row['id']] = $locale->formatName('Users', $row);
                }

                //set name..
                //jclark - Bug 51212 - Forecasting user rollup shows incorrect user name
                if ("{$this->current_user}" == "{$row['id']}") {
                    $this->my_name = $locale->formatName('Users', $row);
                }
            }
        }
    }

    //get logged in user's managers.
    public function get_my_managers()
    {
        $seen_user_before = [];
        $theuser = $this->current_user;

        while (!empty($theuser) && array_key_exists($theuser, $this->all_users)) {
            if (isset($seen_user_before[$theuser])) {
                // We have seen this user before, ignore them
                $theuser = '';
                continue;
            }
            $seen_user_before[$theuser] = 1;
            if (!empty($this->all_users[$theuser])) {
                $theuser = $this->all_users[$theuser];  //get manager's id
                $this->my_managers[] = $theuser;
            } else {
                $theuser = '';
            }
        }
    }

    //return true if the logged in user is a manager.
    //a user is considered a manager if anybody report's to him/her
    public function is_user_manager()
    {
        if (safeCount($this->my_direct_reports) > 0) {
            return true;
        } else {
            return false;
        }
    }

    //using the forecast_schedule table find all the timeperiods the
    //logged in user needs to forecast for.
    //todo add date format for sqlserver.
    /**
     * @deprecated
     */
    public function get_my_timeperiods()
    {
        $this->my_timeperiods = [];
    }

    //returns a list of timeperiods that users has committed forecasts for.
    //sorted by timeperiod start date descending.
    public function get_my_forecasted_timeperiods($the_user_id)
    {
        $my_forecasted_timeperiods = [];

        $query = 'SELECT distinct timeperiods.id, timeperiods.name, timeperiods.start_date';
        $query .= ' FROM timeperiods, forecasts';
        $query .= ' WHERE timeperiods.id = forecasts.timeperiod_id';
        $query .= " AND forecasts.user_id = '$the_user_id'";
        $query .= ' ORDER BY start_date desc';

        $result = $this->db->query($query, false, ' Error filling in list of timeperiods to be forecasted: ');

        while (($row = $this->db->fetchByAssoc($result)) != null) {
            $my_forecasted_timeperiods[$row['id']] = $row['name'];
        }

        return $my_forecasted_timeperiods;
    }

    public function get_user_name($user_id)
    {
        global $locale;

        $sql = 'SELECT  first_name, last_name FROM users WHERE deleted=0 and id = ?';

        $row = DBManagerFactory::getInstance()
            ->getConnection()
            ->executeQuery($sql, [$user_id])
            ->fetchAssociative();

        if ($row !== false) {
            return $locale->formatName('Users', $row);
        }
    }

    public function get_reports_to_id($user_id)
    {
        $query = "SELECT reports_to_id FROM users WHERE id = '$user_id'";
        $result = $this->db->query($query, true, " Error fetching user's report's to Id : ");

        if (($row = $this->db->fetchByAssoc($result)) != null) {
            return $row['reports_to_id'];
        }
    }

    /* this method navigates the reporting hierarchy and produces a ordered list
     * of users,and types of forecast they need to commit to.
     * This function assumes that there is at least one user in the system who does not reports to
     * anybody.
     */
    public function get_forecast_commit_order()
    {
        $commit_order = [];

        $query = "select id from users where reports_to_id is null or reports_to_id = ''";
        $result = $this->db->query($query, true, ' Error fetching users who do not report to anybody: ');

        while (($row = $this->db->fetchByAssoc($result)) != null) {
            $this->process_users_downline($row['id'], $commit_order);
        }
        return $commit_order;
    }

    /*recursive function, adds a direct forecast entry for the user, if the user has a downline,
     * calls itself for each entry in the downline and then adds a rollup entry for the user. */
    public function process_users_downline($user_id, &$commit_order)
    {
        //this user needs to commit a direct forecast.
        $commit_order[] = ['0' => $user_id, '1' => 'Direct'];

        //find the logged in user's downline
        $query = "SELECT id FROM users WHERE reports_to_id = '$user_id'";
        $result = $this->db->query($query, true, " Error fetching user's reporting hierarchy: ");
        $found = 0;
        while (($row = $this->db->fetchByAssoc($result)) != null) {
            $found++;
            $this->process_users_downline($row['id'], $commit_order);
        }
        if ($found > 0) {
            $commit_order[] = ['0' => $user_id, '1' => 'Rollup'];
        }
    }

    public function retrieve_downline($user_id)
    {

        //find the logged in user's downline
        $query = "SELECT id FROM users WHERE reports_to_id = '$user_id'";
        $result = $this->db->query($query, true, " Error fetching user's reporting hierarchy: ");
        while (($row = $this->db->fetchByAssoc($result)) != null) {
            $this->my_downline[] = $row['id'];
            $this->retrieve_downline($row['id']);
        }
    }

    public function retrieve_direct_downline($user_id)
    {
        //find the direct reports_to users
        $query = "SELECT id FROM users WHERE reports_to_id = '$user_id' AND deleted = 0 AND status = 'Active'";
        $result = $this->db->query($query, true, " Error fetching user's reporting hierarchy: ");
        while (($row = $this->db->fetchByAssoc($result)) != null) {
            $this->my_direct_downline[] = $row['id'];
        }
    }

    /**
     * Get the passes in users reportee's who have a forecast for the passed in time period
     *
     * @param string $user_id A User Id
     * @param string $timeperiod_id The Time period you want to check for
     * @return array
     */
    public function getReporteesWithForecasts($user_id, $timeperiod_id)
    {

        $return = [];
        $query = "SELECT distinct users.user_name FROM users, forecasts
                WHERE forecasts.timeperiod_id = '" . $timeperiod_id . "' AND forecasts.deleted = 0
                AND users.id = forecasts.user_id AND (users.reports_to_id = '" . $user_id . "')";

        $result = $this->db->query($query, true, " Error fetching user's reporting hierarchy: ");
        while (($row = $this->db->fetchByAssoc($result)) != null) {
            $return[] = $row['user_name'];
        }

        return $return;
    }
}
