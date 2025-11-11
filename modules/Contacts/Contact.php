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

use Sugarcrm\Sugarcrm\Security\Password\Hash;
use Sugarcrm\Sugarcrm\Portal\Factory as PortalFactory;
use Sugarcrm\Sugarcrm\Util\Uuid;

/**
 *  Contact is used to store customer information.
 */
class Contact extends Person
{
    // Stored fields
    public $id;
    public $name = '';
    public $lead_source;
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
    public $title;
    public $department;
    public $birthdate;
    public $reports_to_id;
    public $do_not_call;
    public $phone_home;
    public $phone_mobile;
    public $phone_work;
    public $phone_other;
    public $phone_fax;
    public $email1;
    public $email_and_name1;
    public $email_and_name2;
    public $email2;
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
    public $portal_name;
    public $portal_app;
    public $portal_active;
    public $contacts_users_id;
    // These are for related fields
    public $bug_id;
    public $account_name;
    public $account_id;
    public $report_to_name;
    public $opportunity_role;
    public $opportunity_rel_id;
    public $opportunity_id;
    public $case_role;
    public $case_rel_id;
    public $case_id;
    public $task_id;
    public $note_id;
    public $meeting_id;
    public $call_id;
    public $email_id;
    public $assigned_user_name;
    public $accept_status;
    public $accept_status_id;
    public $accept_status_name;
    public $alt_address_street_2;
    public $alt_address_street_3;
    public $opportunity_role_id;
    public $portal_password;
    public $primary_address_street_2;
    public $primary_address_street_3;
    public $campaign_id;
    public $sync_contact;
    public $team_name;
    public $quote_role;
    public $quote_rel_id;
    public $quote_id;
    public $full_name; // l10n localized name
    public $invalid_email;
    public $table_name = 'contacts';
    public $rel_account_table = 'accounts_contacts';
    //This is needed for upgrade.  This table definition moved to Opportunity module.
    public $rel_opportunity_table = 'opportunities_contacts';
    public $rel_quotes_table = 'quotes_contacts';

    public $business_center_name;
    public $business_center_id;

    //Marketo
    public $mkto_sync;
    public $mkto_id;
    public $mkto_lead_score;

    public $object_name = 'Contact';
    public $module_dir = 'Contacts';
    public $emailAddress;
    public $new_schema = true;
    public $importable = true;
    public $portal_user_company_name;
    public $site_user_id;

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = ['bug_id', 'assigned_user_name', 'account_name', 'account_id', 'opportunity_id', 'case_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id'
        , 'quote_id',
    ];

    public $relationship_fields = [
        'account_id' => 'accounts',
        'bug_id' => 'bugs',
        'business_center_id' => 'business_centers',
        'call_id' => 'calls',
        'case_id' => 'cases',
        'email_id' => 'emails',
        'meeting_id' => 'meetings',
        'note_id' => 'notes',
        'task_id' => 'tasks',
        'opportunity_id' => 'opportunities',
        'contacts_users_id' => 'user_sync',
    ];

    /**
     * @var string Source of the contact
     */
    public $entry_source = 'internal';

    public function __construct()
    {
        parent::__construct();
    }

    public function add_list_count_joins(&$query, $where)
    {
        // accounts.name
        if (stristr($where, 'accounts.name')) {
            // add a join to the accounts table.
            $query .= '
	            LEFT JOIN accounts_contacts
	            ON contacts.id=accounts_contacts.contact_id
	            LEFT JOIN accounts
	            ON accounts_contacts.account_id=accounts.id
			';
        }
        $custom_join = $this->getCustomJoin();
        $query .= $custom_join['join'];
    }

