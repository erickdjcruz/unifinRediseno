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
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 ********************************************************************************/
// Account is used to store account information.
class Account extends Company
{
    // Stored fields
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $assigned_user_id;
    public $annual_revenue;
    public $billing_address_street;
    public $billing_address_city;
    public $billing_address_state;
    public $billing_address_country;
    public $billing_address_postalcode;

    public $billing_address_street_2;
    public $billing_address_street_3;
    public $billing_address_street_4;

    public $description;
    public $email1;
    public $email2;
    public $email_opt_out;
    public $invalid_email;
    public $employees;
    public $id;
    public $industry;
    public $name;
    public $ownership;
    public $parent_id;
    public $phone_alternate;
    public $phone_fax;
    public $phone_office;
    public $rating;
    public $shipping_address_street;
    public $shipping_address_city;
    public $shipping_address_state;
    public $shipping_address_country;
    public $shipping_address_postalcode;

    public $shipping_address_street_2;
    public $shipping_address_street_3;
    public $shipping_address_street_4;

    public $campaign_id;

    public $sic_code;
    public $ticker_symbol;
    public $account_type;
    public $website;
    public $custom_fields;

    public $created_by;
    public $created_by_name;
    public $modified_by_name;

    public $service_level;

    // These are for related fields
    public $opportunity_id;
    public $case_id;
    public $contact_id;
    public $task_id;
    public $note_id;
    public $meeting_id;
    public $call_id;
    public $email_id;
    public $member_id;
    public $parent_name;
    public $assigned_user_name;
    public $account_id = '';
    public $account_name = '';
    public $bug_id = '';
    public $module_dir = 'Accounts';
    public $emailAddress;

    public $business_center_name;
    public $business_center_id;

    public $team_name;
    public $team_id;
    public $quote_id;
    public $rel_quote_account_table = 'quotes_accounts';
    public $quote_table = 'quotes';

    public $table_name = 'accounts';
    public $object_name = 'Account';
    public $importable = true;
    public $new_schema = true;
    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = ['assigned_user_name', 'assigned_user_id', 'opportunity_id', 'bug_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id'
        , 'quote_id',
    ];
    public $relationship_fields = [
        'bug_id' => 'bugs',
        'business_center_id' => 'business_centers',
        'call_id' => 'calls',
        'case_id' => 'cases',
        'contact_id' => 'contacts',
        'email_id' => 'emails',
        'meeting_id' => 'meetings',
        'member_id' => 'members',
        'note_id' => 'notes',
        'opportunity_id' => 'opportunities',
        'project_id' => 'project',
        'quote_id' => 'quotes',
        'task_id' => 'tasks',
    ];

    //Meta-Data Framework fields
    public $push_billing;
    public $push_shipping;


    public function __construct()
    {
        parent::__construct();

        //Email logic
        if (!empty($_REQUEST['parent_id']) && !empty($_REQUEST['parent_type']) && $_REQUEST['parent_type'] == 'Emails'
            && !empty($_REQUEST['return_module']) && $_REQUEST['return_module'] == 'Emails') {
            $_REQUEST['parent_name'] = '';
            $_REQUEST['parent_id'] = '';
        }
    }

    public function get_summary_text()
    {
        return $this->name;
    }

    public function get_contacts()
    {
        return $this->get_linked_beans('contacts', 'Contact');
    }

    public function get_list_view_data($filter_fields = [])
    {

        $temp_array = parent::get_list_view_data();

        $temp_array['ENCODED_NAME'] = $this->name;

        if (!empty($this->billing_address_state)) {
            $temp_array['CITY'] = $this->billing_address_city . ', ' . $this->billing_address_state;
        } else {
            $temp_array['CITY'] = $this->billing_address_city;
        }
        $temp_array['BILLING_ADDRESS_STREET'] = $this->billing_address_street;
        $temp_array['SHIPPING_ADDRESS_STREET'] = $this->shipping_address_street;

        return $temp_array;
    }

    /**
     * builds a generic search based on the query string using or
     * do not include any $this-> because this is called on without having the class instantiated
     */
    public function build_generic_where_clause($the_query_string)
    {
        $where_clauses = [];
        $the_query_string = $this->db->quote($the_query_string);
        array_push($where_clauses, "accounts.name like '$the_query_string%'");
        if (is_numeric($the_query_string)) {
            array_push($where_clauses, "accounts.phone_alternate like '%$the_query_string%'");
            array_push($where_clauses, "accounts.phone_fax like '%$the_query_string%'");
            array_push($where_clauses, "accounts.phone_office like '%$the_query_string%'");
        }

        $the_where = '';
        foreach ($where_clauses as $clause) {
            if (!empty($the_where)) {
                $the_where .= ' or ';
            }
            $the_where .= $clause;
        }

        return $the_where;
    }

    public function set_notification_body($xtpl, $account)
    {
        $xtpl->assign('ACCOUNT_NAME', $account->name);
        $xtpl->assign('ACCOUNT_TYPE', $account->account_type);
        $xtpl->assign('ACCOUNT_DESCRIPTION', $account->description);

        return $xtpl;
    }

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    public function get_unlinked_email_query($type = [])
    {

        return get_unlinked_email_query($type, $this);
    }
}
