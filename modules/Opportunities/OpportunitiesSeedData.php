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

/**
 * OpportunitiesSeedData.php
 *
 * This is a class used for creating OpportunitiesSeedData.  We moved this code out from install/populateSeedData.php so
 * that we may better control and test creating default Opportunities.
 *
 */
class OpportunitiesSeedData
{
    // @codingStandardsIgnoreStart PSR2.Classes.PropertyDeclaration.Underscore
    private static $_ranges;

    public static $pt_ids = [];

    public static $pc_ids = [];
    // @codingStandardsIgnoreEnd PSR2.Classes.PropertyDeclaration.Underscore

    public static $serviceOppIds;

    public static $oppForRenewal;

    /**
     * @var $db DBManager
     */
    protected static $db;

    protected static $products = [];

    protected static function hasProductLicense(string $product): bool
    {
        if (!empty(static::$products)) {
            return array_key_exists($product, static::$products);
        }

        if (empty(self::$db)) {
            self::$db = DBManagerFactory::getInstance();
        }

        $sql = "SELECT value FROM config WHERE category = 'license' AND name = 'subscription'";
        $data = self::$db->getOne($sql);
        $data = json_decode($data);

        if ($data && isset($data->subscription) && isset($data->subscription->addons)) {
            $addons = $data->subscription->addons;

            $check = [
                'SELL' => 'sell',
                'SERVE' => 'serve',
                'ENT' => 'enterprise',
                //'HINT' => 'hint',
            ];

            foreach ($addons as $addon) {
                if (!empty($addon->product_code_c)) {
                    if (isset($check[$addon->product_code_c])) {
                        static::$products[$check[$addon->product_code_c]] = true;
                    }
                }
            }

            return array_key_exists($product, static::$products);
        }

        return false;
    }

