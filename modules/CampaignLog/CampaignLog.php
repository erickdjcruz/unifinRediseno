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

class CampaignLog extends SugarBean
{
    public $table_name = 'campaign_log';
    public $object_name = 'CampaignLog';
    public $module_dir = 'CampaignLog';

    public $new_schema = true;

    public $campaign_id;
    public $target_tracker_key;
    public $target_id;
    public $target_type;
    public $activity_type;
    public $activity_date;
    public $related_id;
    public $related_type;
    public $deleted;
    public $list_id;
    public $hits;
    public $more_information;
    public $marketing_id;


    public function __construct()
    {
        parent::__construct();

        $this->disable_row_level_security = true;
    }

    public function get_list_view_data($filter_fields = [])
    {
        global $locale;
        $temp_array = $this->get_list_view_array();
        //make sure that both items in array are set to some value, else return null
        if (!(isset($temp_array['TARGET_TYPE']) && $temp_array['TARGET_TYPE'] != '') || !(isset($temp_array['TARGET_ID']) && $temp_array['TARGET_ID'] != '')) {   //needed values to construct query are empty/null, so return null
            $GLOBALS['log']->debug("CampaignLog.php:get_list_view_data: temp_array['TARGET_TYPE'] and/or temp_array['TARGET_ID'] are empty, return null");
            $emptyArr = [];
            return $emptyArr;
        }

        $table = strtolower($temp_array['TARGET_TYPE']);

        if ($temp_array['TARGET_TYPE'] == 'Accounts') {
            $query = "select name from $table where id = ? ";
        } else {
            $query = 'select first_name, last_name, ' . $this->db->concat($table, ['first_name', 'last_name']) . " name from $table" .
                ' where id = ? ';
        }

        $conn = $this->db->getConnection();
        $stmt = $conn->executeQuery($query, [$temp_array['TARGET_ID']]);
        $row = $stmt->fetchAssociative();

        if ($row) {
            if ($temp_array['TARGET_TYPE'] == 'Accounts') {
                $temp_array['RECIPIENT_NAME'] = $row['name'];
            } else {
                $full_name = $locale->formatName($temp_array['TARGET_TYPE'], $row);
                $temp_array['RECIPIENT_NAME'] = $full_name;
            }
        }
        $temp_array['RECIPIENT_EMAIL'] = $this->retrieve_email_address($temp_array['TARGET_ID']);

        $query = 'select name from email_marketing where id = ? ';
        $conn = $this->db->getConnection();
        $stmt = $conn->executeQuery($query, [$temp_array['MARKETING_ID']]);
        $row = $stmt->fetchAssociative();

        if ($row) {
            $temp_array['MARKETING_NAME'] = $row['name'];
        }

        return $temp_array;
    }

    public function retrieve_email_address($trgt_id = '')
    {
        $return_str = '';
        if (!empty($trgt_id)) {
            $qry = ' select eabr.primary_address, ea.email_address';
            $qry .= ' from email_addresses ea ';
            $qry .= ' Left Join email_addr_bean_rel eabr on eabr.email_address_id = ea.id ';
            $qry .= ' where eabr.bean_id = ? ';
            $qry .= ' and ea.deleted = 0 ';
            $qry .= ' and eabr.deleted = 0';
            $qry .= ' order by primary_address desc ';

            $conn = $this->db->getConnection();
            $stmt = $conn->executeQuery($qry, [$trgt_id]);
            $row = $stmt->fetchAssociative();

            if (!empty($row['email_address'])) {
                $return_str = $row['email_address'];
            }
        }
        return $return_str;
    }


    //this function is called statically by the campaign_log subpanel.
    public static function get_related_name($related_id, $related_type)
    {
        global $locale;
        $db = DBManagerFactory::getInstance();
        if ($related_type == 'Emails') {
            $query = 'SELECT name from emails where id = ? ';
            $conn = $db->getConnection();
            $stmt = $conn->executeQuery($query, [$related_id]);
            $name = $stmt->fetchOne();
            if ($name != null) {
                return $name;
            }
        }
        if ($related_type == 'Contacts') {
            $query = 'SELECT first_name, last_name from contacts where id = ? ';
            $conn = $db->getConnection();
            $stmt = $conn->executeQuery($query, [$related_id]);
            $row = $stmt->fetchAssociative();
            if ($row != null) {
                return $full_name = $locale->formatName('Contacts', $row);
            }
        }
        if ($related_type == 'Leads') {
            $query = 'SELECT first_name, last_name from leads where id = ? ';
            $conn = $db->getConnection();
            $stmt = $conn->executeQuery($query, [$related_id]);
            $row = $stmt->fetchAssociative();
            if ($row != null) {
                return $full_name = $locale->formatName('Leads', $row);
            }
        }
        if ($related_type == 'Prospects') {
            $query = 'SELECT first_name, last_name from prospects where id = ? ';
            $conn = $db->getConnection();
            $stmt = $conn->executeQuery($query, [$related_id]);
            $row = $stmt->fetchAssociative();
            if ($row != null) {
                return $full_name = $locale->formatName('Prospects', $row);
            }
        }
        if ($related_type == 'CampaignTrackers') {
            $query = 'SELECT tracker_url from campaign_trkrs where id = ? ';
            $conn = $db->getConnection();
            $stmt = $conn->executeQuery($query, [$related_id]);
            $col = $stmt->fetchOne();
            if ($col != null) {
                return $col;
            }
        }
        if ($related_type == 'Accounts') {
            $query = 'SELECT name from accounts where id= ? ';
            $conn = $db->getConnection();
            $stmt = $conn->executeQuery($query, [$related_id]);
            $name = $stmt->fetchOne();
            if ($name != null) {
                return $name;
            }
        }
        return $related_id . $related_type;
    }
}
