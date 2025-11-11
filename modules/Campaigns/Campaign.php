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
 * Description:
 ********************************************************************************/
class Campaign extends SugarBean
{
    // Stored fields
    public $id;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $assigned_user_id;
    public $created_by;
    public $created_by_name;
    public $currency_id;
    public $base_rate;
    public $modified_by_name;
    public $team_id;
    public $team_name;
    public $name;
    public $start_date;
    public $end_date;
    public $status;
    public $expected_cost;
    public $budget;
    public $actual_cost;
    public $expected_revenue;
    public $campaign_type;
    public $objective;
    public $content;
    public $tracker_key;
    public $tracker_text;
    public $tracker_count;
    public $impressions;

    // These are related
    public $assigned_user_name;

    // module name definitions and table relations
    public $table_name = 'campaigns';
    public $rel_prospect_list_table = 'prospect_list_campaigns';
    public $object_name = 'Campaign';
    public $module_dir = 'Campaigns';
    public $importable = true;

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = [
        'assigned_user_name', 'assigned_user_id',
    ];

    public $relationship_fields = ['prospect_list_id' => 'prospect_lists'];

    public $new_schema = true;


    public function __construct()
    {
        parent::__construct();
    }


    public function get_summary_text()
    {
        return $this->name;
    }

    public function clear_campaign_prospect_list_relationship($campaign_id, $prospect_list_id = '')
    {
        if (!empty($prospect_list_id)) {
            $prospect_clause = ' and prospect_list_id = ' . $this->db->quoted($prospect_list_id);
        } else {
            $prospect_clause = '';
        }

        $query = "DELETE FROM $this->rel_prospect_list_table WHERE campaign_id = " . $this->db->quoted($campaign_id) .
            ' AND deleted = 0 ' . $prospect_clause;
        $this->db->query($query, true, 'Error clearing campaign to prospect_list relationship: ');
    }


    public function mark_relationships_deleted($id)
    {
        $this->clear_campaign_prospect_list_relationship($id);
        parent::mark_relationships_deleted($id);
    }

    public function update_currency_id($fromid, $toid)
    {
    }


    public function get_list_view_data($filter_fields = [])
    {

        $temp_array = $this->get_list_view_array();
        if ($this->campaign_type != 'Email') {
            $temp_array['OPTIONAL_LINK'] = 'display:none';
        }
        $temp_array['TRACK_CAMPAIGN_TITLE'] = translate('LBL_TRACK_BUTTON_TITLE', 'Campaigns');
        $temp_array['TRACK_CAMPAIGN_IMAGE'] = SugarThemeRegistry::current()->getImageURL('view_status.gif');
        $temp_array['TRACK_VIEW_ALT_TEXT'] = translate('LBL_TRACK_BUTTON_TITLE', 'Campaigns');

        $wizardTitle = translate('LBL_TO_WIZARD_TITLE', 'Campaigns');
        $wizardImg = SugarThemeRegistry::current()->getImageURL('edit_wizard.gif');
        $wizardAltText = translate('LBL_TO_WIZARD_TITLE', 'Campaigns');

        $wizardButton = ' <a title="' . $wizardTitle . '" href="index.php?action=WizardHome&module=Campaigns&record=';
        $wizardButton .= $this->id . '"><img border="0" src="' . $wizardImg . '" alt="' . $wizardAltText . '"></a>  ';

        $temp_array['WIZARD_BUTTON'] = $this->ACLAccess('edit') ? $wizardButton : '';

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
        array_push($where_clauses, "campaigns.name like '$the_query_string%'");

        $the_where = '';
        foreach ($where_clauses as $clause) {
            if ($the_where != '') {
                $the_where .= ' or ';
            }
            $the_where .= $clause;
        }


        return $the_where;
    }

    public function save($check_notify = false)
    {

        $this->unformat_all_fields();

        // Bug53301
        if ($this->campaign_type != 'NewsLetter') {
            $this->frequency = '';
        }

        return parent::save($check_notify);
    }

    public function set_notification_body($xtpl, $camp)
    {
        $xtpl->assign('CAMPAIGN_NAME', $camp->name);
        $xtpl->assign('CAMPAIGN_AMOUNT', $camp->budget);
        $xtpl->assign('CAMPAIGN_CLOSEDATE', $camp->end_date);
        $xtpl->assign('CAMPAIGN_STATUS', $camp->status);
        $xtpl->assign('CAMPAIGN_DESCRIPTION', $camp->content);

        return $xtpl;
    }

    public function track_log_leads()
    {
        $this->load_relationship('log_entries');
        $query_array = $this->log_entries->getQuery(true);

        $query_array['select'] = 'SELECT campaign_log.* ';
        $query_array['where'] = $query_array['where'] . " AND activity_type = 'lead' AND archived = 0 AND target_id IS NOT NULL";

        return implode(' ', $query_array);
    }

