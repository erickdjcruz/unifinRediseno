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
 * <b>monthofyear(Date d)</b><br>
 * Returns the month of year that <i>d</i> is in.<br/>
 * Jan = 1, Feb = 2, ... , Dec = 12
 */
class MonthOfYearExpression extends NumericExpression
{
    /**
     * Returns the current month
     */
    public function evaluate()
    {
        $params = DateExpression::parse($this->getParameters()->evaluate());
        if (!$params) {
            return false;
        }
        return $params->month;
    }


    /**
     * Returns the JS Equivalent of the evaluate function.
     */
    public static function getJSEvaluate()
    {
        return <<<EOQ
            var time = this.getParameters().evaluate();
            if (_.isString(time) && _.isEmpty(time)) {
                return '';
            }
            return new Date(time).getMonth() + 1;
EOQ;
    }

    /**
     * Returns the operation name that this Expression should be
     * called by.
     */
    public static function getOperationName()
    {
        return 'monthofyear';
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
