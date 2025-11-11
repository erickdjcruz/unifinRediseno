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

trait PMSEEvalRelations
{
    /**
     * Method that evaluates the relation between two values with the given operator
     * @param string $value1 The left side value of the evaluation
     * @param string $operator This value should be one in either $arrayRelationsSig or $arrayRelationsLit
     * @param string $value2 The left side value of the evaluation
     * @param string $type The data type for the value
     * @param boolean $isUpdate Is this an update (versus new record) process
     * @return int
     */
    public function evalRelations($value1, $operator, $value2, $type = 'typeDefault', $isUpdate = false)
    {
        $arrayRelationsSig = [
            '==',
            '!=',
            '>=',
            '<=',
            '>',
            '<',
        ];
        $arrayRelationsLit = [
            'equals',
            'not_equals',
            'major_equals_than',
            'minor_equals_than',
            'major_than',
            'minor_than',
            'starts_with',
            'ends_with',
            'contains',
            'does_not_contain',
            'changes',
            'changes_from',
            'changes_to',
            'array_has_any',
            'array_has_none',
        ];

        // Set the result
        $result = 0;

        // Get the operator
        if (!in_array($operator, $arrayRelationsLit)) {
            $index = array_search($operator, $arrayRelationsSig);
            if ($index === false) {
                return $result;
            }
            $operator = $arrayRelationsLit[$index];
        }

        // Get proper values for the data we are working with
        if ($type === 'Relate' && is_array($value1)) {
            $value2 = is_array($value2) ? $value2 : explode(',', $value2);
        } else {
            $value1 = $this->isCastException($value1, $type) ? $value1 : $this->typeData($value1, $type);
            $value2 = $this->isCastException($value2, $type) ? $value2 : $this->typeData($value2, $type);
        }

        // Used for reporting back to the caller
        $this->condition .= ':(' . is_array($value1) ? encodeMultienumValue($value1) : $value1 . '):';

        // Handle evaluations...
        switch ($operator) {
            case 'equals':
                if (is_array($value1) && is_array($value2)) {
                    $result = array_diff($value1, $value2) === array_diff($value2, $value1);
                } else {
                    $result = $value1 == $value2;
                }
                break;
            case 'changes':
                // Changes should only evaluate to true for update processes
                $result = $value1 !== null && $isUpdate === true;
                break;
            case 'changes_from':
            case 'changes_to':
                // Changes to/from should only evaluate to true for update processes
                $result = $value1 !== null && $isUpdate === true && $value1 == $value2;
                break;
            case 'not_equals':
                if (is_array($value1) && is_array($value2)) {
                    $result = array_diff($value1, $value2) !== array_diff($value2, $value1);
                } else {
                    $result = $value1 != $value2;
                }
                break;
            case 'major_equals_than':
                $result = $value1 >= $value2;
                break;
            case 'minor_equals_than':
                $result = $value1 <= $value2;
                break;
            case 'major_than':
                $result = $value1 > $value2;
                break;
            case 'minor_than':
                $result = $value1 < $value2;
                break;
            case 'starts_with':
                $len2 = strlen($value2);
                $result = false;
                if (strlen($value1) >= $len2) {
                    $result = true;
                    for ($i = 0; $i < $len2; $i++) {
                        if ($value1[$i] != $value2[$i]) {
                            $result = false;
                            break;
                        }
                    }
                }
                break;
            case 'ends_with':
                $len1 = strlen($value1);
                $len2 = strlen($value2);
                $result = false;
                if ($len1 >= $len2) {
                    $result = true;
                    $len1 -= $len2;
                    for ($i = 0; $i < $len2; $i++) {
                        if ($value1[$len1 + $i] != $value2[$i]) {
                            $result = false;
                            break;
                        }
                    }
                }
                break;
            case 'contains':
                $result = strpos($value1, (string) $value2) !== false;
                break;
            case 'does_not_contain':
                $result = strpos($value1, (string) $value2) === false;
                break;
            case 'array_has_any':
                // getting a null value doesn't make sense
                if (is_null($value1) || is_null($value2)) {
                    break;
                }
                if (!is_array($value1) && is_string($value1)) {
                    $value1 = [$value1];
                }
                if (!is_array($value2) && is_string($value2)) {
                    $value2 = [$value2];
                }
                foreach ($value1 as $element) {
                    if (in_array($element, $value2)) {
                        $result = true;
                        break;
                    }
                }
                break;
            case 'array_has_none':
                // getting a null value doesn't make sense
                if (is_null($value1) || is_null($value2)) {
                    break;
                }
                if (!is_array($value1) && is_string($value1)) {
                    $value1 = [$value1];
                }
                if (!is_array($value2) && is_string($value2)) {
                    $value2 = [$value2];
                }
                $result = empty(array_intersect($value1, $value2));
                break;
            default:
        }

        return $this->typeData($result, 'int');
    }

    /**
     * Check if value is not subject to type casting
     *
     * @param string $value Value
     * @param string $type Casting type
     * @return bool
     */
    protected function isCastException($value, $type): bool
    {
        return $value === null ||
            ($value === '' && in_array(strtolower($type), ['int', 'integer']));
    }
}
