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


class Prospect extends Person
{
    // Stored fields
    public $id;
    public $name = '';
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $assigned_user_id;
    public $created_by;
    public $created_by_name;
    public $modified_by_name;
    public $team_id;
    public $description;
    public $salutation;
    public $first_name;
    public $last_name;
    public $full_name;
    public $title;
    public $department;
    public $birthdate;
    public $do_not_call;
    public $phone_home;
    public $phone_mobile;
    public $phone_work;
    public $phone_other;
    public $phone_fax;
    public $email1;
    public $email2;
    public $email_and_name1;
    public $assistant;
    public $assistant_phone;
    public $email_opt_out;
    public $primary_address_street;
    public $primary_address_city;
    public $primary_address_state;
    public $primary_address_postalcode;
    public $primary_address_country;
    public $alt_address_street;
    public $alt_address_city;
    public $alt_address_state;
    public $alt_address_postalcode;
    public $alt_address_country;
    public $tracker_key;
    public $lead_id;
    public $account_name;
    public $assigned_real_user_name;
    // These are for related fields
    public $assigned_user_name;
    public $team_name;
    public $module_dir = 'Prospects';
    public $table_name = 'prospects';
    public $object_name = 'Prospect';
    public $new_schema = true;
    public $emailAddress;

    public $importable = true;
    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = ['assigned_user_name'];

    public function fill_in_additional_list_fields()
    {
        parent::fill_in_additional_list_fields();
        $this->_create_proper_name_field();
        $this->email_and_name1 = $this->full_name . " &lt;" . $this->email1 . "&gt;";
    }

    public function fill_in_additional_detail_fields()
    {
        parent::fill_in_additional_list_fields();
        $this->_create_proper_name_field();
    }

    /**
     * builds a generic search based on the query string using or
     * do not include any $this-> because this is called on without having the class instantiated
     */
    public function build_generic_where_clause($the_query_string)
    {
        $where_clauses = [];
        $the_query_string = $GLOBALS['db']->quote($the_query_string);

        array_push($where_clauses, "prospects.last_name like '$the_query_string%'");
        array_push($where_clauses, "prospects.first_name like '$the_query_string%'");
        array_push($where_clauses, "prospects.assistant like '$the_query_string%'");

        if (is_numeric($the_query_string)) {
            array_push($where_clauses, "prospects.phone_home like '%$the_query_string%'");
            array_push($where_clauses, "prospects.phone_mobile like '%$the_query_string%'");
            array_push($where_clauses, "prospects.phone_work like '%$the_query_string%'");
            array_push($where_clauses, "prospects.phone_other like '%$the_query_string%'");
            array_push($where_clauses, "prospects.phone_fax like '%$the_query_string%'");
            array_push($where_clauses, "prospects.assistant_phone like '%$the_query_string%'");
        }

        $the_where = '';
        foreach ($where_clauses as $clause) {
            if ($the_where != '') {
                $the_where .= ' or ';
            }
            $the_where .= $clause;
        }


        return $the_where;
    }

    public function converted_prospect($prospectid, $contactid, $accountid, $opportunityid)
    {
        $query = "UPDATE prospects set  contact_id=$contactid, account_id=$accountid, opportunity_id=$opportunityid where  id=$prospectid and deleted=0";
        $this->db->query($query, true, 'Error converting prospect: ');
        //todo--status='Converted', converted='1',
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
     *  This method will be used by Mail Merge in order to retieve the targets as specified in the query
     * @param query String - this is the query which contains the where clause for the query
     */
    public function retrieveTargetList($query, $fields, $offset = 0, $limit = -99, $max = -99, $deleted = 0, $module = '')
    {
        global $beanList, $beanFiles;
        $module_name = $this->module_dir;

        if (empty($module)) {
            //The call to retrieveTargetList contains a query that may contain a pound token
            $pattern = '/AND related_type = [\'#]([a-zA-Z]+)[\'#]/i';
            if (preg_match($pattern, $query, $matches)) {
                $module_name = $matches[1];
                $query = preg_replace($pattern, '', $query);
            }
        }

        $count = safeCount($fields);
        $index = 1;
        $sel_fields = '';
        if (!empty($fields)) {
            foreach ($fields as $field) {
                if ($field == 'id') {
                    $sel_fields .= 'prospect_lists_prospects.id id';
                } else {
                    $sel_fields .= strtolower($module_name) . '.' . $field;
                }
                if ($index < $count) {
                    $sel_fields .= ',';
                }
                $index++;
            }
        }

        $module_name = ucfirst($module_name);
        $seed = BeanFactory::newBean($module_name);
        if (empty($sel_fields)) {
            $sel_fields = $seed->table_name . '.*';
        }
        $select = 'SELECT ' . $sel_fields . ' FROM ' . $seed->table_name;
        $select .= ' INNER JOIN prospect_lists_prospects ON prospect_lists_prospects.related_id = ' . $seed->table_name . '.id';
        $select .= ' INNER JOIN prospect_lists ON prospect_lists_prospects.prospect_list_id = prospect_lists.id';
        $select .= ' INNER JOIN prospect_list_campaigns ON prospect_list_campaigns.prospect_list_id = prospect_lists.id';
        $select .= ' INNER JOIN campaigns on campaigns.id = prospect_list_campaigns.campaign_id';
        $select .= ' WHERE prospect_list_campaigns.deleted = 0';
        $select .= ' AND prospect_lists_prospects.deleted = 0';
        $select .= ' AND prospect_lists.deleted = 0';
        if (!empty($query)) {
            $select .= ' AND ' . $query;
        }

        return $this->process_list_query($select, $offset, $limit, $max, $query);
    }

    /**
     *  Given an id, looks up in the prospect_lists_prospects table
     *  and retrieve the correct type for this id
     */
    public function retrieveTarget($id)
    {
        $query = "SELECT related_id, related_type FROM prospect_lists_prospects WHERE id = '" . $this->db->quote($id) . "'";
        $result = $this->db->query($query);
        if (($row = $this->db->fetchByAssoc($result))) {
            return BeanFactory::retrieveBean($row['related_type'], $row['related_id']);
        } else {
            return null;
        }
    }


    public function get_unlinked_email_query($type = [])
    {

        return get_unlinked_email_query($type, $this);
    }
}
