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
 * <b>dayofmonth(Date d)</b><br>
 * Returns the day portion of <i>d</i> in DD format.
 */
class DayOfMonthExpression extends NumericExpression
{
    /**
     * Returns the day of month of the provided date
     */
    public function evaluate()
    {
        $params = DateExpression::parse($this->getParameters()->evaluate());
        if (!$params) {
            return false;
        }
        return $params->format('j');
    }


    /**
     * Returns the JS equivalent of the evaluate function.
     */
    public static function getJSEvaluate()
    {
        return <<<EOQ
            var time = this.getParameters().evaluate();
            if (_.isString(time) && _.isEmpty(time)) {
                return '';
            }
            return new Date(time).getDate();
EOQ;
    }

    /**
     * Returns the operation name that this Expression should be
     * called by.
     */
    public static function getOperationName()
    {
        return 'dayofmonth';
    }

    /**
     * Returns the maximum number of parameters needed.
     */
    public static function getParamCount()
    {
        return 1;
    }

    /**
     * All parameters have to be a date.
     */
    public static function getParameterTypes()
    {
        return [AbstractExpression::$DATE_TYPE];
    }

    /**
     * Returns the String representation of this Expression.
     */
    public function toString()
    {
    }
}