    /**
     * populateSeedData
     *
     * This is a static function to create Opportunities.
     *
     * @static
     * @param $records Integer value indicating the number of Opportunities to create
     * @param $app_list_strings Array of application language strings
     * @param $accounts Array of Account instances to randomly build data against
     * @param $timeperiods Array of Timeperiods to create timeperiod seed data off of
     * @param $users Array of User instances to randomly build data against
     * @return array Array of Opportunities created
     */
    public static function populateSeedData($records, $app_list_strings, $accounts, $users)
    {
        if (empty($accounts) || empty($app_list_strings) || (!is_int($records) || $records < 1) || empty($users)) {
            return [];
        }

        $opp_config = Opportunity::getSettings(true);
        $usingRLIs = ($opp_config['opps_view_by'] === 'RevenueLineItems');

        self::$db = DBManagerFactory::getInstance();

        $opp_ids = [];
        $timedate = TimeDate::getInstance();

        // get the additional currencies from the table
        /* @var $currency Currency */
        $currency = SugarCurrency::getCurrencyByISO('EUR');

        $now = $GLOBALS['timedate']->nowDb();
        /* @var $opp Opportunity */
        $opp = BeanFactory::newBean('Opportunities');
        $oppFieldDefs = $opp->getFieldDefinitions();
        $oppIndices = $opp->getIndices();
        $oppDbData = self::toDatabaseArray($opp);
        $oppSql = 'INSERT INTO ' . $opp->table_name . ' (' . join(',', array_keys($oppDbData)) . ') VALUES';
        $oppRows = [];
        $oppAccRows = [];
        $oppAccRow = [
            'id' => '',
            'opportunity_id' => '',
            'account_id' => '',
            'date_modified' => self::$db->quoted($now),
            'deleted' => 0,
        ];
        $oppAccSql = 'INSERT INTO accounts_opportunities (' . join(',', array_keys($oppAccRow)) . ') VALUES';


        if ($usingRLIs) {
            // load up the product template_ids
            $sql = 'SELECT id, list_price, cost_price, discount_price, category_id, mft_part_num, list_usdollar,
                        cost_usdollar, discount_usdollar, tax_class, weight, service, renewable, service_duration_value,
                        service_duration_unit
                  FROM product_templates WHERE deleted = 0';

            $results = self::$db->query($sql);
            while ($row = self::$db->fetchByAssoc($results)) {
                static::$pt_ids[$row['id']] = $row;
            }
            $sql = 'SELECT id FROM product_categories WHERE deleted = 0';
            $results = self::$db->query($sql);
            while ($row = self::$db->fetchByAssoc($results)) {
                static::$pc_ids[] = $row['id'];
            }
        }

        /* @var $fw ForecastWorksheet */
        $fw = BeanFactory::newBean('ForecastWorksheets');
        $fw->parent_type = 'Opportunities';
        $fw->draft = 1;
        $fw->date_entered = $now;
        $fw->date_modified = $now;

        $fwFieldDefs = $fw->getFieldDefinitions();
        $fwIndices = $fw->getIndices();
        $fwRows = [];
        $fwDbData = self::toDatabaseArray($fw);
        $fwSql = 'INSERT INTO ' . $fw->table_name . '(' . join(',', array_keys($fwDbData)) . ') VALUES';

        while ($records-- > 0) {
            $key = array_rand($accounts);
            $account = $accounts[$key];

            //Create new opportunities
            $opp->team_id = $account->team_id;
            $opp->team_set_id = $account->team_set_id;

            $opp->assigned_user_id = $account->assigned_user_id;
            $opp->assigned_user_name = $account->assigned_user_name;

            $opp->created_by = $account->assigned_user_id;
            $opp->modified_user_id = $account->assigned_user_id;

            // figure out which one to use
            $base_rate = '1.0';
            $currency_id = '-99';

            if (!$usingRLIs) {
                $seed = random_int(1, 15);
                if ($seed % 2 == 0) {
                    $currency_id = $currency->id;
                    $base_rate = $currency->conversion_rate;
                }
            }

            $opp->base_rate = $base_rate;
            $opp->currency_id = $currency_id;

            $opp->name = $account->name;
            $opp->lead_source = array_rand($app_list_strings['lead_source_dom']);
            $opp->sales_stage = array_rand($app_list_strings['sales_stage_dom']);
            $opp->sales_status = 'New';

            if (!$usingRLIs) {
                // If the deal is already done, make the date closed occur in the past.
                $opp->date_closed = ($opp->sales_stage == Opportunity::STAGE_CLOSED_WON || $opp->sales_stage == Opportunity::STAGE_CLOSED_LOST)
                    ? self::createPastDate()
                    : self::createDate();
                $opp->date_closed_timestamp = $timedate->fromDbDate($opp->date_closed)->getTimestamp();
            }
            $opp->opportunity_type = array_rand($app_list_strings['opportunity_type_dom']);
            $amount = random_int(1000, 7500);
            $opp->amount = $amount;
            $opp->amount_usdollar = SugarMath::init($amount)->div($base_rate)->result();
            $opp->probability = $app_list_strings['sales_probability_dom'][$opp->sales_stage];

            //Setup forecast seed data
            $opp->best_case = $opp->amount;
            $opp->worst_case = $opp->amount;
            $opp->commit_stage = $opp->probability >= 70 ? 'include' : 'exclude';

            $opp->id = create_guid();
            $opp->new_with_id = true;

            $opp->date_entered = $now;
            $opp->date_modified = $now;

            // set the account on the opps, just for saving to the worksheet table
            $opp->account_id = $account->id;
            $opp->account_name = $account->name;

            $oppAccRows[] = '(' . join(',', array_merge($oppAccRow, [
                    'id' => self::$db->quoted(create_guid()),
                    'account_id' => self::$db->quoted($account->id),
                    'opportunity_id' => self::$db->quoted($opp->id),
                ])) . ')';

            $return = [];
            if ($usingRLIs) {
                $return = static::createRevenueLineItems($opp, random_int(3, 5), $app_list_strings);
            }
            $values = array_merge(self::toDatabaseArray($opp), $return);

            $sqlValues = [];
            foreach ($values as $key => $value) {
                $value = self::evaluateIndexedValue($key, $value, $oppIndices);
                $sqlValues[] = self::$db->massageValue($value, $oppFieldDefs[$key]);
            }
            $oppRows[] = '(' . join(',', $sqlValues) . ')';

            if (!$usingRLIs) {
                $fw->modified_user_id = $account->assigned_user_id;
                $fw->created_by = $account->assigned_user_id;
                $fw->copyValues($fw->opportunityFieldMap, $opp);

                $fw->id = create_guid();
                $fw->parent_id = $opp->id;
                $fwValues = self::toDatabaseArray($fw);

                $sqlValues = [];
                foreach ($fwValues as $key => $value) {
                    $value = self::evaluateIndexedValue($key, $value, $fwIndices);
                    $sqlValues[$key] = self::$db->massageValue($value, $fwFieldDefs[$key]);
                }
                $fwRows[] = '(' . join(',', $sqlValues) . ')';
            }

            $opp_ids[] = $opp->id;
        }

        self::insertAndCommit($oppSql, $oppRows);
        self::insertAndCommit($oppAccSql, $oppAccRows);
        if (!$usingRLIs) {
            self::insertAndCommit($fwSql, $fwRows);
        }

        return $opp_ids;
    }


