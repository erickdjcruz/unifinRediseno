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

use Sugarcrm\Sugarcrm\Entitlements\Subscription;

// Opportunity is used to store customer information.
class Opportunity extends SugarBean
{
    public const STAGE_CLOSED_WON = 'Closed Won';
    public const STAGE_CLOSED_LOST = 'Closed Lost';

    public const STATUS_NEW = 'New';
    public const STATUS_IN_PROGRESS = 'In Progress';
    public const STATUS_CLOSED_WON = 'Closed Won';
    public const STATUS_CLOSED_LOST = 'Closed Lost';

    // Stored fields
    public $id;
    public $lead_source;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $assigned_user_id;
    public $created_by;
    public $created_by_name;
    public $modified_by_name;
    public $description;
    public $name;
    public $opportunity_type;
    public $amount;
    public $amount_usdollar;
    public $currency_id;
    public $base_rate;
    public $date_closed;
    public $date_closed_timestamp;
    public $next_step;
    public $sales_stage;
    public $sales_status;
    public $probability;
    public $campaign_id;
    public $team_name;
    public $team_id;
    public $quote_id;
    public $service_start_date;
    public $forecasted_likely;
    public $service_duration_value;
    public $service_duration_unit;
    public $service_open_flex_duration_rlis;

    // These are related
    public $account_name;
    public $account_id;
    public $contact_id;
    public $task_id;
    public $note_id;
    public $meeting_id;
    public $call_id;
    public $email_id;
    public $assigned_user_name;

    public $table_name = 'opportunities';
    public $rel_account_table = 'accounts_opportunities';
    public $rel_contact_table = 'opportunities_contacts';
    public $module_dir = 'Opportunities';
    public $rel_quote_table = 'quotes_opportunities';
    public $best_case;
    public $worst_case;
    public $timeperiod_id;
    public $commit_stage;

    /**
     * Fields that should cascade down to RLIs on save in Opps + RLIs mode.
     *
     * These are in the form of
     * [
     *   $oppFieldName => $methodName
     * ]
     *
     * These will cascade the given opportunity field down to related RLIs when the method returns true.
     * Methods should be non-static, defined here in Opportunity.php, and take one RLI parameter,
     * and return a boolean indicating whether or not values should cascade to that RLI.
     */
    public const CASCADE_FIELD_CONDITIONS = [
        'date_closed' => 'cascadeWhenOpen',
        'service_start_date' => 'cascadeWhenServiceOpen',
        'service_duration_value' => 'cascadeWhenDurationEditableServiceOpen',
        'service_duration_unit' => 'cascadeWhenDurationEditableServiceOpen',
        'commit_stage' => 'cascadeWhenOpen',
        'sales_stage' => 'cascadeWhenOpen',
    ];
    private const OPERATION_CASCADE = 'cascading_opportunity_';

    //Marketo
    public $mkto_sync;
    public $mkto_id;

    /**
     * holds the settings for the Forecast Module
     *
     * @var array
     */
    public static $settings = [];

    public $importable = true;
    public $object_name = 'Opportunity';

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = [
        'assigned_user_name',
        'assigned_user_id',
        'account_name',
        'account_id',
        'contact_id',
        'task_id',
        'note_id',
        'meeting_id',
        'call_id',
        'email_id'
        ,
        'quote_id',
    ];

    public $relationship_fields = [
        'task_id' => 'tasks',
        'note_id' => 'notes',
        'account_id' => 'accounts',
        'meeting_id' => 'meetings',
        'call_id' => 'calls',
        'email_id' => 'emails',
        'project_id' => 'project',
        // Bug 38529 & 40938
        'currency_id' => 'currencies',
        'quote_id' => 'quotes',
    ];

    /**
     * Fields for which we will disable imports.
     *
     * @var array
     */
    public $disableImportFields = [];


    public function __construct()
    {
        parent::__construct();
        global $sugar_config;

        if (empty($sugar_config['require_accounts'])) {
            unset($this->required_fields['account_name']);
        }
        if (!isset($GLOBALS['installing']) || !$GLOBALS['installing']) {
            $this->setDisabledImportFields();
        }
    }


    public $new_schema = true;


    /**
     * Return the a Summary for the Record
     *
     * @return string
     */
    public function get_summary_text()
    {
        return "$this->name";
    }


    /**
     * This is no longer used and is considered deprecated.  It will be removed in a future release.
     *
     * @deprecated
     */
    public function create_list_query($order_by, $where, $show_deleted = 0)
    {
        $GLOBALS['log']->deprecated('Opportunity::create_list_query() has been deprecated in 7.8');
        $custom_join = $this->custom_fields->getJOIN();
        $query = 'SELECT ';

        $query .= '
                            accounts.id as account_id,
                            accounts.name as account_name,
                            accounts.assigned_user_id account_id_owner,
                            users.user_name as assigned_user_name ';
        $query .= ',teams.name AS team_name ';
        if ($custom_join) {
            $query .= $custom_join['select'];
        }
        $query .= ' ,opportunities.*
                            FROM opportunities ';

        // We need to confirm that the user is a member of the team of the item.
        $this->add_team_security_where_clause($query);
        $query .= 'LEFT JOIN users
                            ON opportunities.assigned_user_id=users.id ';
        $query .= getTeamSetNameJoin('opportunities');

        $query .= ' LEFT JOIN timeperiods
                        ON timeperiods.start_date_timestamp <= opportunities.date_closed_timestamp
                        AND timeperiods.end_date_timestamp >= opportunities.date_closed_timestamp ';

        $query .= "LEFT JOIN $this->rel_account_table
                            ON opportunities.id=$this->rel_account_table.opportunity_id
                            LEFT JOIN accounts
                            ON $this->rel_account_table.account_id=accounts.id ";
        if ($custom_join) {
            $query .= $custom_join['join'];
        }
        $where_auto = '1=1';
        if ($show_deleted == 0) {
            $where_auto = "
			($this->rel_account_table.deleted is null OR $this->rel_account_table.deleted=0)
			AND (accounts.deleted is null OR accounts.deleted=0)
			AND opportunities.deleted=0";
        } else {
            if ($show_deleted == 1) {
                $where_auto = ' opportunities.deleted=1';
            }
        }

        if ($where != '') {
            $query .= "where ($where) AND " . $where_auto;
        } else {
            $query .= 'where ' . $where_auto;
        }

        if ($order_by != '') {
            $query .= " ORDER BY $order_by";
        } else {
            $query .= ' ORDER BY opportunities.name';
        }

        return $query;
    }

