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
 * <b>getDropdownKeySet(String list_name)</b><br>
 * Returns a collection of the keys in the supplied dropdown list.<br/>
 * This list must be defined in the DropDown editor.<br/>
 * ex: <i>valueAt(2, getDropdownKeySet("my_list"))</i>
 */
class SugarDropDownExpression extends EnumExpression
{
    /**
     * Returns the entire enumeration bare.
     */
    public function evaluate()
    {
        global $app_list_strings;
        $dd = $this->getParameters()->evaluate();
        ;

        if (isset($app_list_strings[$dd]) && is_array($app_list_strings[$dd])) {
            return array_keys($app_list_strings[$dd]);
        }

        return [];
    }


    /**
     * Returns the JS Equivalent of the evaluate function.
     */
    public static function getJSEvaluate()
    {
        return <<<EOQ
			var dd = this.getParameters().evaluate(),
				arr, ret = [];
			if (App){
				arr = App.lang.getAppListStrings(dd);
			}
			else {
				arr = SUGAR.language.get('app_list_strings', dd);
			}
			if (arr && arr != "undefined")
			{
				for (var i in arr) {
					if (typeof i == "string")
						ret[ret.length] = i;
				}
			}
			return ret;
EOQ;
    }


    /**
     * Returns the exact number of parameters needed.
     */
    public static function getParamCount()
    {
        return 1;
    }

    /**
     * All parameters have to be a string.
     */
    public static function getParameterTypes()
    {
        return AbstractExpression::$STRING_TYPE;
    }

    /**
     * Returns the opreation name that this Expression should be
     * called by.
     */
    public static function getOperationName()
    {
        return ['getDropdownKeySet', 'getDD'];
    }

    /**
     * Returns the String representation of this Expression.
     */
    public function toString()
    {
    }
}
