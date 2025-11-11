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
 * <b>sentimentScoreToStr(Number score)</b><br/>
 * Returns <i>score</i> converted to its string representation.<br/>
 * ex: <em>sentimentScoreToStr(2.0)</em> = "Positive"
 */
class SentimentScoreToStringExpression extends NumericExpression
{
    /**
     * Returns the score value converted to it string representation
     */
    public function evaluate()
    {
        $score = $this->getParameters()->evaluate();

        if ($score > 1.3) {
            return 'Positive';
        } elseif ($score < -1.3) {
            return 'Negative';
        } else {
            return 'Neutral';
        }
    }

    /**
     * Returns the JS Equivalent of the evaluate function.
     */
    public static function getJSEvaluate()
    {
        return <<<EOQ
			var score = this.getParameters().evaluate();
			if (score > 1.3) {
                return 'Positive';
            } else if (score < -1.3) {
                return 'Negative'
            } else {
                return 'Neutral'
            }
EOQ;
    }

    /**
     * Returns the opreation name that this Expression should be
     * called by.
     */
    public static function getOperationName()
    {
        return 'sentimentScoreToStr';
    }

    /**
     * Return param count to prevent errors.
     */
    public static function getParamCount()
    {
        return 1;
    }
}