    /**
     * This is no longer used and is considered deprecated.  It will be removed in a future release.
     *
     * @deprecated
     */
    public function fill_in_additional_list_fields()
    {
        $GLOBALS['log']->deprecated('Opportunity::fill_in_additional_list_fields() has been deprecated in 7.8');
        if ($this->force_load_details == true) {
            $this->fill_in_additional_detail_fields();
        }
    }


    /**
     * @deprecated Not used in the REST API, will be removed in a future version
     */
    public function fill_in_additional_detail_fields()
    {
        $GLOBALS['log']->deprecated('Opportunity::fill_in_additional_detail_fields() has been deprecated in 7.8');
        parent::fill_in_additional_detail_fields();

        if (!empty($this->currency_id)) {
            $currency = BeanFactory::getBean('Currencies', $this->currency_id);
            if ($currency->id != $this->currency_id || $currency->deleted == 1) {
                $this->amount = $this->amount_usdollar;
                $this->currency_id = $currency->id;
            }
        }
    }

    /**
     * Returns a list of the associated contacts
     *
     * This is no longer used and is considered deprecated.  It will be removed in a future release.
     *
     * @deprecated
     */
    public function get_contacts()
    {
        $GLOBALS['log']->deprecated('Opportunity::get_contacts() has been deprecated in 7.8');
        $this->load_relationship('contacts');
        $query_array = $this->contacts->getQuery(true);

        //update the select clause in the retruned query.
        $query_array['select'] = 'SELECT contacts.id, contacts.first_name, contacts.last_name, contacts.title, contacts.email1, contacts.phone_work, opportunities_contacts.contact_role as opportunity_role, opportunities_contacts.id as opportunity_rel_id ';

        $query = '';
        foreach ($query_array as $qstring) {
            $query .= ' ' . $qstring;
        }
        $temp = [
            'id',
            'first_name',
            'last_name',
            'title',
            'email1',
            'phone_work',
            'opportunity_role',
            'opportunity_rel_id',
        ];
        return $this->build_related_list2($query, BeanFactory::newBean('Contacts'), $temp);
    }


    /**
     * This is no longer used and is considered deprecated.  It will be removed in a future release.
     *
     * @param string $fromid
     * @param string $toid
     * @deprecated
     */
    public function update_currency_id($fromid, $toid)
    {
        $GLOBALS['log']->deprecated('Opportunity::update_currency_id() has been deprecated in 7.8');
        $idequals = '';

        $currency = BeanFactory::getBean('Currencies', $toid);
        foreach ($fromid as $f) {
            if (!empty($idequals)) {
                $idequals .= ' or ';
            }
            $idequals .= 'currency_id=' . $this->db->quoted($f);
        }

        if (!empty($idequals)) {
            $query = 'select amount, id from opportunities where (' . $idequals . ") and deleted=0 and opportunities.sales_stage <> '" . self::STAGE_CLOSED_WON . "' AND opportunities.sales_stage <> '" . self::STAGE_CLOSED_LOST . "';";
            $result = $this->db->query($query);

            while ($row = $this->db->fetchByAssoc($result)) {
                $query = sprintf(
                    'UPDATE opportunities SET currency_id = %s, amount_usdollar = %s, base_rate = %s WHERE id = %s;',
                    $this->db->quoted($currency->id),
                    $this->db->quoted(SugarCurrency::convertAmountToBase($row['amount'], $currency->id)),
                    $this->db->quoted($currency->conversion_rate),
                    $this->db->quoted($row['id'])
                );
                $this->db->query($query);
            }
        }
    }


    /**
     * This is no longer used and is considered deprecated.  It will be removed in a future release.
     *
     * @deprecated
     */
    public function get_list_view_data($filter_fields = [])
    {
        $GLOBALS['log']->deprecated('Opportunity::get_list_view_data() has been deprecated in 7.8');
        global $locale, $current_language, $current_user, $mod_strings, $app_list_strings, $sugar_config;
        $app_strings = return_application_language($current_language);
        $params = [];

        $temp_array = $this->get_list_view_array();
        $temp_array['SALES_STAGE'] = empty($temp_array['SALES_STAGE']) ? '' : $temp_array['SALES_STAGE'];
        $temp_array['ENCODED_NAME'] = $this->name;
        return $temp_array;
    }


    /**
     * This is no longer used and is considered deprecated.  It will be removed in a future release.
     *
     * @deprecated
     */
    public function get_currency_symbol()
    {
        $GLOBALS['log']->deprecated('Opportunity::get_currency_symbol() has been deprecated in 7.8');
        if (isset($this->currency_id)) {
            $cur_qry = "select * from currencies where id ='" . $this->currency_id . "'";
            $cur_res = $this->db->query($cur_qry);
            if (!empty($cur_res)) {
                $cur_row = $this->db->fetchByAssoc($cur_res);
                if (isset($cur_row['symbol'])) {
                    return $cur_row['symbol'];
                }
            }
        }
        return '';
    }

    /**
     * To check whether currency_id field is changed during save.
     * @return bool true if currency_id is changed, false otherwise
     */
    protected function isCurrencyIdChanged()
    {
        // if both are defined, compare
        if (isset($this->currency_id) && isset($this->fetched_row['currency_id'])) {
            if ($this->currency_id != $this->fetched_row['currency_id']) {
                return true;
            }
        }
        // one is not defined, the other one is not empty, means changed
        if (!isset($this->currency_id) && !empty($this->fetched_row['currency_id'])) {
            return true;
        }
        if (!isset($this->fetched_row['currency_id']) && !empty($this->currency_id)) {
            return true;
        }

        return false;
    }

    /**
     * builds a generic search based on the query string using or
     * do not include any $this-> because this is called on without having the class instantiated
     */
    public function build_generic_where_clause($the_query_string)
    {
        $where_clauses = [];
        $the_query_string = $GLOBALS['db']->quote($the_query_string);
        array_push($where_clauses, "opportunities.name like '$the_query_string%'");
        array_push($where_clauses, "accounts.name like '$the_query_string%'");

        $the_where = '';
        foreach ($where_clauses as $clause) {
            if ($the_where != '') {
                $the_where .= ' or ';
            }
            $the_where .= $clause;
        }

        return $the_where;
    }

    /**
     * Bean specific logic for when SugarFieldCurrency_id::save() is called to make sure we can update the base_rate
     *
     * @return bool
     */
    public function updateCurrencyBaseRate()
    {
        return !in_array($this->sales_stage, $this->getClosedStages());
    }