    /**
     * populateServiceData
     *
     * This is a static function to create Renewal Opportunities.
     *
     * @static
     * @param $app_list_strings Array of application language strings
     * @param $accounts Array of Account instances to randomly build data against
     */
    public static function populateServiceData($app_list_strings, $accounts)
    {
        // get the additional currencies from the table
        /* @var $currency Currency */
        $currency = SugarCurrency::getCurrencyByISO('EUR');
        $baseRate = '1.0';
        $currencyId = '-99';

        $oppBestCase = 0;
        $oppWorstCase = 0;
        $oppAmount = 0;
        $oppUnits = 0;
        $oppDateClosed = '';
        $oppDateClosedTimestamp = 0;

        if (empty($accounts) || empty($app_list_strings)) {
            return [];
        }

        $oppConfig = Opportunity::getSettings(true);
        $usingRLIs = ($oppConfig['opps_view_by'] === 'RevenueLineItems');

        self::$db = DBManagerFactory::getInstance();

        $oppIds = [];
        $timedate = TimeDate::getInstance();

        // get the additional currencies from the table
        /* @var $currency Currency */
        $currency = SugarCurrency::getCurrencyByISO('EUR');

        $now = $GLOBALS['timedate']->nowDb();

        if ($usingRLIs) {
            // get all the service RLIs from the RLI table
            $sql = 'SELECT * FROM revenue_line_items WHERE deleted = 0 AND service = 1';

            $results = self::$db->query($sql);
            while ($row = self::$db->fetchByAssoc($results)) {
                static::$serviceOppIds[$row['opportunity_id']] = $row;
            }
        }

        foreach (static::$serviceOppIds as $keys => $serviceOpp) {
            /* @var $opp Opportunity */
            $opp = BeanFactory::newBean('Opportunities');
            $oppFieldDefs = $opp->getFieldDefinitions();
            $oppIndices = $opp->getIndices();
            $oppSalesStageFieldDef = $oppFieldDefs['sales_stage'];
            $opp->id = create_guid();

            $opp->base_rate = $baseRate;
            $opp->currency_id = $currencyId;

            /* @var $fw ForecastWorksheet */
            $fw = BeanFactory::newBean('ForecastWorksheets');
            $fw->parent_type = 'Opportunities';
            $fw->draft = 1;
            $fw->date_entered = $now;
            $fw->date_modified = $now;

            $fwFieldDefs = $fw->getFieldDefinitions();
            $fwIndices = $fw->getIndices();
            $fwRows = [];
            $fwDbData = self::toDatabaseArray($fw);
            $fwSql = 'INSERT INTO ' . $fw->table_name . '(' . join(',', array_keys($fwDbData)) . ') VALUES';

            /* @var $rli RevenueLineItem */
            $rli = BeanFactory::newBean('RevenueLineItems');
            $rliFieldDefs = $rli->getFieldDefinitions();
            $rliIndices = $rli->getIndices();
            $rliSql = [];
            $rliDbData = self::toDatabaseArray($rli);
            $sqlRli = 'INSERT INTO ' . $rli->table_name . '(' . join(',', array_keys($rliDbData)) . ') VALUES';

            foreach (array_keys($serviceOpp) as $arrKey) {
                $rli->$arrKey = $serviceOpp[$arrKey];
            }

            // For sell based purchase generation
            $rli->generate_purchase = static::hasProductLicense('sell') ? 'Yes' : '';

            // Updating RLI for renewal opp
            $rli->service_start_date = $timedate->fromString($rli->service_end_date)->modify('+1 day')->asDbDate();
            //service_end_date should be set to null in order to calculate a new one for the Renewal Opp related RLI
            $rli->service_end_date = null;
            $rli->service_end_date = self::setServiceEndDate($rli);
            $rli->opportunity_id = $opp->id;
            $rli->sales_stage = $app_list_strings['sales_stage_dom']['Prospecting'];
            $rli->date_closed = $rli->service_start_date;
            $rli->date_closed_timestamp = $timedate->fromDbDate($rli->date_closed)->getTimestamp();
            $rli->id = create_guid();

            $rli->name = $serviceOpp['name'] . ' New Service';

            $opp->name = $rli->name;

            $values = self::toDatabaseArray($rli);

            $sqlValues = [];
            foreach ($values as $key => $value) {
                $value = self::evaluateIndexedValue($key, $value, $rliIndices);
                $sqlValues[] = self::$db->massageValue($value, $rliFieldDefs[$key]);
            }

            $rliSql[] = '(' . join(',', $sqlValues) . ')';

            $fw->copyValues($fw->productFieldMap, $rli);

            $fw->id = create_guid();
            $fw->parent_id = $rli->id;
            $fwValues = self::toDatabaseArray($fw);
            $sqlValues = [];
            foreach ($fwValues as $key => $value) {
                $value = self::evaluateIndexedValue($key, $value, $fwIndices);
                $sqlValues[$key] = self::$db->massageValue($value, $fwFieldDefs[$key]);
            }
            $fwRows[] = $sqlValues;

            $oppUnits += $rli->quantity;

            $rli->opportunity_id = $opp->id;

            $oppDateClosed = $rli->service_start_date;
            $oppDateClosedTimestamp = $timedate->fromDbDate($rli->date_closed)->getTimestamp();
            if ($oppDateClosedTimestamp < $rli->date_closed_timestamp) {
                $oppDateClosedTimestamp = $rli->date_closed_timestamp;
                $oppDateClosed = $rli->date_closed;
            }

            $oppAmount = SugarMath::init($oppAmount)
                ->add(SugarCurrency::convertWithRate($rli->likely_case, $baseRate, $opp->base_rate))
                ->result();
            $oppBestCase = SugarMath::init($oppBestCase)
                ->add(SugarCurrency::convertWithRate($rli->best_case, $baseRate, $opp->base_rate))
                ->result();
            $oppWorstCase = SugarMath::init($oppWorstCase)
                ->add(SugarCurrency::convertWithRate($rli->worst_case, $baseRate, $opp->base_rate))
                ->result();

            $return = [
                'date_closed' => $oppDateClosed,
                'date_closed_timestamp' => $oppDateClosedTimestamp,
                'amount' => $oppAmount,
                'best_case' => $oppBestCase,
                'worst_case' => $oppWorstCase,
            ];

            self::insertAndCommit($sqlRli, $rliSql);

            // process all the forecast worksheet rows since we have the correct opp name now
            $tRows = [];
            foreach ($fwRows as $row) {
                $row['opportunity_name'] = self::$db->quoted($opp->name);
                $tRows[] = '(' . join(',', $row) . ')';
            }

            self::insertAndCommit($fwSql, $tRows);

            //Renewal Opp
            if ($usingRLIs) {
                // get all the fields from the opportunities table for the given Opportunity Id
                $sql = 'SELECT * FROM opportunities WHERE deleted = 0 AND id = ' . self::$db->quoted($serviceOpp['opportunity_id']);
                $oppResults = self::$db->query($sql);
                while ($row = self::$db->fetchByAssoc($oppResults)) {
                    static::$oppForRenewal[] = $row;
                }
            }

            foreach (static::$oppForRenewal as $renewalOpp) {
                foreach (array_keys($renewalOpp) as $keys => $renewalOppKey) {
                    $opp->$renewalOppKey = $renewalOpp[$renewalOppKey];
                }
            }

            $opp->id = $rli->opportunity_id;
            $opp->renewal_parent_id = $serviceOpp['opportunity_id'];
            $opp->renewal = 1;
            $opp->sales_stage = $rli->sales_stage;
            $opp->name .= ' - ' . $oppUnits . ' Renewal';
            $opp->sales_status = $app_list_strings['sales_status_dom']['In Progress'];

            if (!in_array($rli->sales_stage, $rli->getClosedStages())) {
                $opp->service_open_revenue_line_items++;
            }

            if (!$usingRLIs) {
                $seed = random_int(1, 15);
                if ($seed % 2 == 0) {
                    $currencyId = $currency->id;
                    $baseRate = $currency->conversion_rate;
                }
            }

            $oppSql = 'INSERT INTO ' . $opp->table_name . ' (' . join(',', array_keys(self::toDatabaseArray($opp))) . ') VALUES';
            $oppRows = [];
            $oppAccRows = [];
            $oppAccRow = [
                'id' => '',
                'opportunity_id' => '',
                'account_id' => '',
                'date_modified' => self::$db->quoted($now),
                'deleted' => 0,
            ];
            $oppAccSql = 'INSERT INTO accounts_opportunities (' . join(',', array_keys($oppAccRow)) . ') VALUES';

            $oppAccRows[] = '(' . join(',', array_merge($oppAccRow, [
                    'id' => self::$db->quoted(create_guid()),
                    'account_id' => self::$db->quoted($serviceOpp['account_id']),
                    'opportunity_id' => self::$db->quoted($opp->id),
                ])) . ')';

            $values = array_merge(self::toDatabaseArray($opp), $return);

            $sqlValues = [];
            foreach ($values as $key => $value) {
                $value = self::evaluateIndexedValue($key, $value, $oppIndices);
                $sqlValues[] = self::$db->massageValue($value, $oppFieldDefs[$key]);
            }
            $oppRows[] = '(' . join(',', $sqlValues) . ')';

            if (!$usingRLIs) {
                $fw->modified_user_id = $account->assigned_user_id;
                $fw->created_by = $account->assigned_user_id;
                $fw->copyValues($fw->opportunityFieldMap, $opp);

                $fw->id = create_guid();
                $fw->parent_id = $opp->id;
                $fwValues = self::toDatabaseArray($fw);

                $sqlValues = [];
                foreach ($fwValues as $key => $value) {
                    $value = self::evaluateIndexedValue($key, $value, $fwIndices);
                    $sqlValues[$key] = self::$db->massageValue($value, $fwFieldDefs[$key]);
                }
                $fwRows[] = '(' . join(',', $sqlValues) . ')';
            }

            self::insertAndCommit($oppSql, $oppRows);
            self::insertAndCommit($oppAccSql, $oppAccRows);
            if (!$usingRLIs) {
                self::insertAndCommit($fwSql, $fwRows);
            }

            $oppIds[] = $opp->id;
        }
        return $oppIds;
    }

