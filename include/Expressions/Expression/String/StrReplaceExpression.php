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
 * <b>strReplace(String search, String replace, String subject, Boolean case-sensitive)</b><br/>
 * Replaces occurrences of <i>search</i> within a <i>subject</i> string with the value specified by<i>replace</i>.<br/>
 * Include true as fourth argument to make the search case-sensitive.<br/>
 * ex: <em>strReplace("Hello", "Hi", "Hello World hello", false)</em> = "Hi World Hi"<br/>
 * <em>strReplace("Hello", "Hi", "Hello World hello", true)</em> = "Hi World hello"
 */
class StrReplaceExpression extends StringExpression
{
    /**
     * Returns itself when evaluating.
     */
    public function evaluate()
    {
        $params = $this->getParameters();
        $search = $params[0]->evaluate();
        $replace = $params[1]->evaluate();
        $subject = $params[2]->evaluate();
        $caseSensitivity = $params[3]->evaluate();

        if ($caseSensitivity === AbstractExpression::$TRUE) {
            return str_replace($search, $replace, $subject);
        } else {
            return str_ireplace($search, $replace, $subject);
        }
    }

    /**
     * Returns the JS Equivalent of the evaluate function.
     */
    public static function getJSEvaluate()
    {
        return <<<EOQ
            var params = this.getParameters();
            var search = params[0].evaluate();
            var replace = params[1].evaluate();
            var subject = params[2].evaluate();
            var caseSensitivity = params[3].evaluate();
            
            // go through the search and try to regex escape everything
            search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\\\$&');
            
            var flags = 'g';
            if (caseSensitivity === SUGAR.expressions.Expression.FALSE) {
                flags += 'i';
            }
            var regex = new RegExp(search, flags);
            return subject.replace(regex, replace);
			
EOQ;
    }

    /**
     * Returns the operation name that this Expression should be
     * called by.
     */
    public static function getOperationName()
    {
        return 'strReplace';
    }

    /**
     * @inheritdoc
     */
    public static function getParameterTypes()
    {
        return ['string', 'string', 'string', 'boolean'];
    }

    /**
     * Returns the String representation of this Expression.
     */
    public function toString()
    {
    }
}
