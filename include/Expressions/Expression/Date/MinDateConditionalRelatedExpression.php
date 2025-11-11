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
 * <b>rollupConditionalMinDate(Relate <i>link</i>, String <i>field</i>, List <i>conditionalFields</i>,
 * List <i>conditionalValues</i>)</b><br>
 * Returns the minimum date value among <i>field</i> in records related by <i>link</i>. Records can
 * be filtered to satisfy certain field value conditions. <i>conditionalFields</i> is a list of
 * field names of the related module to filter on (or an empty list if no filtering desired).
 * <i>conditionalValues</i> is a list of values corresponding to each field in <i>conditionalFields</i>
 * (in order) which the field must contain. The number of entries in the <i>conditionalValues</i> list
 * must equal the number of entries in the <i>conditionalFields</i> list. Each value in <i>conditionalValues</i>
 * can be a list itself, to filter on multiple values for a single field.
 *
 * ex: <i>rollupConditionalMinDate($revenuelineitems,"date_closed",createList("product_type","renewable"),createList("Existing Business",,"1"))</i> in Accounts would return the <br/>
 * earliest <i>date_closed</i> field across all related RevenueLineItems where <i>product_type</i> is equal to <i>Existing Business</i>
 * and <i>renewable</i> is equal to 1.
 */
class MinDateConditionalRelatedExpression extends DateExpression
{
    /**
     * @return int|string
     * @deprecated
     */
    public function evaluate()
    {
        LoggerManager::getLogger()->deprecated('The rollupConditionalMinDate SugarLogic function has been ' .
            'deprecated since 10.0.0 and will be removed in a later release');

        // Parse the relationship arguments
        $params = $this->getParameters();
        $relatedBeans = $params[0]->evaluate();
        $relField = $params[1]->evaluate();

        // If there are no beans related via the link field, return the empty string
        if (!is_array($relatedBeans) || empty($relatedBeans)) {
            return '';
        }

        // Parse the condition arguments
        $conditionFields = $params[2]->evaluate();
        $conditionValues = $params[3]->evaluate();
        $conditions = [];
        foreach ($conditionFields as $index => $conditionField) {
            if (is_array($conditionValues[$index])) {
                $conditions[$conditionField] = $conditionValues[$index];
            } else {
                $conditions[$conditionField] = [$conditionValues[$index]];
            }
        }

        return $this->getMinRelatedDate($relatedBeans, $relField, $conditions);
    }

    /**
     * Returns the JS Equivalent of the evaluate function.
     */
    public static function getJSEvaluate()
    {
        return <<<JS

        if (App === undefined) {
            console.log('The rollupConditionalMinDate SugarLogic function has been deprecated since 10.0.0 ' +
            'and will be removed in a later release');
            return SUGAR.expressions.Expression.FALSE;
        }
        App.logger.warn('The rollupConditionalMinDate SugarLogic function has been deprecated since 10.0.0 ' +
            'and will be removed in a later release');
        
        // Parse the arguments
        var params = this.params;
        var target = this.context.target;
        var relationship = params[0].evaluate();
        var rel_field = params[1].evaluate();
        
        // Get information about the model
        var model = this.context.relatedModel || App.data.createRelatedBean(this.context.model, null, relationship);
        var hasModelBeenRemoved = this.context.isRemoveEvent || false;
        
        // Get the values of the field in the related beans
        if (!_.isUndefined(this.context.relatedModel)) {
            this.context.updateRelatedCollectionValues(
                this.context.model,
                relationship,
                'rollupConditionalMinDate',
                rel_field,
                model,
                (hasModelBeenRemoved)? 'remove' : 'add'
            );
        }
        var all_values = this.context.getRelatedCollectionValues(this.context.model,
            relationship, 'rollupConditionalMinDate', rel_field) || {};
        
        // Parse the related values as dates, sort in ascending order, and take
        // the first one
        var rollupValue = '';
        var dates = [];
        _.each(all_values, function(_date) {
            dates.push(new App.date(_date));
        });
        var sortByDateAsc = function (lhs, rhs) { return lhs > rhs ? 1 : lhs < rhs ? -1 : 0; };
        rollupValue = dates.sort(sortByDateAsc)[0];
        
        // Convert the minimum value found into the correct format for this field (date or timestamp)
        if (this.context.getField(target).type === 'date') {
            rollupValue = rollupValue.format('YYYY-MM-DD');
        } else {
            rollupValue = rollupValue.valueOf();
        }

        // If the rollup value is different from the current value, set the
        // rollup value as the new field value. Otherwise, just return the old value
        var currentValue = this.context.model.get(target);
        if (!_.isEqual(rollupValue, currentValue)) {
            this.context.model.set(target, rollupValue);
            this.context.updateRelatedFieldValue(
                relationship,
                'rollupConditionalMinDate',
                rel_field,
                rollupValue,
                this.context.model.isNew()
            );
            return rollupValue;
        } else {
            return currentValue;
        }
JS;
    }