    /**
     * @param Opportunity $opp
     * @param int $rlis_to_create
     * @param array $app_list_strings
     */
    private static function createRevenueLineItems(&$opp, $rlis_to_create, $app_list_strings)
    {
        $pt_id = null;
        $service = null;
        $service_duration_value = null;
        $service_duration_unit = null;
        $currency_id = $opp->currency_id;
        $base_rate = $opp->base_rate;

        $pt = null;

        $seed = random_int(1, 15);
        if ($seed % 2 == 0) {
            $currency = SugarCurrency::getCurrencyByISO('EUR');
            $currency_id = $currency->id;
            $base_rate = $currency->conversion_rate;
        }

        $rlis_created = 0;
        $opp_best_case = 0;
        $opp_worst_case = 0;
        $opp_amount = 0;
        $opp_units = 0;
        $opp->total_revenue_line_items = $rlis_to_create;
        $opp->closed_revenue_line_items = 0;
        $opp->included_revenue_line_items = 0;
        $opp->service_open_revenue_line_items = 0;

        $closedWon = 0;
        $closedLost = 0;

        $timedate = TimeDate::getInstance();
        $now = $timedate->nowDb();

        /* @var $rli RevenueLineItem */
        $rli = BeanFactory::newBean('RevenueLineItems');
        $rliFieldDefs = $rli->getFieldDefinitions();
        $rliIndices = $rli->getIndices();
        $rliSql = [];
        $sqlRli = 'INSERT INTO ' . $rli->table_name . '(' . join(',', array_keys(self::toDatabaseArray($rli))) . ') VALUES';


        /* @var $fw ForecastWorksheet */
        $fw = BeanFactory::newBean('ForecastWorksheets');
        $fw->parent_type = 'RevenueLineItems';
        $fw->draft = 1;
        $fw->date_entered = $now;
        $fw->date_modified = $now;
        $fw->modified_user_id = $opp->modified_user_id;
        $fw->created_by = $opp->created_by;
        $fwFieldDefs = $fw->getFieldDefinitions();
        $fwIndices = $fw->getIndices();
        $fwRows = [];
        $fwSql = 'INSERT INTO ' . $fw->table_name . '(' . join(',', array_keys(self::toDatabaseArray($fw))) . ') VALUES';

        $opp_date_closed = '';
        $opp_date_closed_timestamp = 0;

        //Retrieve the first option of the sales stage dom for default
        $salesStageDomOptions = $app_list_strings['sales_stage_dom'];
        $salesStageFirstOption = array_key_first($salesStageDomOptions);

        $oppFieldDefs = $opp->getFieldDefinitions();
        $oppSalesStageFieldDef = $oppFieldDefs['sales_stage'];

        $defaultSalesStage = $oppSalesStageFieldDef['default'] ?? $salesStageFirstOption;

        $latestRliSalesStageIndex = 0;
        $latestRliSalesStageKey = '';

        while ($rlis_created < $rlis_to_create) {
            $amount = random_int(1000, 7500);
            $rand_best_worst = random_int(100, 900);
            $doPT = false;
            $quantity = random_int(1, 100);
            $cost_price = $amount / 2;
            $list_price = $amount;
            $discount_price = ($amount / $quantity);
            if ($rlis_created % 2 === 0) {
                $doPT = true;
                $pt_id = array_rand(static::$pt_ids);
                $pt = static::$pt_ids[$pt_id];
                $cost_price = $pt['cost_price'];
                $list_price = $pt['list_price'];
                $discount_price = ($pt['discount_price'] / $quantity);
                $amount = $pt['discount_price'];
                $rand_best_worst = random_int(100, $cost_price);
                $service = $pt['service'];
                if ($service === '1') {
                    $rlis_to_create = 1;
                    $renewable = $pt['renewable'];
                    $service_duration_unit = $pt['service_duration_unit'];
                    $service_duration_value = $pt['service_duration_value'];
                }
            }

            if ($service === '1') {
                $rli->sales_stage = $app_list_strings['sales_stage_dom']['Closed Won'];
            } else {
                $rli->sales_stage = array_rand($app_list_strings['sales_stage_dom']);
            }

            $rli->team_id = $opp->team_id;
            $rli->team_set_id = $opp->team_set_id;
            $rli->name = $opp->name;
            $rli->best_case = $amount + $rand_best_worst;
            $rli->likely_case = $amount;
            $rli->worst_case = $amount - $rand_best_worst;
            $rli->list_price = $list_price;
            $rli->discount_price = $discount_price;
            $rli->cost_price = $cost_price;
            $rli->quantity = $quantity;
            $rli->currency_id = $currency_id;
            $rli->base_rate = $base_rate;
            $rli->discount_amount = '0.00';
            $rli->book_value = '0.00';
            $rli->deal_calc = '0.00';

            $rli->probability = $app_list_strings['sales_probability_dom'][$rli->sales_stage];
            $isClosed = false;
            $isClosedLost = false;
            if ($rli->sales_stage == Opportunity::STAGE_CLOSED_WON || $rli->sales_stage == Opportunity::STAGE_CLOSED_LOST) {
                $isClosed = true;
                $rli->best_case = $rli->likely_case;
                $rli->worst_case = $rli->likely_case;
                if ($rli->sales_stage == Opportunity::STAGE_CLOSED_WON) {
                    $closedWon++;
                } else {
                    // closedLost shouldn't be added to the opp totals
                    $isClosedLost = true;
                    $closedLost++;
                }
                $opp->closed_revenue_line_items++;
            } else {
                //Determine the latest sales stage based on index of sales stage dom
                $currentSalesStageIndex = array_search($rli->sales_stage, array_keys($salesStageDomOptions));
                if ($currentSalesStageIndex >= $latestRliSalesStageIndex) {
                    $latestRliSalesStageIndex = $currentSalesStageIndex;
                    $latestRliSalesStageKey = $rli->sales_stage;
                }
            }
            $rli->commit_stage = $rli->probability >= 70 ? 'include' : 'exclude';
            $rli->date_closed = ($isClosed) ? self::createPastDate() : self::createDate();
            $rli->date_closed_timestamp = $timedate->fromDbDate($rli->date_closed)->getTimestamp();
            $rli->assigned_user_id = $opp->assigned_user_id;
            $rli->account_id = $opp->account_id;
            $rli->account_name = $opp->account_name;
            $rli->opportunity_id = $opp->id;
            $rli->lead_source = array_rand($app_list_strings['lead_source_dom']);
            $rli->product_type = $opp->opportunity_type;
            if ($rli->commit_stage === 'include') {
                $opp->included_revenue_line_items++;
            }

            // if this is an even number, assign a product template
            if ($doPT) {
                $rli->product_template_id = $pt_id;
                $rli->discount_amount = random_int(100, intval($rli->cost_price));
                $rli->discount_rate_percent = (($rli->discount_amount / $rli->discount_price) * 100);
                foreach ($pt as $field => $value) {
                    if ($field != 'id') {
                        $rli->$field = $value;
                    }
                }
            } else {
                $rli->discount_amount = 0;
                $rli->discount_rate_percent = 0;
                // if this is not an even number, assign a product category only
                $rli->category_id = static::$pc_ids[array_rand(static::$pc_ids, 1)];
            }

            if ($service === '1') {
                $rli->name = $service_duration_value . ' ' . $service_duration_unit . ' Service';
                $opp->name = $rli->name;
                $rli->service_start_date = $rli->date_closed;
                $rli->service_end_date = self::setServiceEndDate($rli);
                if (!in_array($rli->sales_stage, $rli->getClosedStages())) {
                    $opp->service_open_revenue_line_items++;
                }
            }

            // For sell based purchase generation
            $rli->generate_purchase = !$isClosed && static::hasProductLicense('sell') ? 'Yes' : '';

            $rli->total_amount = (($rli->discount_price - $rli->discount_amount) * $rli->quantity);
            $rli->id = create_guid();
            $rli->date_entered = $now;
            $rli->date_modified = $now;
            $rli->modified_user_id = $opp->modified_user_id;
            $rli->created_by = $opp->created_by;

            $values = self::toDatabaseArray($rli);

            $sqlValues = [];
            foreach ($values as $key => $value) {
                $value = self::evaluateIndexedValue($key, $value, $rliIndices);
                $sqlValues[] = self::$db->massageValue($value, $rliFieldDefs[$key]);
            }

            $rliSql[] = '(' . join(',', $sqlValues) . ')';

            $fw->copyValues($fw->productFieldMap, $rli);

            $fw->id = create_guid();
            $fw->parent_id = $rli->id;
            $fwValues = self::toDatabaseArray($fw);

            $sqlValues = [];
            foreach ($fwValues as $key => $value) {
                $value = self::evaluateIndexedValue($key, $value, $fwIndices);
                $sqlValues[$key] = self::$db->massageValue($value, $fwFieldDefs[$key]);
            }
            $fwRows[] = $sqlValues;

            $opp_units += $rli->quantity;

            if ($opp_date_closed_timestamp < $rli->date_closed_timestamp) {
                $opp_date_closed_timestamp = $rli->date_closed_timestamp;
                $opp_date_closed = $rli->date_closed;
            }

            if (!$isClosedLost) {
                $opp_amount = SugarMath::init($opp_amount)
                    ->add(SugarCurrency::convertWithRate($rli->likely_case, $base_rate, $opp->base_rate))
                    ->result();
                $opp_best_case = SugarMath::init($opp_best_case)
                    ->add(SugarCurrency::convertWithRate($rli->best_case, $base_rate, $opp->base_rate))
                    ->result();
                $opp_worst_case = SugarMath::init($opp_worst_case)
                    ->add(SugarCurrency::convertWithRate($rli->worst_case, $base_rate, $opp->base_rate))
                    ->result();
            }
            $rlis_created++;
        }

        if ($rlis_to_create > ($closedWon + $closedLost) || $rlis_to_create === 0) {
            // still in progress
            $opp->sales_status = Opportunity::STATUS_IN_PROGRESS;
        } else {
            // they are equal so if the total lost == total rlis then it's closed lost,
            // otherwise it's always closed won
            if ($closedLost == $rlis_to_create) {
                $opp->sales_status = Opportunity::STATUS_CLOSED_LOST;
            } else {
                $opp->sales_status = Opportunity::STATUS_CLOSED_WON;
            }
        }

        //populate sales stage data
        if ($rlis_created === 0) {
            $opp->sales_stage = $defaultSalesStage;
        } elseif ($closedLost === $rlis_created) {
            $opp->sales_stage = Opportunity::STATUS_CLOSED_LOST;
        } elseif (($closedWon + $closedLost) === $rlis_created) {
            $opp->sales_stage = Opportunity::STATUS_CLOSED_WON;
        } else {
            $opp->sales_stage = $latestRliSalesStageKey;
        }

        $opp->name .= ' - ' . $opp_units . ' Units';
        self::insertAndCommit($sqlRli, $rliSql);

        // process all the forecast worksheet rows since we have the correct opp name now
        $tRows = [];
        foreach ($fwRows as $row) {
            $row['opportunity_name'] = self::$db->quoted($opp->name);
            $tRows[] = '(' . join(',', $row) . ')';
        }
        self::insertAndCommit($fwSql, $tRows);


        return [
            'date_closed' => $opp_date_closed,
            'date_closed_timestamp' => $opp_date_closed_timestamp,
            'amount' => $opp_amount,
            'best_case' => $opp_best_case,
            'worst_case' => $opp_worst_case,
        ];
    }