    public function save($check_notify = false)
    {
        //if probability is empty, set it based on the sales stage
        if ($this->probability === '' && !empty($this->sales_stage)) {
            $this->mapProbabilityFromSalesStage();
        }

        //if the id is set (previously saved bean) and sales_status is still New, update to in progress
        if (isset($this->id) && !$this->new_with_id && $this->sales_status == Opportunity::STATUS_NEW) {
            $this->sales_status = Opportunity::STATUS_IN_PROGRESS;
        }
        // trigger cascading changes down to open RLIs based on what data is being updated here
        if ($this->isUpdate() && $this->shouldCascade()) {
            $this->cascade();
        }

        // verify that base_rate is set to the correct amount, moved in from SugarBean
        // as we need this to run before perform_save (which does calculations with base_rate)
        if (isset($this->field_defs['currency_id']) && isset($this->field_defs['base_rate'])) {
            SugarCurrency::verifyCurrencyBaseRateSet($this);
        }

        SugarAutoLoader::requireWithCustom('modules/Opportunities/SaveOverload.php');
        perform_save($this);

        return parent::save($check_notify);
    }

    public function save_relationship_changes($is_update, $exclude = [])
    {
        //if account_id was replaced unlink the previous account_id.
        //this rel_fields_before_value is populated by sugarbean during the retrieve call.
        if (!empty($this->account_id) and !empty($this->rel_fields_before_value['account_id']) and
            (trim($this->account_id) != trim($this->rel_fields_before_value['account_id']))
        ) {
            //unlink the old record.
            $this->load_relationship('accounts');
            $this->accounts->delete($this->id, $this->rel_fields_before_value['account_id']);
            //propagate change down to related beans
            $relationshipsToBeTouched = ['products', 'revenuelineitems'];
            foreach ($relationshipsToBeTouched as $relationship) {
                $this->load_relationship($relationship);
                foreach ($this->$relationship->getBeans() as $bean) {
                    $bean->account_id = $this->account_id;
                    $bean->save();
                }
            }
        }
        // Bug 38529 & 40938 - exclude currency_id
        parent::save_relationship_changes($is_update, ['currency_id']);

        if (!empty($this->contact_id)) {
            $this->set_opportunity_contact_relationship($this->contact_id);
        }
    }


    public function set_opportunity_contact_relationship($contact_id)
    {
        global $app_list_strings;
        $default = $app_list_strings['opportunity_relationship_type_default_key'];
        $this->load_relationship('contacts');
        $this->contacts->add($contact_id, ['contact_role' => $default]);
    }


