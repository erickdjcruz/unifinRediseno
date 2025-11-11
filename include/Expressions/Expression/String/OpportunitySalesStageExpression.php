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
 * <b>opportunitySalesStage(String $linkName, String $field)</b><br>
 * Return the correct sales_stage based on the sales_stage set on all RevenueLineItems
 */
class OpportunitySalesStageExpression extends StringExpression
{
    /**
     * Based on the sales_stage of all RLIs, return the correct sales_stage for the Opp
     *
     * @return String The value of the sales_stage, If no stage is found, return an empty string
     */
    public function evaluate()
    {
        global $app_list_strings;

        $params = $this->getParameters();
        $linkField = $params[0]->evaluate();
        $relfield = $params[1]->evaluate();
        $forecastConfig = Forecast::getSettings();
        $closedWonStatus = ['Closed Won'];
        $closedLostStatus = ['Closed Lost'];

        if (!is_array($linkField) || empty($linkField)) {
            return '';
        }

        if ($forecastConfig['is_setup']) {
            $closedWonStatus = $forecastConfig['sales_stage_won'];
            $closedLostStatus = $forecastConfig['sales_stage_lost'];
        }

        $ret = '';
        $totalCt = safeCount($linkField);
        $closedLostCt = 0;
        $closedWonCt = 0;
        $latestSalesStageIndex = 0;
        $salesStageStageOptions = $app_list_strings['sales_stage_dom'];

        foreach ($linkField as $bean) {
            if (!empty($bean->$relfield)) {
                $value = $bean->$relfield;

                if (in_array($value, $closedWonStatus)) {
                    $closedWonCt++;
                } elseif (in_array($value, $closedLostStatus)) {
                    $closedLostCt++;
                } else {
                    $nextIndex = array_search($value, array_keys($salesStageStageOptions));
                    if ($nextIndex >= $latestSalesStageIndex) {
                        $latestSalesStageIndex = $nextIndex;
                        $ret = $value;
                    }
                }
            }
        }

        if ($closedLostCt === $totalCt) {
            return $closedLostStatus[0];
        } elseif ($closedLostCt + $closedWonCt === $totalCt) {
            return $closedWonStatus[0];
        } else {
            return $ret;
        }
    }

    /**
     * This evaluation of the expression in JavaScript
     */
    public static function getJSEvaluate()
    {
        return <<<JS
            if (App === undefined) {
                return '';
            }

            var params = this.getParameters();
            var relationship = params[0].evaluate();
            var relField = params[1].evaluate();
            var config = App.metadata.getModule('Forecasts', 'config');
            var closedWonStatus = ['Closed Won'];
            var closedLostStatus = ['Closed Lost'];
            var model = this.context.relatedModel || App.data.createRelatedBean(this.context.model, null, relationship);
            var ret = '';
            var latestIndex = 0;
            var totalCt = model.collection.length;
            var closedWonCt = 0;
            var closedLostCt = 0;
            var closedCt = 0;
            var index;

            if (config && config.is_setup) {
                closedWonStatus = config.sales_stage_won;
                closedLostStatus = config.sales_stage_lost;
            }
            
            var salesStages = App.lang.getAppListStrings('sales_stage_dom');
            var salesStageArr = [];

            _.each(closedWonStatus, function(stage) {
                salesStages = _.omit(salesStages, stage);
            }, this);
            _.each(closedLostStatus, function(stage) {
                salesStages = _.omit(salesStages, stage);
            }, this);

            salesStageArr = _.keys(salesStages);

            _.each(model.collection.models, function(model) {
                var salesStage = model.get(relField);

                if (_.contains(closedWonStatus, salesStage)) {
                    closedWonCt++;
                    closedCt++;
                } else if (_.contains(closedLostStatus, salesStage)) {
                    closedLostCt++;
                    closedCt++;
                } else {
                    index = salesStageArr.indexOf(salesStage);
                    if (index >= latestIndex) {
                        latestIndex = index;;
                        ret = salesStage;
                    }
                }
            }, this);
            
            if (closedLostCt === totalCt) {
                // if they're all Closed Lost, return a Closed Lost status
                return _.first(closedLostStatus);
            } else if (closedCt === totalCt && closedWonCt) {
                // all items are closed, and there's at least one Closed Won status
                return _.first(closedWonStatus);
            } else {
                return ret;
            }
JS;
    }

    /**
     * Number of params that the expression expects
     *
     * @return int
     */
    public static function getParamCount()
    {
        return 2;
    }

    /**
     * The first parameter is a number
     */
    public static function getParameterTypes()
    {
        return [
            AbstractExpression::$RELATE_TYPE,
            AbstractExpression::$STRING_TYPE,
        ];
    }

    /**
     * Returns the operation name that this Expression could be called by.
     */
    public static function getOperationName()
    {
        return ['opportunitySalesStage'];
    }

    /**
     * Returns the String representation of this Expression.
     */
    public function toString()
    {
    }
}
