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
 * <b>currentUserField(String <i>field</i>)</b><br>
 * Returns the value of the User field <i>field</i> for the currently viewing User<br/>
 * ex: <i>currentUserField("first_name")</i>
 */
class CurrentUserFieldExpression extends GenericExpression
{
    /**
     * Evaluate the expression
     */
    public function evaluate()
    {
        global $current_user;
        $userFieldName = $this->getParameters()->evaluate();

        $validFields = FormulaHelper::getValidUserFields($current_user->getFieldDefinitions());
        $validFieldNames = array_column($validFields, 'name');

        if (in_array($userFieldName, $validFieldNames)) {
            $fieldDef = $current_user->getFieldDefinition($userFieldName);
            return $this->formatField($current_user, $userFieldName, $fieldDef);
        } else {
            throw new Exception('currentUserField: Parameter "' . $userFieldName . '" is not a valid User field');
        }
    }

    /**
     * Formats the value of the field for certain types
     * @param $currentUser
     * @param $userFieldName
     * @param $fieldDef
     * @return mixed
     */
    private function formatField($currentUser, $userFieldName, $fieldDef)
    {
        global $timedate;

        if (empty($fieldDef['type'])) {
            return $currentUser->$userFieldName;
        }

        $value = $currentUser->$userFieldName;
        switch ($fieldDef['type']) {
            case 'date':
                if (!empty($value)) {
                    $value = $timedate->fromDbDate($currentUser->$userFieldName);
                    if (!$value) {
                        $value = $timedate->fromUserDate($currentUser->$userFieldName);
                    }
                    if ($value) {
                        $value->isDate = true;
                        $value->def = $fieldDef;
                    }
                }
                break;
            case 'datetime':
            case 'datetimecombo':
                if (!empty($value)) {
                    $value = $timedate->fromDb($currentUser->$userFieldName);
                    if (!$value) {
                        $value = $timedate->fromUser($currentUser->$userFieldName);
                    }
                    if ($value) {
                        $value->def = $fieldDef;
                    }
                }
                break;
            case 'bool':
                $value = $value ? BooleanExpression::$TRUE : BooleanExpression::$FALSE;
                break;
            case 'currency':
                if (!isset($this->context)) {
                    $this->setContext();
                }
                if (isset($this->context->base_rate) &&
                    isset($currentUser->base_rate) &&
                    $this->context->base_rate !== $currentUser->base_rate
                ) {
                    $value = SugarCurrency::convertWithRate($value, $currentUser->base_rate, $this->context->base_rate);
                }
                break;
        }

        return $value;
    }

    /**
     * Returns the JS equivalent of the evaluate function
     */
    public static function getJSEvaluate()
    {
        return <<<EOQ
            // We can't do this on BWC views since we don't have the user data available
            if (!App) {
                return SUGAR.expressions.Expression.FALSE;
            }
            
            let formatField = function(context, fieldDef, userFieldName) {
                let value = App.user.get('sugar_logic_fields')[userFieldName];
                switch (fieldDef.type) {
                    case 'date':
                    case 'datetime':
                    case 'datetimecombo':
                        let dateOnly = fieldDef.type === 'date';
                        if (!value) {
                            return '';
                        }
                        return App.date(value).formatUser(dateOnly, App.user);
                    case 'bool':
                        if (value) {
                            return SUGAR.expressions.Expression.TRUE;
                        } else {
                            return SUGAR.expressions.Expression.FALSE;
                        }
                    case 'currency':
                        let contextCurrencyId = context.model.get('currency_id');
                        if (contextCurrencyId) {
                            return App.currency.convertFromBase(value, contextCurrencyId);
                        } else {
                            return value;
                        }
                    default:
                        return value;
                }
            };
            
            let getFieldDef = function(userFieldName) {
                let fieldDefs = App.user.get('sugar_logic_fielddefs');
                return fieldDefs.find(field => field.name === userFieldName);
            };
            
            let userFieldName = this.getParameters().evaluate();
            let fieldDef = getFieldDef(userFieldName);
            
            if (fieldDef) {
                return formatField(this.context, fieldDef, userFieldName);
            } else {
                throw 'currentUserField: Parameter "' + userFieldName + '" is not a valid User field';
            }
EOQ;
    }

    /**
     * Returns the name of this expression
     */
    public static function getOperationName()
    {
        return 'currentUserField';
    }

    /**
     * This expression takes one string as a parameter, representing the name of the User field
     */
    public static function getParameterTypes()
    {
        return [AbstractExpression::$STRING_TYPE];
    }

    /**
     * Returns the maximum number of parameters needed
     */
    public static function getParamCount()
    {
        return 1;
    }

    /**
     * Returns the String representation of this Expression.
     */
    public function toString()
    {
    }
}
