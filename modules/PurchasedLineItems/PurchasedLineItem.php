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

class PurchasedLineItem extends Basic
{
    public $module_dir = 'PurchasedLineItems';
    public $object_name = 'PurchasedLineItem';
    public $table_name = 'purchased_line_items';
    public $module_name = 'PurchasedLineItems';

    public $name;
    public $book_value_usdollar;
    public $cost_price;
    public $cost_usdollar;
    public $date_closed;
    public $date_closed_timestamp;
    public $deal_calc;
    public $deal_calc_usdollar;
    public $discount_amount;
    public $discount_amount_signed;
    public $discount_amount_usdollar;
    public $discount_price;
    public $discount_select;
    public $discount_usdollar;
    public $list_usdollar;
    public $mft_part_num;
    public $quantity;
    public $revenue;
    public $revenue_usdollar;
    public $total_amount;
    public $yearly_revenue;
    public $renewal;

    // Fields for relationships
    public $purchase_id;
    public $categories;
    public $category_id;
    public $category_name;
    public $manufacturer;
    public $manufacturer_id;
    public $manufacturer_name;
    public $product_templates;
    public $product_template_id;
    public $product_template_name;
    public $product_type;
    public $product_type_id;
    public $product_type_name;
    public $revenuelineitem;
    public $revenuelineitem_id;
    public $revenuelineitem_name;

    // Fields for "Activity" relationships
    public $calls;
    public $emails;
    public $meetings;
    public $notes;
    public $tasks;

    public $importable = true;

    /**
     * {@inheritDoc}
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function save($check_notify = false)
    {
        $this->setServiceEndDate();
        $id = parent::save($check_notify);
        $this->updateRelatedPurchase($this->purchase_id);

        return $id;
    }

    /**
     * Updates start_date and end_date on related Purchase if necessary
     *
     * @param string $id the ID of the Purchase to update
     * @throws SugarQueryException
     */
    protected function updateRelatedPurchase($id = null)
    {
        $purchaseBean = BeanFactory::retrieveBean('Purchases', $id);
        $purchaseId = $purchaseBean->id;
        $doesUpdate = false;
        if (!empty($purchaseId)) {
            // Purchase fields to be updated on addition/deletion of a PLI
            $rollupFields = [
                'start_date' => $this->getPurchaseStartDate($purchaseId),
                'end_date' => $this->getPurchaseEndDate($purchaseId),
            ];

            // Update the Purchase with the calculated rollup values
            foreach ($rollupFields as $field => $calculatedValue) {
                if ($purchaseBean->$field !== $calculatedValue) {
                    $purchaseBean->$field = $calculatedValue;
                    $doesUpdate = true;
                }
            }

            // If the Purchase bean was updated then save the bean
            if ($doesUpdate) {
                $purchaseBean->save();
            }
        }
    }

    /**
     * Gets a start date or end date for a purchase
     *
     * @param string $type Either start or end
     * @param string $purchaseId the ID of the Purchase
     * @return string The end date or start date
     * @throws SugarQueryException
     */
    protected function getPurchaseDateByType(string $type, string $purchaseId): string
    {
        // Get the sort order based on service start or end date
        $sort = $type === 'start' ? 'ASC' : 'DESC';
        $field = sprintf('service_%s_date', $type);

        // Start working the query
        $db = DBManagerFactory::getInstance();

        // Get the relevant date for the related PLI
        $q = new SugarQuery();
        $q->from($this);
        $q->select($field);
        $q->where()->equals('purchase_id', $purchaseId);
        $q->orderBy($field, $sort);
        $date = $db->fromConvert($q->getOne(), 'date');
        return !empty($date) ? $date : '';
    }

    /**
     * Gets the end date for a purchase
     *
     * @param string $purchaseId the ID of the Purchase
     * @return string The purchase End Date
     * @throws SugarQueryException
     */
    public function getPurchaseEndDate(string $purchaseId): string
    {
        return $this->getPurchaseDateByType('end', $purchaseId);
    }

    /**
     * Gets the start date for a purchase
     *
     * @param string $purchaseId the ID of the Purchase
     * @return string The purchase Start Date
     * @throws SugarQueryException
     */
    public function getPurchaseStartDate(string $purchaseId): string
    {
        return $this->getPurchaseDateByType('start', $purchaseId);
    }

    /**
     * Override the current SugarBean functionality to make sure that when this method is called that it will also
     * update rollup fields on the related Purchase
     *
     * @param string $id The ID of the record we want to delete
     */
    public function mark_deleted($id)
    {
        // Grab the IDs of the related modules as these fields are removed in the
        // call to the parent mark_deleted
        $purchaseId = $this->purchase_id;

        parent::mark_deleted($id);

        // Update rollups on parent records
        $this->updateRelatedPurchase($purchaseId);
    }

    /**
     * The method will unset our PLI <-> RLI relationship when a PLI is deleted.
     * @override SugarBean::mark_relationships_deleted()
     * @param string $id The ID of the record who's RLI relationship we want to update based on our deleted PLI.
     */
    public function mark_relationships_deleted($id)
    {
        if (!empty($this->revenuelineitem_id)) {
            $qb = DBManagerFactory::getConnection()->createQueryBuilder();
            $qb->update('revenue_line_items')
                ->set('purchasedlineitem_id', DBManagerFactory::getInstance()->quoted(''));
            $qb->where($qb->expr()->eq('id', $qb->createPositionalParameter($this->revenuelineitem_id)));
            $qb->execute();
        }
        parent::mark_relationships_deleted($id);
    }

    /**
     * Calculate service_end_date for service PLI.
     *
     * If 'service' or any required service-related fields are empty, or if
     * setting the service end date fails, clear service-related fields to avoid
     * problems resulting from partial service data.
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
                $msg = 'Error calculating the ending service date for Purchased Line Item ' . $this->name . ': ' . $e->getMessage();
                LoggerManager::getLogger()->error($msg);
                $clearServiceValues = true;
            }
        }

        // If this is not a service PLI, or we encountered an error trying to
        // calculate the end date, set default values for non-service PLI to
        // avoid service-related calculation errors from incomplete data
        if ($clearServiceValues) {
            $this->service = false;
            $this->service_duration_unit = 'day';
            $this->service_duration_value = '1';
        }
    }
}
