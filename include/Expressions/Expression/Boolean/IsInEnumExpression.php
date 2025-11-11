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
 * <b>isInList(Generic item, List list)</b><br/>
 * Returns true if item is contained within the list. <br/>
 * <i>isInList(3, createList(2, 3, "red", "blue"))</i> = true
 */
class IsInEnumExpression extends BooleanExpression
{
    /**
     * Returns itself when evaluating.
     */
    public function evaluate()
    {
        $params = $this->getParameters();
        $haystack = $params[1]->evaluate();
        $needle = $params[0]->evaluate();

        if (is_iterable($haystack)) {
            foreach ($haystack as $value) {
                if ($value instanceof Expression) {
                    $value = $value->evaluate();
                }
                if ($value == $needle) {
                    return AbstractExpression::$TRUE;
                }
                if (is_array($value) && safeInArray($needle, $value)) {
                    return AbstractExpression::$TRUE;
                }
            }
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
			var haystack = params[1].evaluate();
			var needle   = params[0].evaluate();

			for ( var i = 0 ; i < haystack.length ; i++ ) {
				var value = haystack[i] instanceof SUGAR.expressions.Expression ? haystack[i].evaluate() : haystack[i];
				if ( value == needle ) {
					return SUGAR.expressions.Expression.TRUE;
				}
				if ( Array.isArray(value) && _.contains(value, needle) ) {
					return SUGAR.expressions.Expression.TRUE;
				}
			}

			return SUGAR.expressions.Expression.FALSE;
EOQ;
    }

    /**
     * Any generic type will suffice.
     */
    public static function getParameterTypes()
    {
        return ['generic', 'enum'];
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
        return ['isInList', 'isInEnum'];
    }

    /**
     * Returns the String representation of this Expression.
     */
    public function toString()
    {
    }
}
