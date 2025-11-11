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
 * <b>prorateValue(Number baseValue, Number dv1, String du1, Number dv2, String du2)</b><br/>
 * Returns the baseValue prorated using </i>dv1<i> and </i>du1<i> as the duration
 * value and unit (respectively) of the numerator, and </i>dv2<i> and </i>du2<i>
 * as the duration value and unit (respectively) of the denominator. Supports
 * "day", "month", or "year" as duration units. Note that since we don't take
 * the actual date into account, this function assumes there are 365 days in a
 * year and (365/12) days in a month.
 * ex: <i>prorateValue(10.50, 30, "day", 60, "day")</i> = 5.25
 */
class ProrateValueExpression extends NumericExpression
{
    /**
     * @var string[] list of valid units of duration, ordered from smallest to largest
     */
    protected $validDurationUnits = ['day', 'month', 'year'];

    /**
     * Returns the prorated value
     */
    public function evaluate()
    {
        // Get the values of the parameters passed in
        $params = $this->getParameters();
        $baseValue = $params[0]->evaluate();
        $numeratorValue = $params[1]->evaluate();
        $numeratorUnit = $params[2]->evaluate();
        $denominatorValue = $params[3]->evaluate();
        $denominatorUnit = $params[4]->evaluate();

        // We can't prorate unless we know what units to convert between
        $this->validateDurationUnits([$numeratorUnit, $denominatorUnit]);

        // Convert the numerator and denominator values to use a common unit
        $commonUnit = $this->getSmallestCommonUnit($numeratorUnit, $denominatorUnit);
        $numeratorValue = $this->convertValue($numeratorValue, $numeratorUnit, $commonUnit);
        $denominatorValue = $this->convertValue($denominatorValue, $denominatorUnit, $commonUnit);

        // Multiply the value by the numerator, then divide by the denominator
        return SugarMath::init($baseValue, 6)->mul($numeratorValue)->div($denominatorValue)->result();
    }

    /**
     * Checks that the duration units used in the formula are valid
     *
     * @param array $units the set of duration unit strings in the formula
     * @throws Exception
     */
    protected function validateDurationUnits($units = [])
    {
        foreach ($units as $unit) {
            if (!in_array($unit, $this->validDurationUnits)) {
                throw new Exception(static::getOperationName() .
                    ': Attempt to prorate value with invalid duration unit "' . $unit . '" ');
            }
        }
    }

    /**
     * Compares two duration units to find the smallest common unit between the
     * two
     *
     * @param $unit1 string the first duration unit
     * @param $unit2 string the second duration unit
     * @return string the smallest (day < month < year) common duration unit of the two units
     */
    protected function getSmallestCommonUnit($unit1, $unit2)
    {
        foreach ($this->validDurationUnits as $validDurationUnit) {
            if (in_array($validDurationUnit, [$unit1, $unit2])) {
                return $validDurationUnit;
            }
        }
        return '';
    }

    /**
     * Converts a value from one duration unit to another
     *
     * @param $value number the number to convert
     * @param $fromUnit string the duration unit to convert from ('year', 'month', 'day')
     * @param $toUnit string the duration unit to convert to ('year', 'month', 'day')
     * @return number
     */
    protected function convertValue($value, $fromUnit, $toUnit)
    {
        if ($fromUnit === 'year' && $toUnit === 'month') {
            return $value * 12;
        } elseif ($fromUnit === 'month' && $toUnit === 'year') {
            return $value / 12;
        } elseif ($fromUnit === 'year' && $toUnit === 'day') {
            return $value * 365;
        } elseif ($fromUnit === 'day' && $toUnit === 'year') {
            return $value / 365;
        } elseif ($fromUnit === 'month' && $toUnit === 'day') {
            return $value * (365 / 12);
        } elseif ($fromUnit === 'day' && $toUnit === 'month') {
            return $value / (365 / 12);
        } else {
            // Either the units are the same or we don't support this conversion
            return $value;
        }
    }

    /**
     * Returns the JS Equivalent of the evaluate function.
     */
    public static function getJSEvaluate()
    {
        return <<<EOQ
            // The list of valid units of duration, ordered from smallest to largest
            var validDurationUnits = ['day', 'month', 'year'];

            // Get the values of the parameters passed in
			var params = this.getParameters();
			var baseValue = params[0].evaluate();
			var numeratorValue = params[1].evaluate();
			var numeratorUnit = params[2].evaluate();
			var denominatorValue = params[3].evaluate();
			var denominatorUnit = params[4].evaluate();
			
			// Checks that the duration units used in the formula are valid
			var validateDurationUnits = function(units) {
			    _.each(units, function(unit) {
			        if (!_.contains(validDurationUnits, unit)) {
			            throw ('prorateValue: Attempt to prorate value with invalid duration unit "' + unit + '"');
			        }
			    });
			}
			
			// We can't prorate unless we know what units to convert between
			validateDurationUnits([numeratorUnit, denominatorUnit]);

            // Compares two duration units to find the lowest common unit between them
			var getSmallestCommonUnit = function(unit1, unit2) {
			    return _.find(validDurationUnits, function(validDurationUnit) {
			        return (_.contains([unit1, unit2], validDurationUnit));
			    }) || '';
			}

			// Converts a value from one duration unit to another
			var convertValue = function(value, fromUnit, toUnit) {
			    if (fromUnit === 'year' && toUnit === 'month') {
			        return value * 12;
			    } else if (fromUnit === 'month' && toUnit === 'year') {
			        return value / 12;
			    } else if (fromUnit === 'year' && toUnit === 'day') {
			        return value * 365;
			    } else if (fromUnit === 'day' && toUnit === 'year') {
			        return value / 365;
			    } else if (fromUnit === 'month' && toUnit === 'day') {
			        return value * (365/12);
			    } else if (fromUnit === 'day' && toUnit === 'month') {
			        return value / (365/12);
			    } else {
			        // Either the units are the same or we don't support this conversion
			        return value;
			    }
			}

            // Convert the numerator and denominator values to use a common unit
            var commonUnit = getSmallestCommonUnit(numeratorUnit, denominatorUnit);
            var numeratorValue = convertValue(numeratorValue, numeratorUnit, commonUnit);
            var denominatorValue = convertValue(denominatorValue, denominatorUnit, commonUnit);

            // Multiply the value by the numerator, then divide by the denominator
            return this.context.divide(this.context.multiply(baseValue, numeratorValue), denominatorValue);
EOQ;
    }

    /**
     * Returns the operation name that this Expression should be
     * called by.
     */
    public static function getOperationName()
    {
        return 'prorateValue';
    }

    /**
     * We must have 5 values to prorate:
     *  -A numeric base value
     *  -A time duration for the numerator of the proration division, made up of two values:
     *      -A numeric value representing the amount time
     *      -A string representing the unit of time
     *  -A time duration for the denominator of the proration division, made up of two values:
     *      -A numeric value representing the amount time
     *      -A string representing the unit of time
     */
    public static function getParameterTypes()
    {
        return [
            AbstractExpression::$NUMERIC_TYPE,
            AbstractExpression::$NUMERIC_TYPE,
            AbstractExpression::$STRING_TYPE,
            AbstractExpression::$NUMERIC_TYPE,
            AbstractExpression::$STRING_TYPE,
        ];
    }

    /**
     * Returns the exact number of parameters needed.
     */
    public static function getParamCount()
    {
        return 5;
    }
}
