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
 * <b>isValidPhone(String phone)</b><br/>
 * Returns true if <i>phone</i> is in a valid phone format.
 */
class IsValidPhoneExpression extends BooleanExpression
{
    /**
     * Returns itself when evaluating.
     */
    public function evaluate()
    {
        $phoneStr = $this->getParameters()->evaluate();

        if (strlen($phoneStr) == 0) {
            return AbstractExpression::$TRUE;
        }
        if (!preg_match('/^\+?[0-9\-\(\)\s]+$/', $phoneStr)) {
            return AbstractExpression::$FALSE;
        }
        return AbstractExpression::$TRUE;
    }

    /**
     * Returns the JS Equivalent of the evaluate function.
     */
    public static function getJSEvaluate()
    {
        return <<<EOQ
		var phoneStr = this.getParameters().evaluate();
		if(phoneStr.length== 0) 	return SUGAR.expressions.Expression.TRUE;
		if( ! /^\+?[0-9\-\(\)\s]+$/.test(phoneStr) )
			return SUGAR.expressions.Expression.FALSE;
		return SUGAR.expressions.Expression.TRUE;
EOQ;
    }

    /**
     * Any generic type will suffice.
     */
    public static function getParameterTypes()
    {
        return ['string'];
    }

    /**
     * Returns the maximum number of parameters needed.
     */
    public static function getParamCount()
    {
        return 1;
    }

    /**
     * Returns the opreation name that this Expression should be
     * called by.
     */
    public static function getOperationName()
    {
        return 'isValidPhone';
    }

    /**
     * Returns the String representation of this Expression.
     */
    public function toString()
    {
    }
}
