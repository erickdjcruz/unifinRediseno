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

class SugarFieldInt extends SugarFieldBase
{
    public function formatField($rawField, $vardef)
    {
        if (!empty($vardef['disable_num_format'])) {
            return $rawField;
        }
        if ($rawField === '' || $rawField === null) {
            return '';
        }

        return format_number($rawField, 0, 0);
    }

    /**
     * {@inheritDoc}
     */
    public function apiFormatField(
        array       &$data,
        SugarBean   $bean,
        array       $args,
        $fieldName,
        $properties,
        array       $fieldList = null,
        ServiceBase $service = null
    ) {

        $this->ensureApiFormatFieldArguments($fieldList, $service);

        $data[$fieldName] = isset($bean->$fieldName) && is_numeric($bean->$fieldName)
            ? (int)$bean->$fieldName : null;
    }

    public function unformatField($formattedField, $vardef)
    {
        if ($formattedField === '' || $formattedField === null) {
            return '';
        }
        return (int)unformat_number($formattedField);
    }

    /**
     * getSearchWhereValue
     *
     * Checks and returns a sane value based on the field type that can be used when building the where clause in a
     * search form.
     *
     * @param $value Mixed value being searched on
     * @return Int the value for the where clause used in search
     */
    public function getSearchWhereValue($value)
    {
        $newVal = parent::getSearchWhereValue($value);
        if (!is_numeric($newVal)) {
            if (strpos($newVal, ',') > 0) {
                $multiVals = explode(',', $newVal);
                $newVal = '';
                foreach ($multiVals as $key => $val) {
                    if (!empty($newVal)) {
                        $newVal .= ',';
                    }
                    if (!empty($val) && !(is_numeric($val))) {
                        $newVal .= -1;
                    } else {
                        $newVal .= $val;
                    }
                }
                return $newVal;
            } else {
                return -1;
            }
        }
        return $newVal;
    }

    public function unformatSearchRequest(&$inputData, &$field)
    {
        $field['value'] = $this->unformatField($field['value'], $field);
    }

    public function getSearchViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex)
    {
        // Use the basic field type for searches, no need to format/unformat everything... for now
        $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
        if ($this->isRangeSearchView($vardef)) {
            $id = $displayParams['idName'] ?? $vardef['name'];
            $this->ss->assign('original_id', "{$id}");
            $this->ss->assign('id_range', "range_{$id}");
            $this->ss->assign('id_range_start', "start_range_{$id}");
            $this->ss->assign('id_range_end', "end_range_{$id}");
            $this->ss->assign('id_range_choice', "{$id}_range_choice");
            return $this->fetch('include/SugarFields/Fields/Int/RangeSearchForm.tpl');
        }

        return $this->fetch($this->findTemplate('SearchForm'));
    }

    /**
     * @see SugarFieldBase::importSanitize()
     */
    public function importSanitize(
        $value,
        $vardef,
        $focus,
        ImportFieldSanitize $settings
    ) {


        $value = str_replace((string)$settings->num_grp_sep, '', (string)$value);
        if (!is_numeric($value) || strstr($value, '.')) {
            return false;
        }

        // check range
        $fieldRange = $this->getFieldRange($vardef);

        if (!empty($fieldRange) && ($value > $fieldRange['max_value'] || $value < $fieldRange['min_value'])) {
            return false;
        }

        return $value;
    }

    /**
     * Validates submitted data
     * @param SugarBean $bean
     * @param array $params
     * @param string $field
     * @param array $properties
     * @return boolean
     */
    public function apiValidate(SugarBean $bean, array $params, $field, $properties)
    {
        if (isset($params[$field])) {
            $fieldRange = $this->getFieldRange($properties);
            $min_value = $fieldRange['min_value'];
            $max_value = $fieldRange['max_value'];
            // If PHP encounters a number beyond the bounds of the int type, it will be interpreted as a float instead
            // https://www.php.net/manual/en/language.types.integer.php#language.types.integer.overflow
            // In order to ensure consistency on a 32 bit system we convert all values to float and then compare
            return ((!is_numeric($min_value) || floatval($params[$field]) >= floatval($min_value)) &&
                (!is_numeric($max_value) || floatval($params[$field]) <= floatval($max_value)));
        }

        return parent::apiValidate($bean, $params, $field, $properties);
    }

    /**
     * Gets field range based on db, vardef, and configs.
     * @param array $vardef
     * @return array | boolean
     */
    protected function getFieldRange($vardef)
    {
        // config
        $minValue = SugarConfig::getInstance()->get('sugar_min_int');
        $maxValue = SugarConfig::getInstance()->get('sugar_max_int');
        // db
        $fieldRange = $GLOBALS['db']->getFieldRange($vardef);
        if (!empty($fieldRange)) {
            $minValue = !is_numeric($minValue) ? $fieldRange['min_value'] : max($minValue, $fieldRange['min_value']);
            $maxValue = !is_numeric($maxValue) ? $fieldRange['max_value'] : min($maxValue, $fieldRange['max_value']);
        }
        // vardef
        if (isset($vardef['min']) && is_int($vardef['min'])) {
            $minValue = !is_numeric($minValue) ? $vardef['min'] : max($minValue, $vardef['min']);
        }
        if (isset($vardef['max']) && is_int($vardef['max'])) {
            $maxValue = !is_numeric($maxValue) ? $vardef['max'] : min($maxValue, $vardef['max']);
        }
        return ['min_value' => $minValue, 'max_value' => $maxValue];
    }
}
