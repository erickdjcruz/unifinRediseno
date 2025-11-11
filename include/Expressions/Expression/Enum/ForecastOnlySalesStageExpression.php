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
 * <b>forecastOnlySalesStages(Boolean $includeWon, Boolean $includeLost, Boolean $includeEverythingElse)</b><br/>
 * Returns nothing by default and only includes the Sales Stages that are asked for from the sales_stage_dom.
 * If you pass in `true` for
 *      $includeWon - Includes any Sales Stages that are included in the "sales_stage_won" set
 *      $includeLost - Includes any Sales Stages that are included in the "sales_stage_lost" set
 *      $includeEverythingElse - Includes any Sales Stages not in "sales_stage_won" or "sales_stage_lost" sets
 * If you pass in `false` for any param, it will exclude those options from the returned set
 * <br/>
 * ex: <i>forecastOnlySalesStages(true, false, false)</i> returns only ["Closed Won"]
 */
class ForecastOnlySalesStageExpression extends EnumExpression
{
    /**
     * Returns only the requested values from sales_stage_dom
     */
    public function evaluate()
    {
        $params = $this->getParameters();
        $includeWon = $params[0]->evaluate();
        $includeLost = $params[1]->evaluate();
        $includeEverythingElse = $params[2]->evaluate();

        $array = array_keys($GLOBALS['app_list_strings']['sales_stage_dom']);

        // get the statuses
        $settings = Forecast::getSettings();

        $keysToInclude = [];
        if ($includeWon == AbstractExpression::$TRUE) {
            $keysToInclude = array_merge($keysToInclude, $settings['sales_stage_won']);
        }

        if ($includeLost == AbstractExpression::$TRUE) {
            $keysToInclude = array_merge($keysToInclude, $settings['sales_stage_lost']);
        }

        if ($includeEverythingElse == AbstractExpression::$TRUE) {
            $closedStages = array_merge($settings['sales_stage_won'], $settings['sales_stage_lost']);
            $keysToInclude = array_merge($keysToInclude, array_diff($array, $closedStages));
        }

        return $keysToInclude;
    }

    /**
     * Returns the JS Equivalent of the evaluate function.
     */
    public static function getJSEvaluate()
    {
        return <<<JS

            // this doesn't support BWC modules, so it should return the full list of dom elememnts
            if (App === undefined) {
                return SUGAR.language.get('app_list_strings', 'sales_stage_dom');
            }

            var SEE = SUGAR.expressions.Expression;
            var config = App.metadata.getModule('Forecasts', 'config');
            var params = this.getParameters();
            var includeWon = params[0].evaluate();
            var includeClosed = params[1].evaluate();
            var includeEverythingElse = params[2].evaluate();
            var array = _.keys(App.lang.getAppListStrings('sales_stage_dom'));
            var keysToInclude = [];

            if (SEE.isTruthy(includeWon)) {
                keysToInclude = _.union(keysToInclude, config.sales_stage_won);
            }

            if (SEE.isTruthy(includeClosed)) {
                keysToInclude = _.union(keysToInclude, config.sales_stage_lost);
            }

            if (SEE.isTruthy(includeEverythingElse)) {
                var nonClosedKeys = _.difference(array, config.sales_stage_won, config.sales_stage_lost)
                keysToInclude = _.union(keysToInclude, nonClosedKeys);
            }

            return keysToInclude;
JS;
    }

    public static function getParamCount()
    {
        return 3;
    }


    /**
     * The first parameter is a number and the second is the list.
     */
    public static function getParameterTypes()
    {
        return [
            AbstractExpression::$BOOLEAN_TYPE,
            AbstractExpression::$BOOLEAN_TYPE,
            AbstractExpression::$BOOLEAN_TYPE,
        ];
    }

    /**
     * Returns the operation name that this Expression could be called by.
     */
    public static function getOperationName()
    {
        return ['forecastOnlySalesStages'];
    }

    /**
     * Returns the String representation of this Expression.
     */
    public function toString()
    {
    }
}
