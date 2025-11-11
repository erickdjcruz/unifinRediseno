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

class Purchase extends Basic
{
    public $module_dir = 'Purchases';
    public $object_name = 'Purchase';
    public $table_name = 'purchases';
    public $module_name = 'Purchases';
    public $importable = true;

    // Fields
    public $name;
    public $start_date;
    public $end_date;
    public $service;
    public $renewable;
    public $product_template_id;
    public $product_template_name;
    public $account_name;
    public $account_id;
    public $type_id;
    public $type_name;
    public $category_id;
    public $category_name;

    public $pliCopyFields = [
        'service' => 'service',
        'type_id' => 'product_type_id',
        'type_name' => 'product_type_name',
        'category_id' => 'category_id',
        'category_name' => 'category_name',
    ];

    public $relationship_fields = [
        'account_id' => 'accounts',
    ];

    /**
     * {@inheritDoc}
     *
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    public function mapFieldsToPli(PurchasedLineItem $pli)
    {
        foreach ($this->pliCopyFields as $purchaseField => $pliField) {
            $pli->$pliField = $this->$purchaseField;
        }
    }

    /**
     * This function saves changes to accounts in Purchases' related module PurchasedLineItems.
     *
     * @param boolean $is_update true if this save is an update
     * @param array $exclude exclude relationships
     */
    public function save_relationship_changes($is_update, $exclude = [])
    {
        if (!empty($this->account_id) &&
            isset($this->rel_fields_before_value['account_id']) &&
            (trim($this->account_id) != trim($this->rel_fields_before_value['account_id']))
        ) {
            $relationshipsToBeTouched = ['purchasedlineitems'];
            foreach ($relationshipsToBeTouched as $relationship) {
                $this->load_relationship($relationship);
                foreach ($this->$relationship->getBeans() as $bean) {
                    $bean->account_id = $this->account_id;
                    $bean->save();
                }
            }
        }

        parent::save_relationship_changes($is_update, $exclude);
    }
}