    public function set_notification_body($xtpl, $oppty)
    {
        global $app_list_strings;

        $xtpl->assign('OPPORTUNITY_NAME', $oppty->name);
        $xtpl->assign('OPPORTUNITY_AMOUNT', $oppty->amount);
        $xtpl->assign('OPPORTUNITY_CLOSEDATE', $oppty->date_closed);

        $oppStage = '';
        if (isset($oppty->sales_stage) && !empty($oppty->sales_stage)) {
            $oppStage = $app_list_strings['sales_stage_dom'][$oppty->sales_stage];
        }
        $xtpl->assign('OPPORTUNITY_STAGE', $oppStage);

        $xtpl->assign('OPPORTUNITY_DESCRIPTION', $oppty->description);

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


    /**
     * This is no longer used since Opportunities is not in BWC.  This will be removed in a future version
     *
     * @return array
     * @deprecated
     *
     */
    public function listviewACLHelper()
    {
        $GLOBALS['log']->deprecated('Opportunity::listviewACLHelper() has been deprecated in 7.8');
        $array_assign = parent::listviewACLHelper();
        $is_owner = false;
        if (!empty($this->account_id)) {
            if (!empty($this->account_id_owner)) {
                global $current_user;
                $is_owner = $current_user->id == $this->account_id_owner;
            }
        }
        if (!ACLController::moduleSupportsACL('Accounts') ||
            ACLController::checkAccess('Accounts', 'view', $is_owner)) {
            $array_assign['ACCOUNT'] = 'a';
        } else {
            $array_assign['ACCOUNT'] = 'span';
        }

        return $array_assign;
    }

    /**
     * getClosedStages
     *
     * Return an array of closed stage names from the admin bean.
     *
     * @access public
     * @return array array of closed stage values
     */
    public function getClosedStages()
    {
        // TODO: move closed stages to a global setting.
        // For now, get them from forecasting.
        static $stages;
        if (!isset($stages)) {
            $admin = BeanFactory::newBean('Administration');
            $settings = $admin->getConfigForModule('Forecasts');

            // get all possible closed stages
            $stages = array_merge(
                isset($settings['sales_stage_won']) ? (array)$settings['sales_stage_won'] : [],
                isset($settings['sales_stage_lost']) ? (array)$settings['sales_stage_lost'] : []
            );
        }
        return $stages;
    }

    /**
     * Handling mapping the probability from the sales stage.
     */
    protected function mapProbabilityFromSalesStage()
    {
        global $app_list_strings;
        $prob_arr = $app_list_strings['sales_probability_dom'];
        if (isset($prob_arr[$this->sales_stage])) {
            $this->probability = $prob_arr[$this->sales_stage];
        }
    }

    public static function getSettings($reload = false)
    {
        /* @var $admin Administration */
        if (empty(static::$settings) || $reload === true) {
            $admin = BeanFactory::newBean('Administration');
            static::$settings = $admin->getConfigForModule('Opportunities');
        }

        return static::$settings;
    }


    /**
     * Return an array of RLI closed won stage names.
     *
     * @return array array of RLI closed won stage values
     */
    public function getRliClosedWonStages(): array
    {
        return Forecast::getSettings()['sales_stage_won'] ?? [self::STAGE_CLOSED_WON];
    }

    /**
     * Return an array of RLI closed lost stage names.
     *
     * @return array array of RLI closed lost stage values
     */
    public function getRliClosedLostStages(): array
    {
        return Forecast::getSettings()['sales_stage_lost'] ?? [self::STAGE_CLOSED_LOST];
    }

    /**
     * Check if we can renew opportunity.
     *
     * @return bool
     */
    public function canRenew(): bool
    {
        // Renewals are only supported for instances using RLIs
        return static::usingRevenueLineItems();
    }

    /**
     * If we are in Opps + RLIs mode, disable importing our cascading fields..
     */
    public function setDisabledImportFields()
    {
        $settings = Opportunity::getSettings();
        if (isset($settings['opps_view_by']) && $settings['opps_view_by'] === 'RevenueLineItems') {
            $this->disableImportFields = array_keys(self::CASCADE_FIELD_CONDITIONS);
        }
    }

    /**
     * Retrieve and update fetched_row['sales_status'] from db.
     */
    public function retrieveSalesStatus()
    {
        if (!empty($this->id)) {
            $query = new \SugarQuery();
            $query->from($this, ['add_deleted' => 1, 'team_security' => false]);
            $query->select('sales_status');
            $query->where()->equals('id', $this->id);
            $query->limit(1);
            $results = $query->execute();
            if (!empty($results)) {
                $row = $results[0];
                $row = $this->convertRow($row);
                if (empty($this->fetched_row)) {
                    $this->fetched_row = [];
                }
                $this->fetched_row['sales_status'] = $row['sales_status'];
            }
        }
    }


    /**
     * Get renewal parent.
     *
     * @return Opportunity|NULL
     */
    public function getRenewalParent(): ?Opportunity
    {
        if (!empty($this->renewal_parent_id)) {
            return BeanFactory::getBean($this->getModuleName(), $this->renewal_parent_id);
        }
        return null;
    }

    /**
     * Get 'Closed Won' and renewable RLIs for this opportunity.
     *
     * @return array
     */
    public function getClosedWonRenewableRLIs(): array
    {
        $rliBeans = [];
        $closedWon = $this->getRliClosedWonStages();

        if ($this->load_relationship('revenuelineitems')) {
            $rliBean = BeanFactory::getBean($this->revenuelineitems->getRelatedModuleName());
            $whereTable = $rliBean->getTableName();

            $params = [
                "$whereTable.service = 1",
                "$whereTable.sales_stage in ('" . implode("','", $closedWon) . "')",
                "$whereTable.renewable = 1",
            ];

            $rliBeans = $this->revenuelineitems->getBeans([
                'where' => implode(' AND ', $params),
            ]);
        }

        return $rliBeans;
    }

    /**
     * Get the ids of Related RLIs with "Generate Purchase" => Yes
     *
     * @return array ids of RLIs
     * @throws SugarQueryException
     */
    public function getGeneratePurchaseRliIds()
    {
        $q = new SugarQuery();
        $q->from(BeanFactory::newBean('RevenueLineItems'));
        $q->select(['id']);
        $q->where()->queryAnd()
            ->equals('opportunity_id', $this->id)
            ->equals('generate_purchase', 'Yes');
        return $q->execute();
    }

    /**
     * Get existing renewal opportunity.
     *
     * @return Opportunity|NULL
     */
    public function getExistingRenewalOpportunity(): ?Opportunity
    {
        $renewalBean = null;
        if ($this->load_relationship('renewal_opportunities')) {
            $whereTable = $this->getTableName();

            $params = [
                "$whereTable.sales_status != " . $this->db->quoted(Opportunity::STATUS_CLOSED_WON),
                "$whereTable.sales_status != " . $this->db->quoted(Opportunity::STATUS_CLOSED_LOST),
                "$whereTable.renewal = 1",
            ];

            $renewalBeans = $this->renewal_opportunities->getBeans([
                'where' => implode(' AND ', $params),
            ]);

            if (!empty($renewalBeans)) {
                $renewalBean = array_shift($renewalBeans);
            }
        }

        return $renewalBean;
    }

    /**
     * Create/update renewal RLIs if Add-On-To PLI is linked to an open renewal Opp
     *
     * #1. Closed Won renewable RLIs with an Add-On-To PLI that links to an open renewal Opp where the renewal Opp
     *    has no existing open RLI whose Product matches my newly won RLI, create a new renewal RLI in this
     *    existing renewal Opp.
     *
     * #2. Closed Won renewable RLIs with an Add-On-To PLI that links to an open renewal Opp where the renewal Opp
     *    has an existing open RLI whose Product matches my current won RLI, update the existing renewal RLI.
     *
     * @param array $rliBeans Closed Won renewable RLI Beans to be processed
     * @return array beans that have ben processed
     */
    public function updateRenewalRLIs(array $rliBeans)
    {
        $rlisUpdated = [];
        $newRenewalRLIProperties = [];

        // Loop through all RLIs from closed won opportunity
        foreach ($rliBeans as $rliBean) {
            if (empty($rliBean->add_on_to_id) && !empty($rliBean->renewal_rli_id)) {
                // Initialize the array in case it hasn't been initialized yet
                if (empty($newRenewalRLIProperties[$rliBean->renewal_rli_id])) {
                    $newRenewalRLIProperties[$rliBean->renewal_rli_id] = [
                        'quantity' => 0,
                        'likely_case' => 0,
                    ];
                }

                // Save non add-on RLI properties for renewal RLI (RLI from original closed-won opp)
                $newRenewalRLIProperties[$rliBean->renewal_rli_id]['quantity'] += $rliBean->quantity;
                $newRenewalRLIProperties[$rliBean->renewal_rli_id]['likely_case'] += $rliBean->likely_case;
            } else {
                // Fetch PLI if RLI is addon and get renewal opp
                $pli = BeanFactory::retrieveBean('PurchasedLineItems', $rliBean->add_on_to_id);

                if ($pli && !empty($pli->renewal_opp_id)) {
                    $renewalOpp = BeanFactory::retrieveBean('Opportunities', $pli->renewal_opp_id);

                    // checks if this is a renewal open Opportunity and gets its related renewal RLIs
                    if ($renewalOpp->isOpenRenewalOpportunity() &&
                        $renewalOpp->load_relationship('revenuelineitems')) {
                        $renewalRLIs = $renewalOpp->revenuelineitems->getBeans();

                        // Match current bean to renewal RLI
                        $filteredRLIs = array_filter($renewalRLIs, function ($renewalRLI) use ($rliBean) {
                            if ($renewalRLI->isOpenRenewalRLI() &&
                                !empty($rliBean->product_template_id) &&
                                !empty($renewalRLI->product_template_id) &&
                                $rliBean->product_template_id === $renewalRLI->product_template_id) {
                                // Also set the renewal rli
                                $rliBean->renewal_rli_id = $renewalRLI->id;
                                $rliBean->save();
                                return true;
                            }
                            return false;
                        });
                        $matchingRenewalRLI = current($filteredRLIs);

                        // Case 2: store updates for an existing renewal RLI
                        if (!empty($matchingRenewalRLI)) {
                            // Initialize this part of the array so we don't have an errors as far as the compiler is concerned
                            if (empty($newRenewalRLIProperties[$matchingRenewalRLI->id])) {
                                $newRenewalRLIProperties[$rliBean->renewal_rli_id] = [
                                    'quantity' => $matchingRenewalRLI->quantity,
                                    'likely_case' => $matchingRenewalRLI->likely_case,
                                ];
                            }

                            // Update the quantity
                            if (!is_numeric($newRenewalRLIProperties[$matchingRenewalRLI->id]['quantity'])) {
                                LoggerManager::getLogger()->warn(sprintf('RLI quantity is expected to be numeric, "%s" given', gettype($newRenewalRLIProperties[$matchingRenewalRLI->id]['quantity'])) . PHP_EOL . (new Exception())->getTraceAsString());
                                $newRenewalRLIProperties[$matchingRenewalRLI->id]['quantity'] = 0;
                            }
                            if (!is_numeric($rliBean->quantity)) {
                                LoggerManager::getLogger()->warn(sprintf('RLI quantity is expected to be numeric, "%s" given', gettype($rliBean->quantity)) . PHP_EOL . (new Exception())->getTraceAsString());
                                $rliBean->quantity = floatval($rliBean->quantity);
                            }
                            $newRenewalRLIProperties[$matchingRenewalRLI->id]['quantity'] += $rliBean->quantity;

                            // we need to convert the amount to the existing renewal RLI's currency
                            // when $rliBean & $rli have different currency_id
                            if ($rliBean->currency_id !== $matchingRenewalRLI->currency_id) {
                                $newRenewalRLIProperties[$matchingRenewalRLI->id]['likely_case'] += SugarCurrency::convertAmount(
                                    (float)$rliBean->likely_case,
                                    $rliBean->currency_id,
                                    $matchingRenewalRLI->currency_id
                                );
                            } else {
                                $newRenewalRLIProperties[$matchingRenewalRLI->id]['likely_case'] += $rliBean->likely_case;
                            }
                        } else {
                            // Case 1 create a new renewal RLI since one doesn't match our RLI
                            $newRLIBean = $renewalOpp->createNewRenewalRLI($rliBean);

                            // Link the renewal RLI to the RLI it is generating
                            $rliBean->renewal_rli_id = $newRLIBean->id;
                            $rliBean->save();

                            // Add ID to save for updating
                            $newRenewalRLIProperties[$newRLIBean->id] = [
                                'quantity' => $newRLIBean->quantity,
                                'likely_case' => $newRLIBean->likely_case,
                            ];
                        }

                        $rlisUpdated[] = $rliBean->id;
                    }
                }
            }
        }

        // Update renewal RLIs that have values stored for them
        foreach ($newRenewalRLIProperties as $renewalRLIId => $properties) {
            $renewalRLI = $this->retrieveRliBean($renewalRLIId);

            $renewalRLI->quantity = $properties['quantity'];
            $renewalRLI->likely_case = $properties['likely_case'];
            $renewalRLI->save();
        }

        return $rlisUpdated;
    }

    /**
     * Retrieves an RLI bean from the DB
     *
     * @param string $rliId the ID of the RLI bean
     * @return SugarBean|null
     */
    public function retrieveRliBean($rliId)
    {
        return BeanFactory::retrieveBean('RevenueLineItems', $rliId);
    }

    /**
     * Check if it is an open renewal opportunity.
     *
     * @return bool
     */
    public function isOpenRenewalOpportunity()
    {
        return ($this->sales_status !== Opportunity::STATUS_CLOSED_WON &&
            $this->sales_status !== Opportunity::STATUS_CLOSED_LOST &&
            $this->renewal == 1);
    }

    /**
     * Create a new renewal opportuinty.
     *
     * @return Opportunity
     */
    public function createNewRenewalOpportunity(): Opportunity
    {
        $copyOpFields = [
            'name',
            'assigned_user_id',
            'assigned_user_name',
            'team_id',
            'team_set_id',
            'acl_team_set_id',
        ];

        $newBean = BeanFactory::newBean($this->getModuleName());
        $newBean->renewal = 1;
        $newBean->renewal_parent_id = $this->id;

        foreach ($copyOpFields as $field) {
            if (isset($this->$field)) {
                $newBean->$field = $this->$field;
            }
        }

        $duplicates = $newBean->findDuplicates();
        if (!empty($duplicates['records'])) {
            // check if its renewal
            foreach ($duplicates['records'] as $opp) {
                if (!empty($opp->renewal) && $opp->renewal_parent_id === $this->id) {
                    return $opp;
                }
            }
        }

        $newBean->save();

        if ($newBean->load_relationship('accounts')) {
            $newBean->accounts->add([$this->account_id]);
        }

        return $newBean;
    }

    /**
     * Create a new renewal RLI from an existing RLI.
     *
     * @param RevenueLineItem $rli
     * @return RevenueLineItem
     */
    public function createNewRenewalRLI(RevenueLineItem $rli): RevenueLineItem
    {
        $copyRliFields = [
            'name',
            'account_id',
            'product_template_id',
            'category_id',
            'tax_class',
            'likely_case',
            'currency_id',
            'base_rate',
            'quantity',
            'list_price',
            'cost_price',
            'discount_price',
            'renewable',
            'service',
            'service_duration_value',
            'service_duration_unit',
            'catalog_service_duration_value',
            'catalog_service_duration_unit',
            'assigned_user_id',
            'team_id',
            'team_set_id',
            'acl_team_set_id',
        ];

        $newRliBean = BeanFactory::newBean($rli->getModuleName());
        $timeDate = TimeDate::getInstance();
        $newStartDate = $timeDate->fromDbDate($rli->service_end_date)->modify('+1 day')->asDbDate();
        $newRliBean->service_start_date = $newStartDate;
        $newRliBean->date_closed = $newStartDate;
        $newRliBean->product_type = 'Existing Business';
        $newRliBean->opportunity_id = $this->id;
        $newRliBean->renewal = true;

        foreach ($copyRliFields as $field) {
            if (isset($rli->$field)) {
                $newRliBean->$field = $rli->$field;
            }
        }

        $newRliBean->save();

        if ($this->load_relationship('revenuelineitems')) {
            $this->revenuelineitems->add($newRliBean);
        }

        return $newRliBean;
    }

    /**
     * Overrides the parent updateCalculatedFields to also include recalculating
     * non-SugarLogic rollup fields, and prevent recalculating fields if we're in
     * a cascade operation
     *
     * @throws SugarQueryException
     */
    public function updateCalculatedFields()
    {
        if (SugarBean::inOperation(self::OPERATION_CASCADE . $this->id)) {
            return;
        }
        $this->updateRLIRollupFields();
        parent::updateCalculatedFields();
    }

    /**
     * Updates the non-SugarLogic rollup fields on the Opportunity
     *
     * @return $this
     * @throws SugarQueryException
     */
    public function updateRLIRollupFields()
    {
        $settings = Opportunity::getSettings();
        $rliMode = isset($settings['opps_view_by']) && $settings['opps_view_by'] === 'RevenueLineItems';
        if (!empty($this->id) && $rliMode) {
            $rollupFields = [
                'service_start_date' => $this->calculateOpportunityServiceStartDate(),
                'sales_stage' => $this->calculateOpportunitySalesStage(),
                'date_closed' => $this->calculateOpportunityExpectedCloseDate(),
                'service_open_revenue_line_items' => $this->calculateServiceOpenRLI(),
                'service_open_flex_duration_rlis' => sizeof($this->getEditableDurationServiceRLIs()),
            ];
            $rollupFields = array_merge($rollupFields, $this->calculateServiceDuration());
            if (Forecast::isSetup()) {
                $rollupFields['commit_stage'] = $this->calculateOpportunityCommitStage();
            }

            // Update the Opportunity with the calculated rollup values. If any
            // values have changed on the Opportunity, then save it afterward
            foreach ($rollupFields as $field => $calculatedValue) {
                if ($this->$field !== $calculatedValue) {
                    $this->$field = $calculatedValue;
                }
            }
        }

        return $this;
    }

    /**
     * Runs DB queries to calculate the rollup value for the service duration
     * fields from the related RLIs
     *
     * @return array service duration value and unit
     * @throws SugarQueryException
     */
    public function calculateServiceDuration()
    {
        // If there are no service RLIs, set the service duration to blank
        $serviceRLICount = $this->getServiceRLIs(true);
        if ($serviceRLICount === 0) {
            return [
                'service_duration_value' => '',
                'service_duration_unit' => '',
            ];
        }

        $closedLostStages = $this->getRliClosedLostStages();
        $closedWonStages = $this->getRliClosedWonStages();

        $closedServiceRLICount = $this->getServiceRLIs(true, 'in', array_merge($closedLostStages, $closedWonStages));
        if ($serviceRLICount === $closedServiceRLICount) {
            $closedLostServiceRLICount = $this->getServiceRLIs(true, 'in', $closedLostStages);
            if ($closedServiceRLICount === $closedLostServiceRLICount) {
                // If there are service RLIs and they're all closed and lost,
                // use the max duration of the lost service RLIs.
                $rlis = $this->getServiceRLIs(false, 'in', $closedLostStages);
            } else {
                // If there are service RLIs and they're all closed with at least one
                // that is won, use the max duration of the won service RLIs.
                $rlis = $this->getServiceRLIs(false, 'in', $closedWonStages);
            }
        } else {
            $editableDurationServiceRLICount = $this->getEditableDurationServiceRLIs(true);
            if ($editableDurationServiceRLICount > 0) {
                // If there are service RLIs and at least one is open and has an editable
                // duration, use the max duration of the open, service, flex duration RLIs.
                $rlis = $this->getEditableDurationServiceRLIs();
            } else {
                // If there are service RLIs and at least one is open but none have an
                // editable duration, use the max duration of the open and closed-won
                // service RLIs.
                $rlis = $this->getServiceRLIs(false, 'notIn', $closedLostStages);
            }
        }

        $maxDurationRLI = $this->getRLIWithMaxDuration($rlis);
        return [
            'service_duration_value' => $maxDurationRLI['service_duration_value'],
            'service_duration_unit' => $maxDurationRLI['service_duration_unit'],
        ];
    }

    /**
     * Finds the RLI with the maximum duration
     * @param array $rlis
     * @return array
     */
    private function getRLIWithMaxDuration($rlis)
    {
        // Safety check, if we got here with an empty array then return a blank
        // service duration.
        if (empty($rlis)) {
            return [
                'service_duration_value' => '',
                'service_duration_unit' => '',
            ];
        }

        $now = new SugarDateTime('now', new DateTimeZone('UTC'));

        $durations = [];
        foreach ($rlis as $rli) {
            $modify_by = '+' . $rli['service_duration_value'] . ' ' . $rli['service_duration_unit'];
            $ts = (clone $now)->modify($modify_by)->getTimestamp();
            $durations[$ts] = $rli;
        }

        $maxTs = max(array_keys($durations));
        return $durations[$maxTs];
    }

    /**
     * Gets service RLIs related to this opportunity. If $op and $sales_stages
     * are null then gets all service RLIs. Otherwise only gets service RLIs that
     * match given sales stage criteria.
     * @param bool $count_only
     * @param 'in'|'notIn'|null $op
     * @param array|null $sales_stages
     * @return array
     * @throws SugarQueryException
     */
    private function getServiceRLIs($count_only = false, $op = null, $sales_stages = null)
    {
        $q = new SugarQuery();
        $q->from(BeanFactory::newBean('RevenueLineItems'));
        if ($count_only) {
            $q->select()->setCountQuery();
        } else {
            $q->select(['id', 'service_duration_unit', 'service_duration_value']);
        }
        $queryAnd = $q->where()->queryAnd();
        $queryAnd
            ->equals('opportunity_id', $this->id)
            ->equals('service', 1);
        if (!empty($op) && !empty($sales_stages) && in_array($op, ['in', 'notIn'])) {
            $queryAnd->{$op}('sales_stage', $sales_stages);
        }

        if ($count_only) {
            $data = $q->execute();
            return $data[0]['record_count'];
        } else {
            return $q->execute();
        }
    }

    /**
     * Gets service RLIs with editable durations that are related to this
     * opportunity.
     * @return array
     * @throws SugarQueryException
     */
    public function getEditableDurationServiceRLIs($count_only = false)
    {
        $closedStages = $this->getClosedStages();

        // RLIs with no product template
        $q1 = new SugarQuery();
        $q1->from(BeanFactory::newBean('RevenueLineItems'));
        if ($count_only) {
            $q1->select()->setCountQuery();
        } else {
            $q1->select(['id', 'service_duration_unit', 'service_duration_value']);
        }
        $q1->where()->queryAnd()
            ->equals('opportunity_id', $this->id)
            ->equals('service', 1)
            ->notIn('sales_stage', $closedStages)
            ->isEmpty('add_on_to_id')
            ->isEmpty('product_template_id');

        // RLIs with an unlocked duration product template
        $q2 = new SugarQuery();
        $q2->from(BeanFactory::newBean('RevenueLineItems'));
        $q2->joinTable('product_templates', [
            'alias' => 'pt',
        ])->on()
            ->equalsField('pt.id', 'product_template_id')
            ->notEquals('pt.lock_duration', 1);
        if ($count_only) {
            $q2->select()->setCountQuery();
        } else {
            $q2->select(['id', 'service_duration_unit', 'service_duration_value']);
        }
        $q2->where()->queryAnd()
            ->equals('opportunity_id', $this->id)
            ->equals('service', 1)
            ->notIn('sales_stage', $closedStages)
            ->isEmpty('add_on_to_id');

        if ($count_only) {
            $data1 = $q1->execute();
            $data2 = $q2->execute();
            return $data1[0]['record_count'] + $data2[0]['record_count'];
        } else {
            $q = new SugarQuery();
            $q->union($q1);
            $q->union($q2);

            return $q->execute();
        }
    }

    /**
     * Runs DB query to check if there are any open and marked as service RLIs for the opportunity
     *
     * @return int the number of open and service RLIs
     * @throws SugarQueryException
     */
    private function calculateServiceOpenRLI(): int
    {
        $closedStages = $this->getClosedStages();

        // Get the number of open and service marked RLIs
        $q = new SugarQuery();
        $q->from(BeanFactory::newBean('RevenueLineItems'));
        $q->select(['id']);
        $q->where()->queryAnd()
            ->equals('opportunity_id', $this->id)
            ->equals('service', 1)
            ->notIn('sales_stage', $closedStages);

        return sizeof($q->execute());
    }

    /**
     * Using the Opp's RLIs, calculates the new commit stage for the Opp
     *
     * @return string the calculated commit stage for the Opportunity
     * @throws SugarQueryException
     */
    public function calculateOpportunityCommitStage()
    {
        $ranges = $this->getSortedForecastRangeKeys();

        // If we can't get the configured forecast ranges, or if something else goes wrong,
        // leave the commit_stage as-is
        if (empty($ranges)) {
            return $this->commit_stage;
        }

        $q = new SugarQuery();
        $q->from(BeanFactory::newBean('RevenueLineItems'));
        $select = $q->select();
        foreach ($ranges as $range) {
            $select->fieldRaw(
                'SUM(CASE WHEN commit_stage = ' . $this->db->quoted($range) . ' THEN 1 ELSE 0 END)',
                $range,
            );
        }
        $q->where()->equals('opportunity_id', $this->id);

        $result = $q->execute();
        if (!empty($result) && !empty($result[0])) {
            $result = $result[0];
        } else {
            return $this->commit_stage;
        }

        // Look through the ranges and find the highest probability one in use
        foreach ($ranges as $range) {
            if (!empty($result[$range])) {
                return $range;
            }
        }

        return $this->commit_stage;
    }

    /**
     * Gets the forecast range keys, in descending order
     * @return array|null
     */
    public function getSortedForecastRangeKeys()
    {
        if (!Forecast::isSetup()) {
            return null;
        }

        $forecastSettings = Forecast::getSettings();
        $forecastRangeSetting = $forecastSettings['forecast_ranges'];
        if (empty($forecastRangeSetting)) {
            return null;
        }
        $ranges = $forecastSettings[$forecastRangeSetting . '_ranges'];
        if (empty($ranges)) {
            return null;
        }

        // First separate the probability and non-probability ranges
        $probabilityRanges = array_filter($ranges, function ($range) {
            return $range['max'] !== 0;
        });

        // Next sort the probability ranges in descending order
        $sortKey = array_column($probabilityRanges, 'max');
        array_multisort($probabilityRanges, SORT_DESC, $sortKey);

        // Finally take all the keys and merge them back together
        $probabilityRangeKeys = array_keys($probabilityRanges);
        $nonProbabilityRangeKeys = array_diff(array_keys($ranges), $probabilityRangeKeys);

        return array_merge($probabilityRangeKeys, $nonProbabilityRangeKeys);
    }

    /**
     * Runs a DB query to calculate the rollup value for the Service Start Date
     * field from the related RLIs
     *
     * @return string containing the calculated Service Start Date
     * @throws SugarQueryException
     */
    private function calculateOpportunityServiceStartDate(): string
    {
        $closedWonStages = $this->getRliClosedWonStages();
        $closedLostStages = $this->getRliClosedLostStages();

        // Build the case statement for the query. This will be used to order the
        // query results so that open RLIs come before closed-won
        $quotedWonStages = implode(',', $this->getQuotedStringArray($closedWonStages));
        $caseStatement = 'CASE WHEN sales_stage IN (' . $quotedWonStages . ') THEN 1 ELSE 0 END';

        // Get the earliest Service Start Date of a non-closed-lost service RLI
        // related to the Opportunity. If any of the related service RLIs are
        // open, their value takes precedence over closed-won service RLIs.
        $q = new SugarQuery();
        $q->from(BeanFactory::newBean('RevenueLineItems'));
        $q->select(['service_start_date'])
            ->fieldRaw($caseStatement, 'is_closed');
        $q->where()->queryAnd()
            ->equals('opportunity_id', $this->id)
            ->equals('service', 1)
            ->notIn('sales_stage', $closedLostStages);
        $q->orderByRaw('is_closed', 'ASC');
        $q->orderBy('service_start_date', 'ASC');
        $result = $q->getDBManager()->fromConvert($q->getOne(), 'date');

        return !empty($result) ? $result : '';
    }

    /**
     * Runs a DB query to calculate the rollup value for the Sales Stage field
     * from the related RLIs
     *
     * @return string containing the calculated Sales Stage
     * @throws SugarQueryException
     */
    private function calculateOpportunitySalesStage(): string
    {
        // Get the lists of sales stages needed for the query
        $listStrings = return_app_list_strings_language('en_us');
        $quotedAllStages = $this->getQuotedStringArray($listStrings['sales_stage_dom']);
        $quotedClosedWonStages = $this->getQuotedStringArray($this->getRliClosedWonStages());
        $quotedClosedLostStages = $this->getQuotedStringArray($this->getRliClosedLostStages());

        // Build the case statements. These will be used to order query results
        // so that the first result will be the correct sales stage
        $wonStages = implode(',', $quotedClosedWonStages);
        $lostStages = implode(',', $quotedClosedLostStages);
        $closedOrderCases = 'CASE WHEN sales_stage IN (' . $wonStages . ') THEN 1 ' .
            'WHEN sales_stage IN (' . $lostStages . ') THEN 2 ' .
            'ELSE 0 END';
        $stageOrderCases = 'CASE ';
        foreach ($quotedAllStages as $index => $stage) {
            $stageOrderCases .= 'WHEN sales_stage = ' . $stage . ' THEN ' . $index . ' ';
        }
        $stageOrderCases .= 'ELSE ' . safeCount($quotedAllStages) . ' END';

        // Execute the query. If any RLIs are open, we get the latest sales_stage
        // of the open RLIs. Otherwise, if any are closed-won, the sales_stage is
        // closed-won. Otherwise, it is closed-lost.
        $q = new SugarQuery();
        $q->from(BeanFactory::newBean('RevenueLineItems'));
        $q->select(['sales_stage'])
            ->fieldRaw($closedOrderCases, 'closed_order')
            ->fieldRaw($stageOrderCases, 'stage_order');
        $q->where()->equals('opportunity_id', $this->id);
        $q->orderByRaw('closed_order', 'ASC');
        $q->orderByRaw('stage_order', 'DESC');
        $result = $q->getOne();

        return !empty($result) ? $result : '';
    }

    /**
     * Runs a DB query to calculate the rollup value for the Expected Close Date
     * field from the related RLIs
     *
     * @return string containing the calculated Expected Close Date
     * @throws SugarQueryException
     */
    private function calculateOpportunityExpectedCloseDate(): string
    {
        // Get the lists of sales stages needed for the query
        $quotedClosedWonStages = $this->getQuotedStringArray($this->getRliClosedWonStages());
        $quotedClosedLostStages = $this->getQuotedStringArray($this->getRliClosedLostStages());

        // Build the case statement. This will be used to order query results so
        // that open RLIs take precedence over closed RLIs, and closed-won RLIs
        // take precedence over closed-lost RLIs
        $wonStages = implode(',', $quotedClosedWonStages);
        $lostStages = implode(',', $quotedClosedLostStages);
        $closedOrderCases = 'CASE WHEN sales_stage IN (' . $wonStages . ') THEN 1 ' .
            'WHEN sales_stage IN (' . $lostStages . ') THEN 2 ' .
            'ELSE 0 END';

        // Execute the query. If any RLIs are open, we get the latest expected
        // close date of the open RLIs. Otherwise, if any are closed-won, we get
        // the latest expected close date of the closed-won. Otherwise, we get
        // the latest expected close date of the closed-lost RLIs
        $q = new SugarQuery();
        $q->from(BeanFactory::newBean('RevenueLineItems'));
        $q->select(['date_closed'])
            ->fieldRaw($closedOrderCases, 'closed_order');
        $q->where()->equals('opportunity_id', $this->id);
        $q->orderByRaw('closed_order', 'ASC');
        $q->orderByRaw('date_closed', 'DESC');
        $result = $q->getDBManager()->fromConvert($q->getOne(), 'date');

        return !empty($result) ? $result : '';
    }

    /**
     * Adds proper DB quotation to an array of strings for use in SQL queries
     * @param array $array the array of strings to quote
     * @return array an array of the passed-in strings quoted correctly for the DB
     */
    private function getQuotedStringArray(array $array): array
    {
        $db = DBManagerFactory::getInstance();
        $quotedArray = [];
        foreach ($array as $key => $value) {
            $quotedArray[] = $db->quoted($value);
        }
        return $quotedArray;
    }

    /**
     * Cascades certain Opportunity fields down to related RLIs upon save depending on
     * the {field}=>{condition} mappings defined in CASCADE_FIELD_CONDITIONS. This is
     * only called if at least one of our '_cascade' fields has a value.
     */
    private function cascade()
    {
        // If entering a cascade operation fails (because we're already in one) return early
        if (!SugarBean::enterOperation(self::OPERATION_CASCADE . $this->id)) {
            return;
        }

        // entering/leaving operations is entirely static, and incurs really low overhead
        // Checking settings and local variables is a little more expensive, so we check these conditions
        // after our static operations to avoid work we don't need to do.
        $settings = Opportunity::getSettings();
        if ($settings['opps_view_by'] !== 'RevenueLineItems') {
            SugarBean::leaveOperation(self::OPERATION_CASCADE . $this->id);
            return;
        }

        if ($this->load_relationship('revenuelineitems')) {
            $rlis = $this->revenuelineitems->getBeans();
            if (!is_array($rlis) || empty($rlis)) {
                return;
            }
            $updated_rlis = [];

            foreach (self::CASCADE_FIELD_CONDITIONS as $field => $callback) {
                $cascadeField = $field . '_cascade';
                if (!empty($this->{$cascadeField})) {
                    foreach ($rlis as $rli) {
                        if ($rli->{$field} !== $this->{$cascadeField} &&
                            call_user_func([$this, $callback], $rli)) {
                            $rli->{$field} = $this->{$cascadeField};
                            $updated_rlis[] = $rli;
                        }
                    }
                    $this->{$cascadeField} = null;
                }
            }
            foreach ($updated_rlis as $rli) {
                $rli->save();
            }
        }
        parent::leaveOperation(self::OPERATION_CASCADE . $this->id);
        // After updating all associated RLIs, update the calculated fields on this Opportunity
        // to set the original fields correctly.
        $this->updateCalculatedFields();
    }

    /**
     * Returns true when RLI is open.
     *
     * @param RevenueLineItem rli
     * @return bool True if RLI is open
     */
    public function cascadeWhenOpen($rli)
    {
        return !in_array($rli->sales_stage, $this->getClosedStages());
    }

    /**
     * Returns true when RLI is open and marked as a service.
     *
     * @param RevenueLineItem $rli
     * @return bool True if RLI is marked 'service' and is open
     */
    public function cascadeWhenServiceOpen($rli)
    {
        return (isTruthy($rli->service) && $this->cascadeWhenOpen($rli));
    }

    /**
     * Returns true when RLI is open, marked as a service, and has an editable
     * duration.
     *
     * @param RevenueLineItem $rli
     * @return bool
     */
    public function cascadeWhenDurationEditableServiceOpen($rli)
    {
        if (!$this->cascadeWhenServiceOpen($rli) || !empty($rli->add_on_to_id)) {
            return false;
        }
        $productTemplate = BeanFactory::retrieveBean('ProductTemplates', $rli->product_template_id);
        if ($productTemplate->id) {
            return isFalsy($productTemplate->lock_duration);
        }
        return true;
    }

    /**
     * If at least one '_cascade' field has a value set, we should cascade. Because they're
     * non-db fields, on non-cascading saves they will be the empty string or null.
     * @return bool
     */
    private function shouldCascade()
    {
        foreach (array_keys(self::CASCADE_FIELD_CONDITIONS) as $field) {
            if (!empty($this->{$field . '_cascade'})) {
                return true;
            }
        }
        return false;
    }

    /**
     * Util function to see if we are in Opps + RLIs mode
     * @return bool true if in RLIs mode, false if in Opps Only mode
     */
    public static function usingRevenueLineItems(): bool
    {
        $settings = Opportunity::getSettings();
        return isset($settings['opps_view_by']) && $settings['opps_view_by'] === 'RevenueLineItems';
    }
}
