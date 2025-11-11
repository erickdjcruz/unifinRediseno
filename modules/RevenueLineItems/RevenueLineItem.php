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

// Product is used to store customer information.
class RevenueLineItem extends SugarBean
{
    public const STATUS_CONVERTED_TO_QUOTE = 'Converted to Quote';

    public const STATUS_QUOTED = 'Quotes';

    // Stored fields
    public $id;
    public $deleted;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $created_by;
    public $created_by_name;
    public $modified_by_name;
    public $name;
    public $product_template_id;
    public $description;
    public $vendor_part_num;
    public $cost_price;
    public $discount_price;
    public $list_price;
    public $list_usdollar;
    public $discount_usdollar;
    public $cost_usdollar;
    public $deal_calc;
    public $deal_calc_usdollar;
    public $discount_amount_usdollar;
    public $currency_id;
    public $mft_part_num;
    public $status;
    public $date_purchased;
    public $weight;
    public $quantity;
    public $website;
    public $tax_class;
    public $support_name;
    public $support_description;
    public $support_contact;
    public $support_term;
    public $date_support_expires;
    public $date_support_starts;
    public $pricing_formula;
    public $pricing_factor;
    public $team_id;
    public $serial_number;
    public $asset_number;
    public $book_value;
    public $book_value_usdollar;
    public $book_value_date;
    public $currency_symbol;
    public $currency_name;
    public $default_currency_symbol;
    public $discount_amount;
    public $discount_select;
    public $best_case;
    public $likely_case;
    public $worst_case;
    public $base_rate;
    public $probability;
    public $date_closed;
    public $date_closed_timestamp;
    public $commit_stage;
    public $product_type;
    public $discount_amount_signed;
    public $total_amount;
    public $service_start_date;
    public $service;
    public $renewal;

    public $forecasted_likely;

    /**
     * @public String      The Current Sales Stage
     */
    public $sales_stage;

    // These are for related fields
    public $assigned_user_id;
    public $assigned_user_name;
    public $type_name;
    public $type_id;
    public $quote_id;
    public $quote_name;
    public $manufacturer_name;
    public $manufacturer_id;
    public $category_name;
    public $category_id;
    public $account_name;
    public $account_id;
    public $opportunity_id;
    public $opportunity_name;
    public $contact_name;
    public $contact_id;
    public $related_product_id;
    public $contracts;
    public $product_index;
    public $renewal_rli_id;
    public $add_on_to_id;

    public $table_name = 'revenue_line_items';
    public $rel_manufacturers = 'manufacturers';
    public $rel_types = 'product_types';
    public $rel_products = 'product_product';
    public $rel_categories = 'product_categories';

    public $object_name = 'RevenueLineItem';
    public $module_dir = 'RevenueLineItems';
    public $new_schema = true;
    public $importable = false;

    /**
     * Flag used for purchase generation within Sell
     * @var string
     */
    public $generate_purchase;

    public $experts;

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = ['quote_id', 'quote_name', 'related_product_id'];

    /** Fields to copy when generating a Purchase */
    public $purchaseCopyFields = [
        'account_id', 'account_name', 'acl_team_set_id', 'assigned_user_id',
        'assigned_user_name', 'category_id', 'product_template_id',
        'product_template_name', 'name', 'service', 'renewable', 'team_id', 'team_set_id',
        'type_id',
    ];

    /** Fields to copy when generating a Purchased Line Item */
    public $pliCopyFields = [
        'name', 'date_closed', 'quantity', 'discount_select', 'discount_amount',
        'discount_price', 'renewable', 'description', 'assigned_user_id',
        'assigned_user_name', 'team_id', 'team_set_id', 'acl_team_set_id',
        'asset_number', 'base_rate', 'vendor_part_num', 'list_price',
        'tax_class', 'weight', 'website', 'serial_number', 'cost_price',
        'mft_part_num', 'book_value_date', 'book_value', 'support_term',
        'support_title', 'support_expires', 'support_starts',
        'support_contact', 'support_desc', 'product_template_id',
        'product_template_name', 'currency_id', 'renewal',
        'catalog_service_duration_value', 'catalog_service_duration_unit',
    ];

