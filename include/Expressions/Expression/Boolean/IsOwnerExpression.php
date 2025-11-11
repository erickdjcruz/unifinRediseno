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
 * <b>isOwner()</b><br>
 * Returns true if current record is assigned to current user
 */
class IsOwnerExpression extends BooleanExpression
{
    /**
     * Evaluate the expression
     */
    public function evaluate()
    {
        global $current_user;
        if (!isset($this->context)) {
            $this->setContext();
        }
        if (!empty($this->context->assigned_user_id) &&
            $this->context->assigned_user_id === $current_user->id) {
            return AbstractExpression::$TRUE;
        }

        return AbstractExpression::$FALSE;
    }

    /**
     * Returns the JS Equivalent of the evaluate function.
     */
    public static function getJSEvaluate()
    {
        return <<<JS
            // this doesn't support BWC modules
            if (App === undefined) {
                return SUGAR.expressions.Expression.FALSE;
            }

            if (this.context.model &&
                this.context.model.get('assigned_user_id') === App.user.id) {
                return SUGAR.expressions.Expression.TRUE
            }

            return SUGAR.expressions.Expression.FALSE;
JS;
    }

    /**
     * Returns the maximum number of parameters needed.
     */
    public static function getParamCount()
    {
        return 0;
    }

    /**
     * Returns the operation name that this Expression should be
     * called by.
     */
    public static function getOperationName()
    {
        return 'isOwner';
    }

    /**
     * Returns the String representation of this Expression.
     */
    public function toString()
    {
    }
}
