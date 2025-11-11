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
 * Expression trigger
 * @api
 */
class Trigger
{
    public $triggerFields = [];
    public $conditionFunction = '';
    public static $ValueNotSetError = -1;

    public function __construct($condition, $fields = [])
    {
        $this->conditionFunction = $condition;
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        $this->triggerFields = $fields;
    }

    public function evaluate($target)
    {
        $result = Parser::evaluate($this->conditionFunction, $target)->evaluate();
        if ($result == AbstractExpression::$TRUE) {
            return true;
        } else {
            return false;
        }
    }

    public function getJavascript()
    {
        $js = 'new SUGAR.forms.Trigger([';
        for ($i = 0; $i < sizeOf($this->triggerFields); $i++) {
            $js .= "'{$this->triggerFields[$i]}'";
            if ($i < sizeOf($this->triggerFields) - 1) {
                $js .= ',';
            }
        }
        $js .= "], '" . str_replace("\n", '', $this->conditionFunction) . "')";
        return $js;
    }

    public function getCondition()
    {
        return $this->conditionFunction;
    }

    public function getFields()
    {
        return $this->triggerFields;
    }
}