    /**
     * Calculate service_end_date for service RLI.
     */
    public static function setServiceEndDate($rli)
    {
        if (!empty($rli->service) &&
            !empty($rli->service_start_date) &&
            !empty($rli->service_duration_value) &&
            !empty($rli->service_duration_unit) &&
            empty($rli->service_end_date)
        ) {
            $timeDate = TimeDate::getInstance();
            $duration = '+' . $rli->service_duration_value . ' ' . $rli->service_duration_unit;
            $endDate = $timeDate->fromDbDate($rli->service_start_date)->modify($duration);
            $rli->service_end_date = $endDate->modify('-1 day')->asDbDate();
        }
        return $rli->service_end_date;
    }


    protected static function insertAndCommit($sql, array $rows, $table = '')
    {
        if (self::$db->dbType !== 'oci8') {
            self::$db->query($sql . ' ' . join(',', $rows));
            self::$db->commit();
        } else {
            $sql = substr($sql, 7);
            self::$db->query("INSERT ALL\n\t" . $sql . ' ' . join("\n\t" . $sql . ' ', $rows) .
                "\nSELECT * FROM dual\n");

            self::$db->commit();
        }
    }

    /**
     * @static creates range of probability for the months
     * @param int $total_months - total count of months
     * @return mixed
     */
    private static function getRanges($total_months = 12)
    {
        if (self::$_ranges === null) {
            self::$_ranges = [];
            for ($i = $total_months; $i >= 0; $i--) {
                // define priority for month,
                self::$_ranges[$total_months - $i] = ($total_months - $i > 6)
                    ? self::$_ranges[$total_months - $i] = 6 ** 2 + $i
                    : self::$_ranges[$total_months - $i] = $i ** 2 + 1;
                // increase probability for current quarters
                self::$_ranges[$total_months - $i] = $total_months - $i == 0 ? self::$_ranges[$total_months - $i] * 2.5 : self::$_ranges[$total_months - $i];
                self::$_ranges[$total_months - $i] = $total_months - $i == 1 ? self::$_ranges[$total_months - $i] * 2 : self::$_ranges[$total_months - $i];
                self::$_ranges[$total_months - $i] = $total_months - $i == 2 ? self::$_ranges[$total_months - $i] * 1.5 : self::$_ranges[$total_months - $i];
            }
        }
        return self::$_ranges;
    }

