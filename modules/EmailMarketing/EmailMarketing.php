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

class EmailMarketing extends SugarBean
{
    public $id;
    public $deleted;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $created_by;
    public $name;
    public $from_addr;
    public $from_name;
    public $reply_to_name;
    public $reply_to_addr;
    public $date_start;
    public $time_start;
    public $template_id;
    public $campaign_id;
    public $all_prospect_lists;
    public $status;
    public $inbound_email_id;

    public $table_name = 'email_marketing';
    public $object_name = 'EmailMarketing';
    public $module_dir = 'EmailMarketing';

    public $new_schema = true;


    public function __construct()
    {
        parent::__construct();

        $this->disable_row_level_security = true;
    }

    public function get_summary_text()
    {
        return $this->name;
    }

    public function get_list_view_data($filter_fields = [])
    {

        $temp_array = $this->get_list_view_array();

        $id = $temp_array['ID'];

        //mode is set by schedule.php from campaigns module.
        if (!isset($this->mode) or empty($this->mode) or $this->mode != 'test') {
            $this->mode = 'rest';
        }

        if ($temp_array['ALL_PROSPECT_LISTS'] == 1) {
            $query = 'SELECT name from prospect_lists ';
            $query .= ' INNER JOIN prospect_list_campaigns plc ON plc.prospect_list_id = prospect_lists.id';
            $query .= ' WHERE plc.campaign_id=?';
            $query .= ' AND prospect_lists.deleted=0';
            $query .= ' AND plc.deleted=0';
            if ($this->mode == 'test') {
                $query .= " AND prospect_lists.list_type='test'";
            } else {
                $query .= " AND prospect_lists.list_type!='test'";
            }
            $param = $temp_array['CAMPAIGN_ID'];
        } else {
            $query = 'SELECT name from prospect_lists ';
            $query .= ' INNER JOIN email_marketing_prospect_lists empl ON empl.prospect_list_id = prospect_lists.id';
            $query .= ' WHERE empl.email_marketing_id=?';
            $query .= ' AND prospect_lists.deleted=0';
            $query .= ' AND empl.deleted=0';
            if ($this->mode == 'test') {
                $query .= " AND prospect_lists.list_type='test'";
            } else {
                $query .= " AND prospect_lists.list_type!='test'";
            }
            $param = $id;
        }
        $result = $this->db->getConnection()
            ->executeQuery(
                $query,
                [$param]
            );
        foreach ($result->iterateAssociative() as $row) {
            if (!empty($temp_array['PROSPECT_LIST_NAME'])) {
                $temp_array['PROSPECT_LIST_NAME'] .= '<BR>';
            }
            $temp_array['PROSPECT_LIST_NAME'] .= $row['name'];
        }
        return $temp_array;
    }

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    public function get_all_prospect_lists()
    {

        $query = 'select prospect_lists.* from prospect_lists ';
        $query .= ' left join prospect_list_campaigns on prospect_list_campaigns.prospect_list_id=prospect_lists.id';
        $query .= ' where prospect_list_campaigns.deleted=0';
        $query .= " and prospect_list_campaigns.campaign_id='$this->campaign_id'";
        $query .= ' and prospect_lists.deleted=0';
        $query .= " and prospect_lists.list_type not like 'exempt%'";

        return $query;
    }
}
