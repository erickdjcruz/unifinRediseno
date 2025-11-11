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

class SugarWidgetFieldDiscountAmount extends SugarWidgetFieldCurrency
{
    public function querySelect(&$layout_def)
    {
        $query = parent::querySelect($layout_def);

        $bean = BeanFactory::newBean('Products');
        $discountSelectField = $bean->getFieldDefinition('discount_select');
        if (!empty($discountSelectField)) {
            $table = $layout_def['table_alias'];
            $query = "$table.discount_select, " . $query;
        }

        return $query;
    }

    /**
     *   Returns the discount amount in the appropriate format.
     *   If DISCOUNT_SELECT is 0, then it should be formatted as currency.
     *   If DISCOUNT_SELECT is 1, then it should be formatted as a percent value.
     */
    public function displayListPlain($layout_def)
    {

        if (empty($layout_def['fields']['DISCOUNT_SELECT'])) {
            $displayList = parent::displayListPlain($layout_def);
        } else {
            if (isset($layout_def['varname'])) {
                $key = strtoupper($layout_def['varname']);
            } else {
                $key = strtoupper($this->_get_column_alias($layout_def));
            }
            if (isset($layout_def['fields'][$key])) {
                // If it is 0, drop the trailing decimal places and leave off the %.
                if ((float)$layout_def['fields'][$key] == 0) {
                    $output = number_format($layout_def['fields'][$key], 0, '.', ',');
                } else {
                    $output = number_format($layout_def['fields'][$key], 2, '.', ',') . '%';
                }
                return $output;
            }
            return '';
        }
        return $displayList;
    }

    /**
     * Get discount amount value for sidecar field
     *
     * @param array $layoutDef
     *
     * @return mixed
     */
    public function getFieldControllerData(array $layoutDef)
    {
        $value = $this->displayListPlain($layoutDef);

        return $value;
    }
}