    public function listviewACLHelper()
    {
        $array_assign = parent::listviewACLHelper();
        $is_owner = false;
        //MFH BUG 18281; JChi #15255
        $is_owner = !empty($this->assigned_user_id) && $this->assigned_user_id == $GLOBALS['current_user']->id;
        if (!ACLController::moduleSupportsACL('Accounts') || ACLController::checkAccess('Accounts', 'view', $is_owner)) {
            $array_assign['ACCOUNT'] = 'a';
        } else {
            $array_assign['ACCOUNT'] = 'span';
        }
        return $array_assign;
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

        //if this is from "contact address popup" action, then process popup list query
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ContactAddressPopup') {
            return $this->address_popup_create_new_list_query($order_by, $where, $filter, $params, $show_deleted, $join_type, $return_array, $parentbean, $singleSelect);
        } else {
            //any other action goes to parent function in sugarbean
            if (strpos($order_by, 'sync_contact') !== false) {
                //we have found that the user is ordering by the sync_contact field, it would be troublesome to sort by this field
                //and perhaps a performance issue, so just remove it
                $order_by = '';
            }
            return parent::create_new_list_query(
                $order_by,
                $where,
                $filter,
                $params,
                $show_deleted,
                $join_type,
                $return_array,
                $parentbean,
                $singleSelect,
                $ifListForExport
            );
        }
    }

    public function address_popup_create_new_list_query($order_by, $where, $filter = [], $params = [], $show_deleted = 0, $join_type = '', $return_array = false, $parentbean = null, $singleSelect = false)
    {
        $ret_array = [];
        //if this is any action that is not the contact address popup, then go to parent function in sugarbean
        if (isset($_REQUEST['action']) && $_REQUEST['action'] !== 'ContactAddressPopup') {
            return parent::create_new_list_query($order_by, $where, $filter, $params, $show_deleted, $join_type, $return_array, $parentbean, $singleSelect);
        }

        $custom_join = $this->getCustomJoin();
        // MFH - BUG #14208 creates alias name for select
        $select_query = 'SELECT ';
        $select_query .= db_concat($this->table_name, ['first_name', 'last_name']) . ' name, ';
        $select_query .= "
				$this->table_name.*,
                accounts.name as account_name,
                accounts.id as account_id,
                accounts.assigned_user_id account_id_owner,
                users.user_name as assigned_user_name ";
        $select_query .= ',teams.name AS team_name ';
        $select_query .= $custom_join['select'];
        $ret_array['select'] = $select_query;

        $from_query = '
                FROM contacts ';
        // We need to confirm that the user is a member of the team of the item.
        $this->addVisibilityFrom($from_query, ['where_condition' => true]);

        $from_query .= 'LEFT JOIN users
	                    ON contacts.assigned_user_id=users.id
	                    LEFT JOIN accounts_contacts
	                    ON contacts.id=accounts_contacts.contact_id  and accounts_contacts.deleted = 0
	                    LEFT JOIN accounts
	                    ON accounts_contacts.account_id=accounts.id AND accounts.deleted=0 ';
        $from_query .= 'LEFT JOIN teams ON contacts.team_id=teams.id AND (teams.deleted=0) ';
        $from_query .= "LEFT JOIN email_addr_bean_rel eabl  ON eabl.bean_id = contacts.id AND eabl.bean_module = 'Contacts' and eabl.primary_address = 1 and eabl.deleted=0 ";
        $from_query .= 'LEFT JOIN email_addresses ea ON (ea.id = eabl.email_address_id) ';
        $from_query .= $custom_join['join'];
        $ret_array['from'] = $from_query;
        $ret_array['from_min'] = 'from contacts';

        $where_auto = '1=1';
        if ($show_deleted == 0) {
            $where_auto = " $this->table_name.deleted=0 ";
            //$where_auto .= " AND accounts.deleted=0  ";
        } elseif ($show_deleted == 1) {
            $where_auto = " $this->table_name.deleted=1 ";
        }

        if ($where != '') {
            $where_query = "where ($where) AND " . $where_auto;
        } else {
            $where_query = 'where ' . $where_auto;
        }

        $this->addVisibilityWhere($where_query, ['where_condition' => true]);
        $acc = BeanFactory::newBean('Accounts');
        $acc->addVisibilityWhere($where_query, ['where_condition' => true, 'table_alias' => 'accounts']);

        $ret_array['where'] = $where_query;
        $ret_array['order_by'] = '';

        //process order by and add if it's not empty
        $order_by = $this->process_order_by($order_by);
        if (!empty($order_by)) {
            $ret_array['order_by'] = ' ORDER BY ' . $order_by;
        }

        if ($return_array) {
            return $ret_array;
        }

        return $ret_array['select'] . $ret_array['from'] . $ret_array['where'] . $ret_array['order_by'];
    }

    public function fill_in_additional_list_fields()
    {
        parent::fill_in_additional_list_fields();
        $this->_create_proper_name_field();

        if ($this->force_load_details == true) {
            $this->fill_in_additional_detail_fields();
        }
    }

    public function fill_in_additional_detail_fields()
    {
        parent::fill_in_additional_detail_fields();
        if (empty($this->id)) {
            return;
        }

        global $locale;

        $this->load_relationship('user_sync');
        if ($this->user_sync->_relationship->relationship_exists($GLOBALS['current_user'], $this)) {
            $this->sync_contact = true;
        } else {
            $this->sync_contact = false;
        }
        if (!empty($this->fetched_row)) {
            $this->fetched_row['sync_contact'] = $this->sync_contact;
        }

        /** concating this here because newly created Contacts do not have a
         * 'name' attribute constructed to pass onto related items, such as Tasks
         * Notes, etc.
         */
        $this->name = $locale->formatName($this);
    }

    public function get_list_view_data($filter_fields = [])
    {
        $temp_array = parent::get_list_view_data();

        if ($filter_fields && !empty($filter_fields['sync_contact'])) {
            $this->load_relationship('user_sync');
            $temp_array['SYNC_CONTACT'] = $this->user_sync->_relationship->relationship_exists(
                $GLOBALS['current_user'],
                $this
            ) ? 1 : 0;
        }

        $temp_array['EMAIL_AND_NAME1'] = "{$this->full_name} &lt;" . $temp_array['EMAIL1'] . '&gt;';

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

        array_push($where_clauses, "contacts.last_name like '$the_query_string%'");
        array_push($where_clauses, "contacts.first_name like '$the_query_string%'");
        array_push($where_clauses, "accounts.name like '$the_query_string%'");
        array_push($where_clauses, "contacts.assistant like '$the_query_string%'");
        array_push($where_clauses, "ea.email_address like '$the_query_string%'");

        if (is_numeric($the_query_string)) {
            array_push($where_clauses, "contacts.phone_home like '%$the_query_string%'");
            array_push($where_clauses, "contacts.phone_mobile like '%$the_query_string%'");
            array_push($where_clauses, "contacts.phone_work like '%$the_query_string%'");
            array_push($where_clauses, "contacts.phone_other like '%$the_query_string%'");
            array_push($where_clauses, "contacts.phone_fax like '%$the_query_string%'");
            array_push($where_clauses, "contacts.assistant_phone like '%$the_query_string%'");
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

    public function set_notification_body($xtpl, $contact)
    {
        global $locale;

        $xtpl->assign('CONTACT_NAME', $locale->formatName($contact));
        $xtpl->assign('CONTACT_DESCRIPTION', $contact->description);

        return $xtpl;
    }

    public function get_contact_id_by_email($email)
    {
        $email = trim($email);
        if (empty($email)) {
            //email is empty, no need to query, return null
            return null;
        }

        $where_clause = "(email1='$email' OR email2='$email') AND deleted=0";

        $query = "SELECT id FROM $this->table_name WHERE $where_clause";
        $GLOBALS['log']->debug("Retrieve $this->object_name: " . $query);
        $result = $this->db->getOne($query, true, "Retrieving record $where_clause:");

        return empty($result) ? null : $result;
    }

    public function save_relationship_changes($is_update, $exclude = [])
    {
        //if account_id was replaced unlink the previous account_id.
        //this rel_fields_before_value is populated by sugarbean during the retrieve call.
        if (!empty($this->account_id) and !empty($this->rel_fields_before_value['account_id']) and
            (trim($this->account_id) != trim($this->rel_fields_before_value['account_id']))) {
            //unlink the old record.
            $this->load_relationship('accounts');
            $this->accounts->delete($this->id, $this->rel_fields_before_value['account_id']);
        }
        parent::save_relationship_changes($is_update);
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

    /**
     * used by import to add a list of users
     *
     * Parameter can be one of the following:
     * - string 'all': add this contact for all users
     * - comma deliminated lists of teams and/or users
     *
     * @param string $list_of_user
     */
    public function process_sync_to_outlook($list_of_users)
    {
        static $focus_user;

        // cache this object since we'll be reusing it a bunch
        if (!($focus_user instanceof User)) {
            $focus_user = BeanFactory::newBean('Users');
        }

        static $focus_team;

        // cache this object since we'll be reusing it a bunch
        if (!($focus_team instanceof Team)) {
            $focus_team = BeanFactory::newBean('Teams');
        }

        if (empty($list_of_users)) {
            return;
        }
        if (!isset($this->users)) {
            $this->load_relationship('user_sync');
        }

        if (strtolower($list_of_users) == 'all') {
            // add all non-deleted users
            $sql = 'SELECT id FROM users WHERE deleted=0 AND is_group=0 AND portal_only=0';
            $result = $this->db->query($sql);
            while ($hash = $this->db->fetchByAssoc($result)) {
                $this->user_sync->add($hash['id']);
            }
        } else {
            $theList = explode(',', $list_of_users);
            foreach ($theList as $eachItem) {
                if (($user_id = $focus_user->retrieve_user_id($eachItem))
                    || $focus_user->retrieve($eachItem)) {
                    // it is a user, add user
                    $this->user_sync->add($user_id ?: $focus_user->id);
                    return;
                }
                if ($focus_team->retrieve($eachItem)
                    || $focus_team->retrieve_team_id($eachItem)) {
                    // it is a team, add all team members
                    $sql = "SELECT DISTINCT(user_id)
                                FROM team_memberships
                                WHERE team_id='{$focus_team->id}'
                                    AND deleted=0";
                    $result = $this->db->query($sql);
                    while ($hash = $this->db->fetchByAssoc($result)) {
                        $this->user_sync->add($hash['user_id']);
                    }
                }
            }
        }
    }

    /**
     *
     * @see parent::save()
     */
    public function save($check_notify = false)
    {
        // verify if portal_name already exists
        if (!empty($this->verifyDuplicatePortalName())) {
            throw new SugarApiExceptionNotAuthorized('ERR_PORTAL_NAME_EXISTS', [$this->portal_name], $this->module_name);
        }

        // no duplicate so get going
        if (!is_null($this->sync_contact)) {
            if (empty($this->fetched_row) || $this->fetched_row['sync_contact'] != $this->sync_contact) {
                $this->load_relationship('user_sync');
                if ($this->sync_contact) {
                    // They want to sync_contact
                    $this->user_sync->add($GLOBALS['current_user']->id);
                } else {
                    $this->user_sync->delete($this->id, $GLOBALS['current_user']->id);
                }
            }
        }

        // Support for Pendo analytics in Portal
        if (!empty($this->portal_active)) {
            $this->getSiteUserId();
        }

        // Update the datetime the consent was granted
        if (!empty($this->cookie_consent) && empty($this->cookie_consent_received_on)) {
            $this->cookie_consent_received_on = TimeDate::getInstance()->nowDb();
        }
        // Wipe the datetime if the consent was revoked
        if (empty($this->cookie_consent) && !empty($this->cookie_consent_received_on)) {
            $this->cookie_consent_received_on = null;
        }

        //Set business_center_id to the same as related account when not provided
        if ($this->load_relationship('accounts')) {
            $accounts = $this->accounts->getBeans();
            if ($accounts) {
                $account = array_shift($accounts);
                if ($account->business_center_id) {
                    $this->business_center_id = $account->business_center_id;
                }
            }
        }

        return parent::save($check_notify);
    }

    /**
     * Gets and sets a site user id for analytics
     * @param boolean $save Whether to save updates on set, defaults to false
     * @return string
     */
    public function getSiteUserId($save = false)
    {
        if (empty($this->site_user_id)) {
            if (!$this->id) {
                $this->id = Uuid::uuid1();
                $this->new_with_id = true;
            }

            $this->site_user_id = getSiteHash($this->id);

            // Handle saving only if explicitly told to
            if ($save) {
                $this->save();
            }
        }

        return $this->site_user_id;
    }

    /**
     * Checks if a duplicate portal_name exists in the DB
     * @return false|string
     */
    public function verifyDuplicatePortalName()
    {
        // ignore if not changed
        if (isset($this->fetched_row['portal_name']) &&
            $this->portal_name === $this->fetched_row['portal_name']) {
            return false;
        }
        $query = new SugarQuery();
        $query->select(['portal_name']);
        $query->from($this);
        $query->where()
            ->equals('portal_name', $this->portal_name);
        if (!empty($this->id)) {
            $query->where()->notEquals('id', $this->id);
        }
        // just one duplicate record will do the trick
        return $query->getOne();
    }

    /**
     * Attempt to rehash the current portal password hash
     * @param string $password Clear text password
     */
    public function rehashPortalPassword($password)
    {
        if (empty($this->id) || empty($this->portal_password) || empty($password)) {
            return;
        }

        $hashBackend = Hash::getInstance();

        if ($hashBackend->needsRehash($this->portal_password)) {
            if ($newHash = $hashBackend->hash($password)) {
                $update = sprintf(
                    'UPDATE %s SET portal_password = %s WHERE id = %s',
                    $this->table_name,
                    $this->db->quoted($newHash),
                    $this->db->quoted($this->id)
                );
                $this->db->query($update);
                $GLOBALS['log']->info("Rehashed portal password for contact id '{$this->id}'");
            } else {
                $GLOBALS['log']->warn("Error trying to rehash portal password for contact id '{$this->id}'");
            }
        }
    }

    /**
     * {@inheritDoc}
     *
     * In Portal, allows to access only the logged in Contact.
     */
    public function getOwnerWhere($user_id, $table_alias = null)
    {
        $portalSession = PortalFactory::getInstance('Session');
        if ($portalSession->isActive() && ($contactId = $portalSession->getContactId())) {
            if ($table_alias === null) {
                $table_alias = $this->table_name;
            }
            return $table_alias . '.id = ' . $this->db->quoted($contactId);
        }

        return parent::getOwnerWhere($user_id, $table_alias);
    }

    /**
     * Provides the dropdown list elements needed for the `entry_source`. This is
     * a system status so it should not be editable in the dropdownlist editor,
     * thus it is wrapped in a function. However, the values should be localizable
     * hence the use of labels.
     * @return array
     */
    public function getSourceTypes()
    {
        return [
            'external' => translate('LBL_SOURCE_EXTERNAL', 'Contacts'),
            'internal' => translate('LBL_SOURCE_INTERNAL', 'Contacts'),
        ];
    }
}