    /**
     * Finds the minimum date field value among all beans related by the specified
     * link field that satisfy the specified conditions
     *
     * @param array $relatedBeans the list of beans related through the specified link
     * @param string $relField the field to find the minimum of in related records
     * @param array $conditions the list of fields -> values the related beans must satisfy
     * @return int|string the minimum date value in the original format of
     *          the given field (timestamp int or date string)
     */
    protected function getMinRelatedDate($relatedBeans, $relField, $conditions)
    {
        // Iterate through the related beans, keeping track of the minimum date
        // and date type (timestamp or not)
        $minDate = 0;
        $isTimestamp = true;
        foreach ($relatedBeans as $bean) {
            // If the bean does not satisfy the conditions, skip it
            if (!$this->checkConditions($bean, $conditions)) {
                continue;
            }

            // Get the value of the date field in the bean and format it as a
            // timestamp for easy comparison
            if (!empty($bean->$relField)) {
                if (is_int($bean->$relField)) {
                    $value = $bean->relfield;
                } else {
                    $isTimestamp = false;

                    // Try to parse the date value from DB format and convert to
                    // timestamp
                    $dateObject = SugarDateTime::createFromFormat(TimeDate::DB_DATE_FORMAT, $bean->$relField);
                    if (empty($dateObject)) {
                        // Unable to parse date, so skip this value
                        continue;
                    }
                    $value = $dateObject->getTimestamp();
                }
            } else {
                continue;
            }

            // Compare the value to the current minimum date found
            if ($minDate === 0 || $value < $minDate) {
                $minDate = $value;
            }
        }

        // Return the result in proper format for the field
        if ($minDate === 0) {
            return '';
        } elseif ($isTimestamp) {
            return $minDate;
        } else {
            $date = new DateTime();
            return $date->setTimestamp($minDate)->format(TimeDate::DB_DATE_FORMAT);
        }
    }

    /**
     * Checks whether a bean satisfies the specified conditions of the expression
     *
     * @param SugarBean $bean the bean to check against
     * @param array $conditions the list of fields -> values the bean must satisfy
     * @return bool true if the bean satisfies the conditions; false otherwise
     */
    protected function checkConditions(SugarBean $bean, array $conditions)
    {
        foreach ($conditions as $field => $values) {
            if (!safeInArray($bean->$field, $values)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns the operation name that this Expression should be
     * called by.
     */
    public static function getOperationName()
    {
        return ['rollupConditionalMinDate'];
    }

    /**
     * The first parameter should be a relationship link
     * The second parameter should be a field name string
     * The third parameter should be a list (enum) of fields
     * The fourth parameter should be a list (enum) of field values
     */
    public static function getParameterTypes()
    {
        return [
            AbstractExpression::$RELATE_TYPE,
            AbstractExpression::$STRING_TYPE,
            AbstractExpression::$ENUM_TYPE,
            AbstractExpression::$ENUM_TYPE,
        ];
    }

    /**
     * Returns the maximum number of parameters needed.
     */
    public static function getParamCount()
    {
        return 4;
    }
}
