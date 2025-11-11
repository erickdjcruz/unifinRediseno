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
 * <b>isAfter(Date day1, Date day2)</b><br>
 * Returns true day1 is after day2.<br/>
 * ex: <i>isBefore(date("1/1/2001"), date("2/2/2002"))</i> = false
 */
class isAfterExpression extends BooleanExpression
{
    /**
     * Returns itself when evaluating.
     */
    public function evaluate()
    {
        $params = $this->getParameters();

        $a = DateExpression::parse($params[0]->evaluate());
        $b = DateExpression::parse($params[1]->evaluate());

        if (empty($a) || empty($b)) {
            return false;
        }

        if ($a > $b) {
            return AbstractExpression::$TRUE;
        }
        return AbstractExpression::$FALSE;
    }

    /**
     * Returns the JS Equivalent of the evaluate function.
     */
    public static function getJSEvaluate()
    {
        return <<<EOQ
			var params = this.getParameters();
			var a = SUGAR.util.DateUtils.parse(params[0].evaluate());
			var b = SUGAR.util.DateUtils.parse(params[1].evaluate());

            if (!a || !b || isNaN(a) || isNaN(b)) {
                return SUGAR.expressions.Expression.FALSE;
            }

			if ( a > b )	return SUGAR.expressions.Expression.TRUE;
			return SUGAR.expressions.Expression.FALSE;
EOQ;
    }

    /**
     * Any generic type will suffice.
     */
    public static function getParameterTypes()
    {
        return ['date', 'date'];
    }

    /**
     * Returns the maximum number of parameters needed.
     */
    public static function getParamCount()
    {
        return 2;
    }

    /**
     * Returns the opreation name that this Expression should be
     * called by.
     */
    public static function getOperationName()
    {
        return 'isAfter';
    }

    /**
     * Returns the String representation of this Expression.
     */
    public function toString()
    {
    }
}