    /**
     * @static return month delta as random value using range of probability, 0 - current month, 1 next/previos month...
     * @param int $total_months - total count of months
     * @return int
     */
    public static function getMonthDeltaFromRange($total_months = 12)
    {
        $key = null;
        $ranges = self::getRanges($total_months);
        asort($ranges, SORT_NUMERIC);
        $x = random_int(1, array_sum($ranges));
        foreach ($ranges as $key => $y) {
            $x -= $y;
            if ($x <= 0) {
                break;
            }
        }
        return $key;
    }

    /**
     * @static generates date
     * @param null $monthDelta - offset from current date in months to create date, 0 - current month, 1 - next month
     * @return string
     */
    public static function createDate($monthDelta = null)
    {
        global $timedate;
        $monthDelta = $monthDelta ?? self::getMonthDeltaFromRange();

        $now = $timedate->getNow(true);
        $now->modify("+$monthDelta month");
        // random day from now to end of month
        $now->setTime(0, 0, 0);
        $day = random_int($now->day, $now->days_in_month);
        return $timedate->asDbDate($now->get_day_begin($day));
    }

    /**
     * @static generate past date
     * @param null $monthDelta - offset from current date in months to create past date, 0 - current month, 1 - previous month
     * @return string
     */
    public static function createPastDate($monthDelta = null)
    {
        global $timedate;
        $monthDelta = $monthDelta ?? self::getMonthDeltaFromRange();

        $now = $timedate->getNow(true);
        $now->modify("-$monthDelta month");

        if ($monthDelta == 0 && $now->day == 1) {
            $now->modify('-1 day');
            $day = $now->day;
        } else {
            // random day from start of month to now
            $tmpDay = ($now->day - 1 != 0) ? $now->day - 1 : 1;
            $day = random_int(1, $tmpDay);
        }
        $now->setTime(0, 0, 0); // always default it to midnight
        return $timedate->asDbDate($now->get_day_begin($day));
    }

