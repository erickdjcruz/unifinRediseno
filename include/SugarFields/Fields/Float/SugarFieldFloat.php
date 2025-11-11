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


class SugarFieldFloat extends SugarFieldInt
{
    public function formatField($rawField, $vardef)
    {
        // A null precision uses the user prefs / system prefs by default
        $precision = null;
        if (isset($vardef['precision'])) {
            $precision = $vardef['precision'];
        }

        if ($rawField === '' || $rawField === null) {
            return '';
        }

        return format_number($rawField, $precision, $precision);
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
            ? (float)$bean->$fieldName : null;
    }

    public function unformatField($formattedField, $vardef)
    {
        if ($formattedField === '' || $formattedField === null) {
            return '';
        }
        if (is_array($formattedField)) {
            $formattedField = array_shift($formattedField);
        }
        return (float)unformat_number($formattedField);
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
        $dec_sep = $settings->dec_sep;
        if ($dec_sep != '.') {
            $value = str_replace($dec_sep, '.', $value);
        }
        if (!is_numeric($value)) {
            return false;
        }

        return $value;
    }

    /**
     * For Floats we need to round down to the precision of the passed in value, since the db's could be showing
     * something different
     *
     * {@inheritdoc}
     * @throws \SugarApiExceptionInvalidParameter
     */
    public function fixForFilter(
        &$value,
        $columnName,
        SugarBean $bean,
        SugarQuery $q,
        SugarQuery_Builder_Where $where,
        $op
    ) {

        $inputValues = is_array($value) ? array_values($value) : [$value];
        $separatorPosition = strlen(substr($inputValues[0], strpos($inputValues[0], '.') + 1));
        $format = $separatorPosition === 0 ? '%d' : "%.{$separatorPosition}F";
        $field = sprintf('ROUND(%s, %d)', $columnName, $separatorPosition);
        $literals = array_map(function ($inputValue) use ($format) {
            return sprintf($format, $inputValue);
        }, $inputValues);

        switch ($op) {
            case '$equals':
                $q->whereRaw("$field = {$literals[0]}");
                return false;
            case '$not_equals':
                $q->whereRaw("$field != {$literals[0]}");
                return false;
            case '$between':
                if (safeCount($literals) != 2) {
                    throw new SugarApiExceptionInvalidParameter('$between requires an array with two values');
                }
                $q->whereRaw("$field BETWEEN {$literals[0]} AND {$literals[1]}");
                return false;
            case '$lt':
                $q->whereRaw("$field < {$literals[0]}");
                return false;
            case '$lte':
                $q->whereRaw("$field <= {$literals[0]}");
                return false;
            case '$gt':
                $q->whereRaw("$field > {$literals[0]}");
                return false;
            case '$gte':
                $q->whereRaw("$field >= {$literals[0]}");
                return false;
        }

        return true;
    }

    /**
     * Currently not supported.
     * {@inheritDoc}
     */
    public function apiValidate(SugarBean $bean, array $params, $field, $properties)
    {
        return true;
    }

    /**
     * Currently not supported.
     * {@inheritDoc}
     */
    protected function getFieldRange($vardef)
    {
        return false;
    }
}