    /** Fields to map when generating a Purchased Line Item */
    public $pliMapFields = [
        'likely_case' => 'revenue',
    ];

    /**
     * Default Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $currency = BeanFactory::newBean('Currencies');
        $this->default_currency_symbol = $currency->getDefaultCurrencySymbol();
    }

    /**
     * Get summary text
     */
    public function get_summary_text()
    {
        return "$this->name";
    }

    /**
     * Bean specific logic for when SugarFieldCurrency_id::save() is called to make sure we can update the base_rate
     *
     * @return bool
     */
    public function updateCurrencyBaseRate()
    {
        return !safeInArray($this->sales_stage, $this->getClosedStages());
    }

    /**
     * Utility Method to make sure the best/worst case values are set
     */
    protected function setBestWorstFromLikely()
    {
        if ($this->ACLFieldAccess('best_case', 'write') &&
            empty($this->best_case) &&
            (string)$this->best_case !== '0'
        ) {
            $this->best_case = $this->likely_case;
        }
        if ($this->ACLFieldAccess('worst_case', 'write') &&
            empty($this->worst_case) &&
            (string)$this->worst_case !== '0'
        ) {
            $this->worst_case = $this->likely_case;
        }
    }

    /**
     * Utility method for checking the quantity
     */
    protected function checkQuantity()
    {
        if ($this->quantity === '' || is_null($this->quantity)) {
            $this->quantity = 0;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save($check_notify = false)
    {
        //If an opportunity_id value is provided, lookup the Account information (if available)
        if (!empty($this->opportunity_id)) {
            $this->setAccountIdForOpportunity($this->opportunity_id);
            //Updates the non-SugarLogic rollup fields on the Opportunity if necessary
            $this->addOpportunityToResaveList($this->opportunity_id);
        }

        $this->setBestWorstFromLikely();

        $this->checkQuantity();

        $this->setDiscountPrice();

        $this->setCommitStage();

        $this->mapFieldsFromProductTemplate();
        $this->mapFieldsFromOpportunity();

        $this->setDurationFields();
        $this->setServiceEndDate();

        $id = parent::save($check_notify);
        // this only happens when ent is built out
        $this->saveProductWorksheet();

        // Update rollups on parent records
        $this->updateRelatedAccount($this->account_id);

        return $id;
    }

    /**
     * Enforces that the RevenueLineItem has the correct commit stage if its
     * sales stage is in a closed won or closed lost state
     */
    protected function setCommitStage()
    {
        $settings = Forecast::getSettings();
        if ($settings['is_setup']) {
            $won = $settings['sales_stage_won'];
            $lost = $settings['sales_stage_lost'];

            if (safeInArray($this->sales_stage, $won)) {
                $this->commit_stage = 'include';
            } elseif (safeInArray($this->sales_stage, $lost)) {
                $this->commit_stage = 'exclude';
            }
        }
    }


    /**
     * Calculate service_end_date for service RLI.
     */
    protected function setServiceEndDate()
    {
        $clearServiceValues = false;
        if (!empty($this->service) &&
            !empty($this->service_start_date) &&
            !empty($this->service_duration_value) &&
            !empty($this->service_duration_unit)
        ) {
            try {
                $this->service_end_date = TimeDate::getInstance()->fromString($this->service_start_date)
                    ->modify('+' . $this->service_duration_value . ' ' . $this->service_duration_unit)
                    ->modify('-1 day')
                    ->asDbDate(false);
            } catch (Exception $e) {
                $GLOBALS['log']->error('Error calculating service end date:' . $e->getMessage());
                $clearServiceValues = true;
            }
        } else {
            $clearServiceValues = true;
        }

        // If this is not a service RLI, or we encountered an error trying to
        // calculate the end date, clear the related service fields to avoid
        // any problems due to partial service data
        if ($clearServiceValues) {
            $this->service = false;
            $this->service_start_date = null;
            $this->service_end_date = null;
            $this->service_duration_value = null;
            $this->service_duration_unit = null;
        }
    }

    /**
     * Set the service duration for coterm RLIs so the end date remains constant
     */
    protected function setDurationFields()
    {
        if (!empty($this->add_on_to_id) &&
            !empty($this->service_start_date) &&
            !empty($this->service_end_date)) {
            $startDate = new \SugarDateTime($this->service_start_date);
            // calculates inclusive of the end date
            $endDate = new \SugarDateTime($this->service_end_date);
            $endDate->modify('+1 day');

            // calculates whole years/months, otherwise, days is used
            $diff = $startDate->diff($endDate);
            if ($diff->d > 0) {
                $this->service_duration_unit = 'day';
                $this->service_duration_value = $diff->days;
            } elseif ($diff->m > 0) {
                $this->service_duration_unit = 'month';
                $this->service_duration_value = $diff->y * 12 + $diff->m;
            } elseif ($diff->y > 0) {
                $this->service_duration_unit = 'year';
                $this->service_duration_value = $diff->y;
            } else {
                $this->service_duration_unit = 'day';
                $this->service_duration_value = -1;
            }
        }
    }

    /**
     * Updates next_renewal_date on related Account if necessary
     *
     * @param string $id the ID of the account to update
     * @throws SugarQueryException
     */
    protected function updateRelatedAccount($id = null)
    {
        $accountBean = BeanFactory::retrieveBean('Accounts', $id);
        if (!empty($accountBean->id)) {
            // Get the minimum date_closed of all RLIs related to the account
            // that are of type "Existing Business" and are renewable
            $db = DBManagerFactory::getInstance();
            $q = new SugarQuery();
            $q->from($this);
            $q->select('date_closed');
            $q->where()->queryAnd()
                ->equals('account_id', $accountBean->id)
                ->equals('product_type', 'Existing Business')
                ->equals('renewable', 1);
            $q->orderBy('date_closed', 'ASC');
            $result = $db->fromConvert($q->getOne(), 'date');
            $result = !empty($result) ? $result : '';

            // If the value is different, update the account
            if ($accountBean->next_renewal_date !== $result) {
                $accountBean->next_renewal_date = $result;
                $accountBean->save();
            }
        }
    }

    /**
     * Check if it is an open renewal Revenue Line Item.
     *
     * @return bool
     */
    public function isOpenRenewalRLI()
    {
        return ($this->sales_stage !== Opportunity::STATUS_CLOSED_WON &&
            $this->sales_stage !== Opportunity::STATUS_CLOSED_LOST &&
            $this->renewable == 1);
    }

    /**
     * Force add Opportunity to the resave list
     *
     * @param string $id the ID of the Opportunity to update
     */
    protected function addOpportunityToResaveList($id = null)
    {
        if ($opp = BeanFactory::retrieveBean('Opportunities', $id)) {
            SugarRelationship::addToResaveList($opp);
        }
    }

    /**
     * Set the discount_price
     */
    protected function setDiscountPrice()
    {
        if (!is_numeric($this->discount_price) &&
            empty($this->product_template_id) &&
            is_numeric($this->likely_case)
        ) {
            $quantity = floatval($this->quantity);

            if (empty($quantity)) {
                $quantity = 1;
            }

            $this->discount_price = SugarMath::init($this->likely_case)->div($quantity)->result();
        }
    }


    /**
     * Override the current SugarBean functionality to make sure that when this method is called that it will also
     * take care of any draft worksheets by rolling-up the data, as well as
     * updating rollup fields on the related Account
     *
     * @param string $id The ID of the record we want to delete
     */
    public function mark_deleted($id)
    {
        // Grab the IDs of the related modules as these fields are removed in the
        // call to the parent mark_deleted
        $accountId = $this->account_id;

        //Updates the non-SugarLogic rollup fields on the Opportunity if necessary
        if (!empty($this->opportunity_id)) {
            $this->addOpportunityToResaveList($this->opportunity_id);
        }

        parent::mark_deleted($id);

        // this only happens when ent is built out
        $this->saveProductWorksheet();

        // Update rollups on parent records
        $this->updateRelatedAccount($accountId);
    }


    /**
     * map fields if opportunity id is set
     */
    protected function mapFieldsFromOpportunity()
    {
        if (!empty($this->opportunity_id) && empty($this->product_type)) {
            $opp = BeanFactory::getBean('Opportunities', $this->opportunity_id);
            $this->product_type = $opp->opportunity_type;
        }
    }


    /**
     * Save the updated product to the worksheet, this will create one if one does not exist
     * this will also update one if a draft version exists
     *
     * @return bool         True if the worksheet was saved/updated, false otherwise
     */
    protected function saveProductWorksheet()
    {
        $settings = Forecast::getSettings();
        if ($settings['is_setup'] && $settings['forecast_by'] === $this->module_name) {
            // save the a draft of each product
            /* @var $worksheet ForecastWorksheet */
            $worksheet = BeanFactory::newBean('ForecastWorksheets');
            $worksheet->saveRelatedProduct($this);
            return true;
        }

        return false;
    }

    /**
     * Sets the account_id value for instance given an opportunityId argument of the Opportunity id
     *
     * @param $opportunityId String value of the Opportunity id
     * @return bool true if account_id was set; false otherwise
     */
    protected function setAccountIdForOpportunity($opportunityId)
    {
        $opp = BeanFactory::getBean('Opportunities', $opportunityId);
        if ($opp->load_relationship('accounts')) {
            $accounts = $opp->accounts->get();
            if (!empty($accounts)) {
                // get the first row
                $this->account_id = array_shift($accounts);
                return true;
            }
        }
        return false;
    }

    /**
     * Handle the mapping of the fields from the product template to the product
     */
    protected function mapFieldsFromProductTemplate()
    {
        if ($this->product_template_id && (
            empty($this->fetched_row) || $this->fetched_row['product_template_id'] != $this->product_template_id
        )) {
            /* @var $pt ProductTemplate */
            $pt = BeanFactory::getBean('ProductTemplates', $this->product_template_id);

            $this->category_id = $pt->category_id;
            $this->mft_part_num = $pt->mft_part_num;
            $this->list_price = SugarCurrency::convertAmount($pt->list_price, $pt->currency_id, $this->currency_id);
            $this->cost_price = SugarCurrency::convertAmount($pt->cost_price, $pt->currency_id, $this->currency_id);
            $this->discount_price = SugarCurrency::convertAmount($pt->discount_price, $pt->currency_id, $this->currency_id); // discount_price = unit price on the front end...
            $this->list_usdollar = $pt->list_usdollar;
            $this->cost_usdollar = $pt->cost_usdollar;
            $this->discount_usdollar = $pt->discount_usdollar;
            $this->tax_class = $pt->tax_class;
            $this->weight = $pt->weight;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function bean_implements($interface)
    {
        // if we are installing, we want to return false, really hacky, but OOB default on ENT and ULT is to not
        // have RevenueLineItems ACLed
        if (isset($GLOBALS['installing']) && $GLOBALS['installing'] === true) {
            return false;
        }

        // if we are using opportunities with RLI's we should return true, otherwise return false
        $settings = Opportunity::getSettings();
        if (isset($settings['opps_view_by']) && $settings['opps_view_by'] === 'RevenueLineItems') {
            switch ($interface) {
                case 'ACL':
                    return true;
            }
        }
        return false;
    }

    /**
     * Copy the values from the provided array of fields from this RLI onto the
     * provided bean.
     *
     * @param SugarBean $bean Bean to receive copied attributes
     * @param array $copyFields Fields to copy
     */
    public function copyFieldsToBean(SugarBean $bean, array $copyFields): void
    {
        foreach ($copyFields as $field) {
            $bean->$field = $this->$field;
        }
    }

    /**
     * Map fields represented by the keys in the array from this RLI to fields
     * on the provided bean represented by the array's values. E.g. providing
     * [ 'fromField' => 'toField' ]
     * would copy $this->fromField to $bean->toField for each key/value pair.
     *
     * @param SugarBean $bean Bean to receive this RLI values
     * @param array $mapFields Key/value pairs of field names to map
     */
    public function mapFieldsToBean(SugarBean $bean, array $mapFields): void
    {
        foreach ($mapFields as $rliField => $beanField) {
            $bean->$beanField = $this->$rliField;
        }
    }

    /**
     * Get the ID of purchase with the same product/account link as this RLI.
     * If none exists, return null.
     * @return string|null
     * @throws SugarQueryException
     */
    public function getMatchingPurchaseId(): ?string
    {
        $query = new \SugarQuery();
        $query->from(BeanFactory::newBean('Purchases'));
        $query->select(['id']);
        $query->where()->queryAnd()
            ->equals('account_id', $this->account_id)
            ->equals('product_template_id', $this->product_template_id);
        $result = $query->getOne();
        return !empty($result) ? $result : null;
    }

    /**
     * Create a new Purchase from this RLI. Purchases should inherit the fields
     * listed in copyFields from the RLI, as well as any custom fields with
     * an exact field name match.
     *
     * Method returns purchase for use in creating PLI.
     *
     * @return Purchase|null
     */
    public function generatePurchaseFromRli(): Purchase
    {
        $purchase = BeanFactory::newBean('Purchases');
        $this->copyFieldsToBean($purchase, $this->purchaseCopyFields);
        $this->copyCustomFields($purchase);
        $purchase->save();
        return $purchase;
    }

    /**
     * Create a new PLI populated with values from this RLI. Relate it to the
     * given Purchase, and this RLI.
     * @param Purchase $purchase
     *
     * @return PurchasedLineItem|null
     */
    public function generatePliFromRli(Purchase $purchase): PurchasedLineItem
    {
        $pli = BeanFactory::newBean('PurchasedLineItems');
        if ($this->service) {
            array_push(
                $this->pliCopyFields,
                'service_start_date',
                'service_end_date',
                'service_duration_value',
                'service_duration_unit'
            );
        } else {
            $pli->service_start_date = $this->date_closed;
            $pli->service_end_date = $this->date_closed;
            $pli->service_duration_value = 1;
            $pli->service_duration_unit = 'day';
        }

        if ($this->renewable && !empty($this->renewal_rli_id)) {
            $renewalRli = BeanFactory::retrieveBean('RevenueLineItems', $this->renewal_rli_id);
            if (!empty($renewalRli->id)) {
                $pli->renewal_opp_id = $renewalRli->opportunity_id;
            }
        }

        if (!empty($purchase->account_id)) {
            $pli->account_id = $purchase->account_id;
        }

        $this->copyFieldsToBean($pli, $this->pliCopyFields);
        $this->mapFieldsToBean($pli, $this->pliMapFields);
        $this->copyCustomFields($pli);
        $purchase->mapFieldsToPli($pli);
        $pli->save();

        if ($purchase->load_relationship('purchasedlineitems')) {
            $purchase->purchasedlineitems->add($pli);
        }

        if ($this->load_relationship('documents') &&
            $pli->load_relationship('documents')) {
            foreach ($this->documents->getBeans() as $document) {
                $pli->documents->add($document);
            }
        }
        return $pli;
    }

    /**
     * Copy custom field values from this RLI
     *
     * @param $bean SugarBean Object to receive custom attributes
     */
    public function copyCustomFields(SugarBean $bean)
    {
        $customFields = $this->getFieldDefinitions('source', ['custom_fields']);
        foreach (array_keys($customFields) as $field) {
            // If a custom RLI field matches our target bean, copy it
            if (array_key_exists($field, $bean->field_defs)) {
                $bean->$field = $this->$field;
            }
        }
    }

    /**
     * A Revenue Line Item should only generate a purchase if all of these
     * conditions are met:
     * 1. Its sales stage is "Closed Won", or in Forecast settings "Closed Won" stages
     * 2. Generate Purchase set to "Yes"
     * 3. Parent Opportunity's sales stage is "Closed Won" or in Forecast "Closed Won" stages
     * @return bool
     */
    protected function shouldGeneratePurchase(): bool
    {
        // Check our RLI properties first so we don't retrieve Opp info we don't
        // need
        $closedWon = Forecast::getSettings()['sales_stage_won'] ?? [Opportunity::STAGE_CLOSED_WON];
        if ($this->generate_purchase !== 'Yes' ||
            !safeInArray($this->sales_stage, $closedWon)) {
            return false;
        }
        $parentOpp = BeanFactory::retrieveBean('Opportunities', $this->opportunity_id);
        if (!safeInArray($parentOpp->sales_stage, $closedWon)) {
            return false;
        }
        return true;
    }

    /**
     * Process chunks of RLI Ids to create Purchases and Purchased Line Items as
     * appropriate. This function assumes data comes in the form of the return
     * from a SugarQuery->execute(); i.e.
     * [
     *   [ 'id' => abc,],
     *   [ 'id' => def,],
     * ]
     *
     * @param $data
     * @see OpportunityWithRevenueLineItem::processOpportunityIds for comparison
     */
    public static function processRliIds(array $data): void
    {
        // Disable activity stream and FTS index during mass record creation
        Activity::disable();
        $ftsSearch = \Sugarcrm\Sugarcrm\SearchEngine\SearchEngine::getInstance();
        $ftsSearch->setForceAsyncIndex(true);


        foreach ($data as $row) {
            $rli = BeanFactory::retrieveBean('RevenueLineItems', $row['id']);

            // Only perform work if we have a DB match for this ID, and it
            // meets the conditions to generate a Purchase
            if ($rli && $rli->shouldGeneratePurchase()) {
                $purchaseId = $rli->getMatchingPurchaseId();
                $purchase = null;
                if ($purchaseId) {
                    // If we can find a matching purchase ID, retrieve that purchase
                    // rather than making a new purchase
                    $purchase = BeanFactory::retrieveBean('Purchases', $purchaseId);
                }
                // If purchase doesn't exist or bean retrieval fails, create a new one
                if (!$purchase) {
                    $purchase = $rli->generatePurchaseFromRli();
                }

                $pli = $rli->generatePliFromRli($purchase);
                $rli->load_relationship('purchasedlineitem');
                $rli->purchasedlineitem->add($pli);
                // The relationship between PLIs and RLIs sets the "rli_id" value
                // on the PLI, but we need to set the pli_id attribute on the RLI.
                $rli->purchasedlineitem_id = $pli->id;
                $rli->generate_purchase = 'Completed';
                $rli->save();
            }
        }

        // Reset FTS indexing and Activity Streams to their previous state
        $ftsSearch->setForceAsyncIndex(
            SugarConfig::getInstance()->get('search_engine.force_async_index', false)
        );
        Activity::restoreToPreviousState();
    }

    /**
     * Util function to schedule a purchase generation jobs for a given array of
     * RLI IDs in the form returned by a SugarQuery. i.e.
     * [
     *   [ 'id' => abc,],
     *   [ 'id' => def,],
     * ]
     *
     * @param array $data list of RLI Ids to schedule in a job
     * @return array of ScheduleJob IDs
     */
    public static function schedulePurchaseGenerationJob(array $data): array
    {
        global $current_user;

        $ret = [];
        if (Opportunity::usingRevenueLineItems()) {
            $jobGroup = md5(microtime());
            $jq = new SugarJobQueue();

            foreach (array_chunk($data, 100) as $chunk) {
                /* @var $job SchedulersJob */
                $job = BeanFactory::newBean('SchedulersJobs');
                $job->name = 'Generate Purchases for Closed RLIs';
                $job->target = 'class::SugarJobCreatePurchasesAndPLIs';
                $job->data = json_encode(['data' => $chunk]);
                $job->retry_count = 0;
                $job->assigned_user_id = $current_user->id;
                $job->job_group = empty($jobGroup) ? md5(microtime()) : $jobGroup;

                $ret[] = $jq->submitJob($job);
            }
        }

        return $ret;
    }

    /**
     * {@inheritdoc}
     * @deprecated
     */
    public function listviewACLHelper()
    {
        $GLOBALS['log']->deprecated('RevenueLineItem::listviewACLHelper() has been deprecated in 7.8');
        $array_assign = parent::listviewACLHelper();

        $is_owner = false;
        if (!empty($this->contact_name)) {
            if (!empty($this->contact_name_owner)) {
                global $current_user;
                $is_owner = $current_user->id == $this->contact_name_owner;
            }
        }
        if (ACLController::checkAccess('Contacts', 'view', $is_owner)) {
            $array_assign['CONTACT'] = 'a';
        } else {
            $array_assign['CONTACT'] = 'span';
        }
        $is_owner = false;
        if (!empty($this->account_name)) {
            if (!empty($this->account_name_owner)) {
                global $current_user;
                $is_owner = $current_user->id == $this->account_name_owner;
            }
        }
        if (ACLController::checkAccess('Accounts', 'view', $is_owner)) {
            $array_assign['ACCOUNT'] = 'a';
        } else {
            $array_assign['ACCOUNT'] = 'span';
        }
        $is_owner = false;
        if (!empty($this->quote_name)) {
            if (!empty($this->quote_name_owner)) {
                global $current_user;
                $is_owner = $current_user->id == $this->quote_name_owner;
            }
        }
        if (ACLController::checkAccess('Quotes', 'view', $is_owner)) {
            $array_assign['QUOTE'] = 'a';
        } else {
            $array_assign['QUOTE'] = 'span';
        }

        return $array_assign;
    }

    /**
     * Converts (copies) RLI to Products (QuotedLineItem)
     * @return Product
     */
    public function convertToQuotedLineItem()
    {
        /* @var $product Product */
        $product = BeanFactory::newBean('Products');
        $product->id = create_guid();
        $product->new_with_id = true;
        foreach ($this->getFieldDefinitions() as $field) {
            if ($field['name'] == 'id') {
                // if it's the ID field, associate it back to the product on the relationship field
                $product->revenuelineitem_id = $this->{$field['name']};
                // In addition to the 1:1 relation above, set the ID for the 1:M relation
                $product->parent_rli_id = $this->{$field['name']};
            } else {
                $product->{$field['name']} = $this->{$field['name']};
            }
        }
        // use product name if available
        if (!empty($this->product_template_id)) {
            $pt = BeanFactory::getBean('ProductTemplates', $this->product_template_id);
            if (!empty($pt) && !empty($pt->name)) {
                $product->name = $pt->name;
            }
        }
        // if discount_price (unit_price) is not set, use likely_case
        if (strlen($this->discount_price) == 0) {
            $product->discount_price = $this->likely_case;
        }

        if (!empty($this->add_on_to_id)) {
            $product->add_on_to_id = $this->add_on_to_id;
        }

        return $product;
    }

    /**
     * Test if this rli can be converted to a quote.
     *
     * @return bool|string  Returns true if it can be converted, otherwise it returns
     *                      a string with the reason it couldn't be converted.
     */
    public function canConvertToQuote()
    {
        $mod_strings = return_module_language($GLOBALS['current_language'], $this->module_dir);
        if (empty($this->product_template_id) && !empty($this->category_id)) {
            return $mod_strings['LBL_CONVERT_INVALID_RLI_PRODUCT'];
        }

        return true;
    }

    /**
     * getClosedStages
     *
     * Return an array of closed stage names from the admin bean.
     *
     * @access protected
     * @return array array of closed stage values
     */
    public function getClosedStages()
    {
        $settings = Forecast::getSettings();

        // get all possible closed stages
        $stages = array_merge(
            (array)$settings['sales_stage_won'],
            (array)$settings['sales_stage_lost']
        );
        // db quote values
        foreach ($stages as $stage_key => $stage_value) {
            $stages[$stage_key] = $this->db->quote($stage_value);
        }
        return $stages;
    }
}