    /**
     * @static evaluates indexed value
     * @param string $name field name from a bean
     * @param int|string|null $value field value from a bean
     * @param array $indices index definitions of the module
     * @return
     */
    public static function evaluateIndexedValue(string $name, $value, array $indices)
    {
        $indexDef = [];
        // Most indices don't have the field name defined as key in the $indices array, even it does,
        // we cannot rely on the key as a valid field name, so we need to find out if it exists
        // in the 'fields' of $index meta data.
        foreach ($indices as $key => $index) {
            if (!empty($index['fields']) &&
                is_array($index['fields']) &&
                safeInArray($name, $index['fields'])) {
                $indexDef = $index;
                break;
            }
        }

        // Field with unique index type and has empty value should return null because DBs treat
        // empty string as unique value, null as not set. Thus, such empty field value needs to be
        // converted to null to avoid inserting duplicate entry error for database.
        if (empty($value) &&
            isset($indexDef['type']) &&
            $indexDef['type'] === 'unique') {
            return null;
        }
        return $value;
    }

    /**
     * Returns an associative array where the keys are the names of the fields that are stored in the database and the values are their values
     * @param SugarBean $bean
     * @return array
     */
    private static function toDatabaseArray(SugarBean $bean): array
    {
        return array_filter($bean->toArray(), static function ($key) use ($bean): bool {
            $data = $bean->field_defs[$key];
            return !isset($data['source']) || $data['source'] === 'db';
        }, ARRAY_FILTER_USE_KEY);
    }
}