    public function track_log_entries($type = [])
    {
        //get arguments being passed in
        $args = func_get_args();
        $mkt_id = '';

        $this->load_relationship('log_entries');
        $query_array = $this->log_entries->getQuery(true);

        //if one of the arguments is marketing ID, then we need to filter by it
        foreach ($args as $arg) {
            if (isset($arg['EMAIL_MARKETING_ID_VALUE'])) {
                $mkt_id = $arg['EMAIL_MARKETING_ID_VALUE'];
            }

            if (isset($arg['group_by'])) {
                $query_array['group_by'] = $arg['group_by'];
            }
        }


        if (empty($type)) {
            $type[0] = 'targeted';
        }

        $query_array['select'] = 'SELECT campaign_log.* ';
        $query_array['where'] = $query_array['where'] . ' AND activity_type=' . $this->db->quoted($type[0]) . ' AND archived=0 AND target_id IS NOT NULL';
        //add filtering by marketing id, if it exists
        if (!empty($mkt_id)) {
            $query_array['where'] = $query_array['where'] . ' AND marketing_id = ' . $this->db->quoted($mkt_id);
        }

        //B.F. #37943
        if (isset($query_array['group_by'])) {
            //perform the inner join with the group by if a marketing id is defined, which means we need to filter out duplicates.
            //if no marketing id is specified then we are displaying results from multiple marketing emails and it is understood there might be duplicate target entries
            if (!empty($mkt_id)) {
                $group_by = str_replace('campaign_log', 'cl', $query_array['group_by']);
                $join_where = str_replace('campaign_log', 'cl', $query_array['where']);
                $query_array['from'] .= " INNER JOIN (select min(id) as id from campaign_log cl $join_where GROUP BY $group_by  ) secondary
					on campaign_log.id = secondary.id	";
            }
            unset($query_array['group_by']);
        } elseif (isset($query_array['group_by'])) {
            $query_array['where'] = $query_array['where'] . ' GROUP BY ' . $query_array['group_by'];
            unset($query_array['group_by']);
        }

        $query = (implode(' ', $query_array));
        return $query;
    }


    public function get_queue_items()
    {
        //get arguments being passed in
        $args = func_get_args();
        $mkt_id = '';

        $this->load_relationship('queueitems');
        $query_array = $this->queueitems->getQuery(true);

        //if one of the arguments is marketing ID, then we need to filter by it
        foreach ($args as $arg) {
            if (isset($arg['EMAIL_MARKETING_ID_VALUE'])) {
                $mkt_id = $arg['EMAIL_MARKETING_ID_VALUE'];
            }

            if (isset($arg['group_by'])) {
                $query_array['group_by'] = $arg['group_by'];
            }
        }

        //add filtering by marketing id, if it exists, and if where key is not empty
        if (!empty($mkt_id) && !empty($query_array['where'])) {
            $query_array['where'] = $query_array['where'] . ' AND marketing_id = ' . $this->db->quoted($mkt_id);
        }

        //get select query from email man
        $man = BeanFactory::newBean('EmailMan');
        $listquery = $man->create_queue_items_query('', str_replace(['WHERE', 'where'], '', $query_array['where']), null, $query_array);
        return $listquery;
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
     * create_list_count_query
     * Overrode this method from SugarBean to handle the distinct parameter used to filter out
     * duplicate entries for some of the subpanel listivews.  Without the distinct filter, the
     * list count would be inaccurate because one-to-many email_marketing entries may be associated
     * with a campaign.
     *
     * @param string $query Select query string
     * @param array $param array of arguments
     * @return string count query
     *
     */
    public function create_list_count_query($query, $params = [])
    {
        //include the distinct filter if a marketing id is defined, which means we need to filter out duplicates by the passed in group by.
        //if no marketing id is specified, it is understood there might be duplicate target entries so no need to filter out
        if ((strpos($query, 'marketing_id') !== false) && isset($params['distinct'])) {
            $pattern = '/SELECT(.*?)(\s){1}FROM(\s){1}/is';  // ignores the case
            $replacement = 'SELECT COUNT(DISTINCT ' . $params['distinct'] . ') c FROM ';
            $query = preg_replace($pattern, $replacement, $query, 1);
            return $query;
        }

        //If distinct parameter not found, default to SugarBean's function
        return parent::create_list_count_query($query);
    }

    /**
     * Returns count of deleted leads,
     * which were created through generated lead form
     *
     * @return integer
     */
    public function getDeletedCampaignLogLeadsCount()
    {
        $query = <<<'SQL'
SELECT COUNT(*) AS count 
FROM campaign_log 
WHERE campaign_id = ? AND target_id IS NULL AND activity_type = 'lead'
SQL;
        $stmt = $this->db->getConnection()->executeQuery(
            $query,
            [$this->getFieldValue('id')]
        );
        return (int)$stmt->fetchOne();
    }
}
