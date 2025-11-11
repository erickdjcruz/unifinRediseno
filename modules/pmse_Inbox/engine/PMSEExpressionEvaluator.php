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
 * Description of PMSEExpressionEvaluator
 *
 */
class PMSEExpressionEvaluator
{
    use PMSEEvalRelations;

    /**
     * Store the existing groups and subgroups
     * @var array
     */
    public $arrayGroups;

    /**
     * The types of operators that supports the evaluation according to the
     * following order ot operations
     * 'unary', 'exponent', 'multiplication/division', 'addition/substraction', 'relations', 'logic'
     * @var array
     */
    public $operationList = [
        'unary' => ['NOT', '!'], //unary operators
        'exponent' => ['^'], //function for evalAritmetic
        'multiply_divide' => ['x', '/'], //function for Multiplication Division
        'add_substract' => ['+', '-'], //function for Add and Substraction
        'relation' => ['<', '<=', '>', '>=', '==', '!='], //function for evalRelations
        'logic' => ['AND', 'OR'], //function for evalLogic
    ];

    /**
     * history assessments by token
     * @var string
     */
    public $condition;

    /**
     * exception codes
     */
    protected static $exceptionCodes = [
        'NO_BUSINESS_CENTER' => 100,
    ];

    /**
     * Get exception code
     * @param string $key Key of exception code
     * @return int Exception code, or 0 if no $key is found.
     */
    public static function getExceptionCode(string $key): int
    {
        return static::$exceptionCodes[$key] ?? 0;
    }

    /**
     * Add exception code
     * @param string $key Key of exception code
     * @param int $code Exception code
     * @return void
     */
    public static function addExceptionCode(string $key, int $code): void
    {
        static::$exceptionCodes[$key] = $code;
    }

    /**
     * Remove exception code
     * @param string $key Key of exception code
     * @return bool
     */
    public static function removeExceptionCode(string $key): bool
    {
        $ret = static::hasExceptionCode($key);
        unset(static::$exceptionCodes[$key]);
        return $ret;
    }

    /**
     * Has exception code
     * @param string $key Key of exception code
     * @return bool
     */
    public static function hasExceptionCode(string $key): bool
    {
        return isset(static::$exceptionCodes[$key]);
    }

    public function getCondition()
    {
        return $this->condition;
    }

    public function evaluateExpression($expression)
    {
        $accArray = [];
        foreach ($this->operationList as $groupKey => $groupOperator) {
            while ($token = array_shift($expression)) {
                if (safeInArray($token->expValue, $groupOperator)
                    && $token->expType !== 'VARIABLE'
                    && $token->expType !== 'CONSTANT') {
                    if ($groupKey != 'unary') {
                        $firstOperand = array_pop($accArray);
                    } else {
                        $firstOperand = $this->getDefaultToken();
                    }
                    $accArray[] = $this->processOperation(
                        $groupKey,
                        $firstOperand,
                        $token,
                        array_shift($expression)
                    );
                } else {
                    $accArray[] = $token;
                }
            }
            $expression = $accArray;
            $accArray = [];
        }
        return $expression;
    }

    public function getDefaultToken()
    {
        $token = new stdClass();
        $token->expType = 'CONSTANT';
        $token->expSubtype = 'string';
        $token->expValue = '';
        $token->expLabel = '';
        return $token;
    }

    public function processOperation($groupKey, $firstToken, $token, $secondToken)
    {
        $resultToken = new stdClass();
        $resultToken->expType = 'CONSTANT';

        $firstTokenExpSubtype = PMSEEngineUtils::getExpressionSubtype($firstToken);
        $secondTokenExpSubtype = PMSEEngineUtils::getExpressionSubtype($secondToken);

        if (strtolower($firstTokenExpSubtype) == 'currency' || strtolower($secondTokenExpSubtype) == 'currency') {
            $resultToken->expSubtype = 'currency';
            $operationGroup = $this->checkCurrencyEvaluation(
                $groupKey,
                $firstToken,
                $token,
                $secondToken
            );
            $resultToken = $this->routeCurrencyFunctionOperator(
                $operationGroup,
                $firstToken,
                $token->expValue,
                $secondToken
            );
        } else {
            $operationGroup = $this->checkDateEvaluation(
                $groupKey,
                $firstToken,
                $token,
                $secondToken
            );
            $resultToken->expValue = $this->routeFunctionOperator(
                $operationGroup,
                $firstToken->expValue,
                $token->expValue,
                $secondToken->expValue,
                null,
                false,
                $secondToken->expBean ?? null
            );
            $this->processTokenAttributes($resultToken);
        }

        return $resultToken;
    }

    public function checkDateEvaluation($key, $firstToken, $operator, $secondToken)
    {
        $firstTokenExpSubtype = PMSEEngineUtils::getExpressionSubtype($firstToken);
        $secondTokenExpSubtype = PMSEEngineUtils::getExpressionSubtype($secondToken);
        if (strtolower($firstTokenExpSubtype) == 'date' ||
            strtolower($firstTokenExpSubtype) == 'datetime') {
            if (strtolower($secondTokenExpSubtype) == 'date' ||
                strtolower($secondTokenExpSubtype) == 'datetime') {
                $key = 'dateDateOp';
            } elseif (strtolower($secondTokenExpSubtype) == 'timespan') {
                if (PMSEEngineUtils::isForBusinessTimeOp($secondToken->expValue)) {
                    $key = 'dateSpanBusinessOp';
                } else {
                    $key = 'dateSpanOp';
                }
            }
        } elseif (strtolower($firstTokenExpSubtype) == 'timespan') {
            if (strtolower($secondTokenExpSubtype) == 'date' ||
                strtolower($secondTokenExpSubtype) == 'datetime') {
                $key = 'spanDateOp';
            } elseif (strtolower($secondTokenExpSubtype) == 'timespan') {
                $key = 'spanSpanOp';
            }
        }
        return $key;
    }

    public function checkCurrencyEvaluation($key, $firstToken, $operator, $secondToken)
    {
        $firstTokenExpSubtype = PMSEEngineUtils::getExpressionSubtype($firstToken);
        $secondTokenExpSubtype = PMSEEngineUtils::getExpressionSubtype($secondToken);
        if (strtolower($firstTokenExpSubtype) == 'currency' || strtolower($secondTokenExpSubtype) == 'currency') {
            switch ($operator->expValue) {
                case '+':
                case '-':
                    $key = 'currencyAddSubstract';
                    break;
                case 'x':
                case '/':
                    $key = 'currencyMultiplyDivide';
                    break;
            }
        }
        return $key;
    }

    public function processExpression($expression = [])
    {
        $resultGroup = [];
        while ($token = array_shift($expression)) {
            switch (true) {
                case $token->expValue === '(':
                    $expression = $this->processExpression($expression);
                    break;
                case $token->expValue === ')':
                    $resultGroup = array_merge($this->evaluateExpression($resultGroup), $expression);
                    return $resultGroup;
                    break;
                default:
                    $resultGroup[] = $token;
                    break;
            }
        }
        return $this->evaluateExpression($resultGroup);
    }

    /**
     * Method to address the function to conduct the operation
     * @param string $operation Name of the method to redirect
     * @param string $firstOperand assess value
     * @param string $operator type of operator that evaluates
     * @param string $secondOperand assess value
     * @param string $tokenType
     * @param boolean $isUpdate Flag that determines if this is a record update or record add
     * @return type
     */
    public function routeFunctionOperator(
        $operation,
        $firstOperand,
        $operator,
        $secondOperand = null,
        $tokenType = null,
        $isUpdate = false,
        $secondOperandBean = null
    ) {

        switch ($operation) {
            case 'unary':
                $result = $this->executeUnaryOp($operator, $secondOperand);
                break;
            case 'exponent':
                $result = $this->executeExponentOp($firstOperand, $operator, $secondOperand);
                break;
            case 'multiply_divide':
                $result = $this->executeMultiplyDivideOp($firstOperand, $operator, $secondOperand);
                break;
            case 'add_substract':
                $result = $this->executeAddSubstractOp($firstOperand, $operator, $secondOperand);
                break;
            case 'dateDateOp':
                $result = $this->executeDateDateOp($firstOperand, $operator, $secondOperand);
                break;
            case 'dateSpanOp':
                $result = $this->executeDateSpanOp($firstOperand, $operator, $secondOperand);
                break;
            case 'spanDateOp':
                $result = $this->executeSpanDateOp($firstOperand, $operator, $secondOperand);
                break;
            case 'dateSpanBusinessOp':
                $result = $this->executeDateSpanBCOp($firstOperand, $operator, $secondOperand, $secondOperandBean);
                break;
            case 'spanSpanOp':
                $result = $this->executeSpanSpanOp($firstOperand, $operator, $secondOperand);
                break;
            case 'relation':
                $result = (bool)$this->evalRelations($firstOperand, $operator, $secondOperand, $tokenType, $isUpdate);
                break;
            case 'logic':
                $result = $this->executeLogicOp($firstOperand, $operator, $secondOperand);
                break;
            default:
                $result = 0;
                break;
        }
        return $result;
    }

    public function routeCurrencyFunctionOperator(
        $operation,
        $firstOperand,
        $operator,
        $secondOperand
    ) {

        switch ($operation) {
            case 'currencyAddSubstract':
                $result = $this->executeAddSubstractCurrency($firstOperand, $operator, $secondOperand);
                break;
            case 'currencyMultiplyDivide':
                $result = $this->executeMultiplyDivideCurrency($firstOperand, $operator, $secondOperand);
                break;
            default:
                $result = 0;
                break;
        }
        return $result;
    }

    public function processTokenAttributes($token)
    {
        switch (true) {
            case is_integer($token->expValue):
            case is_double($token->expValue):
            case is_float($token->expValue):
                $token->expSubtype = 'number';
                $token->expLabel = (string)$token->expValue;
                break;
            case is_string($token->expValue)://if is string
                $token->expSubtype = 'string';
                $token->expLabel = $token->expValue;
                break;
            case is_bool($token->expValue):
                $token->expSubtype = 'boolean';
                $boolarray = [false => 'false', true => 'true'];
                $token->expLabel = $boolarray[$token->expValue];
                break;
            case is_a($token->expValue, 'DateTime'):
                $dateTimeObject = $token->expValue;
                $token->expValue = $dateTimeObject->format('c');
                $token->expLabel = $dateTimeObject->format('Y-m-d H:i:s');
                $token->expSubtype = 'date';
                break;
            case is_a($token->expValue, 'DateInterval'):
                $dateIntervalObject = $token->expValue;
                $token->expLabel = $dateIntervalObject->format('P%yY%mM%dDT%hH%iM%sS');
                $token->expSubtype = 'timespan';
                break;
        }
        return $token;
    }

    public function executeExponentOp($value1, $operator, $value2)
    {
        return $value1 ** $value2;
    }

    public function executeUnaryOp($operator, $value)
    {
        $result = 0;
        switch ($operator) {
            case 'NOT':
                $result = !(bool)($value);
                break;
            case '!':
                $result = !(bool)($value);
                break;
        }
        return $result;
    }

    public function executeMultiplyDivideOp($value1, $operator, $value2)
    {
        $result = 0;
        switch ($operator) {
            case '/':
                $result = $value1 / $value2;
                break;
            case 'x':
                $result = $value1 * $value2;
                break;
        }
        return $result;
    }

    public function isScalar($expression)
    {
        $expSubtype = PMSEEngineUtils::getExpressionSubtype($expression);
        $result = ($expression->expType == 'CONSTANT' && $expSubtype == 'number') ||
            ($expression->expType == 'VARIABLE' && ($expSubtype == 'Currency' ||
                    $expSubtype == 'Decimal' || $expSubtype == 'Float' ||
                    $expSubtype == 'Integer'));
        return $result;
    }

    public function executeMultiplyDivideCurrency($value1, $operator, $value2)
    {
        $result = null;
        $value1ExpSubtype = PMSEEngineUtils::getExpressionSubtype($value1);
        $value2ExpSubtype = PMSEEngineUtils::getExpressionSubtype($value2);
        if ((strtolower($value1ExpSubtype) == 'currency' && $this->isScalar($value2)) ||
            (strtolower($value2ExpSubtype) == 'currency' && $this->isScalar($value1))) {
            switch ($operator) {
                case 'x':
                    $v1exp = $value1->expValue ?? null;
                    $v2exp = $value1->expValue ?? null;
                    if (is_numeric($v1exp) && is_numeric($v2exp)) {
                        $result = (float)$v1exp * (float)$v2exp;
                    } else {
                        $result = 0;
                        PMSELogger::getInstance()->error(
                            'Non-numeric values in executeMultiplyDivideCurrency: '
                            . '$value1->expValue = ' . var_export($v1exp, true)
                            . '; $value2->expValue = ' . var_export($v2exp, true)
                        );
                    }
                    break;
                case '/':
                    if (strtolower($value1ExpSubtype) == 'currency' && (float)($value2->expValue ?? 0) != 0) {
                        $result = (float)($value1->expValue ?? 0) / (float)$value2->expValue;
                    } else {
                        $error = 'Impossible to divide an scalar value by a currency.';
                    }
                    break;
            }
        } else {
            $error = 'Mutiply|Divide Currency - At least one operand must be currency type.';
        }
        if (isset($error)) {
            throw new PMSEExpressionEvaluationException($error, func_get_args());
        }
        $currency_id = strtolower($value1ExpSubtype) == 'currency' ? $value1->expField : $value2->expField;
        $newCurrency = new stdClass();
        $newCurrency->expType = 'CONSTANT';
        $newCurrency->expSubtype = 'currency';
        $newCurrency->expField = $currency_id;
        $newCurrency->expValue = $result;
        return $newCurrency;
    }

    public function executeAddSubstractOp($value1, $operator, $value2)
    {
        $result = 0;
        switch ($operator) {
            case '+':
                $result = (float)$value1 + (float)$value2;
                break;
            case '-':
                $result = (float)$value1 - (float)$value2;
                break;
        }
        return $result;
    }

    public function executeAddSubstractCurrency($value1, $operator, $value2)
    {
        $result = null;
        global $current_user;

        $value1ExpSubtype = PMSEEngineUtils::getExpressionSubtype($value1);
        $value2ExpSubtype = PMSEEngineUtils::getExpressionSubtype($value2);
        if (!(strtolower($value1ExpSubtype) == 'currency' && strtolower($value2ExpSubtype) == 'currency')) {
            throw new PMSEExpressionEvaluationException('Add|Substract Currency - ' .
                'Both operands must be currency types.', func_get_args());
        }

        if ($value1->expField == $value2->expField) {
            $num1 = $value1->expValue;
            $num2 = $value2->expValue;
            $resCurrency = $value1->expField;
        } else {
            $resCurrency = SugarCurrency::getUserLocaleCurrency();
            $resCurrency = $resCurrency->id;
            $num1 = SugarCurrency::convertAmount($value1->expValue, $value1->expField, $resCurrency);
            $num2 = SugarCurrency::convertAmount($value2->expValue, $value2->expField, $resCurrency);
        }

        switch ($operator) {
            case '+':
                $result = (float)$num1 + (float)$num2;
                break;
            case '-':
                $result = (float)$num1 - (float)$num2;
                break;
        }

        if ($result < 0) {
            throw new PMSEExpressionEvaluationException(
                'Currency Subtraction - The result is a negative currency.',
                func_get_args()
            );
        }

        $resultCurrency = new stdClass();
        $resultCurrency->expType = 'CONSTANT';
        $resultCurrency->expSubtype = 'currency';
        $resultCurrency->expField = $resCurrency;
        $resultCurrency->expValue = $result;
        return $resultCurrency;
    }

    /*
     * A function that calculates the interval between two dates
     *
     * @param string    $value1     A string representation of a DateTime value
     * @param string    $operator   '-' is the only logical choice here
     * @param string    $value2     A string representation of a DateTime value
     *
     * @return DateInterval     The difference between the two DateTime values (signed)
     */
    public function executeDateDateOp($value1, $operator, $value2)
    {
        $intervalObject = null;
        if ($operator == '-') {
            $valueObject1 = new DateTime($value1);
            $valueObject2 = new DateTime($value2);
            $intervalObject = $valueObject2->diff($valueObject1);
        }
        return $intervalObject;
    }

    /*
     * A function that calculates a date plus/minus an interval
     *
     * @param string                $value1     A string representation of a DateTime value
     * @param string                $operator   '+' or '-'
     * @param DateInterval/string   $value2     A DateInterval object or a string representation
     *                                          of a DateInterval value
     *
     * @return DateTime     The resulting DateTime value
     */
    public function executeDateSpanOp($value1, $operator, $value2)
    {
        $dateObject = new DateTime($value1);
        $intervalObject = $this->processDateInterval($value2);
        if ($operator == '+') {
            $dateObject->add($intervalObject);
        } elseif ($operator == '-') {
            $dateObject->sub($intervalObject);
        }
        return $dateObject;
    }

    /**
     * A function that fetches business center related time
     *
     * @param $value1 a DateTime object
     * @param String $operator '+' or '-' (only + is supported at the moment)
     * @param String $value2 business center based time interval
     * @param String $bid business center id
     * @return DateTime The resulting DateTime value
     */
    public function executeDateSpanBCOp($value1, string $operator, string $value2, string $bid): DateTime
    {
        $bean = BeanFactory::getBean('BusinessCenters', $bid);
        if (empty($bean) || empty($bean->id)) {
            // can't find business center, throw an exception for the caller to handle
            PMSELogger::getInstance()->error('Invalid Business Center bean, id: ' . $bid);
            throw new PMSEExpressionEvaluationException(
                'Invalid Business Center bean.',
                func_get_args(),
                static::getExceptionCode('NO_BUSINESS_CENTER')
            );
        }

        $pattern = PMSEEngineUtils::getBusinessTimePattern(false);
        if (!preg_match($pattern, $value2, $matches)) {
            PMSELogger::getInstance()->error('Invalid Business Center string: ' . $value2);
            throw new PMSEExpressionEvaluationException(
                'Invalid Business Center string: ' . $value2,
                func_get_args(),
                static::getExceptionCode('NO_BUSINESS_CENTER')
            );
        }

        // If `$value1` is not a DateTime object then it is either a date string
        // or the string `'now'`. Both of these strings can be made into a DateTime
        // object, which we need for the date calculations coming up.
        if (!$value1 instanceof DateTime) {
            $value1 = new DateTime($value1);
        }

        if ($bean->timezone) {
            $value1->setTimeZone(new DateTimeZone($bean->timezone)); // convert to business center time zone
        } else {
            $value1->setTimeZone(new DateTimeZone(date_default_timezone_get())); // convert to server time zone
        }
        $beforeTime = $value1->format('m/d/Y H:i:s');
        $unit = PMSEEngineUtils::getBusinessTimeUnit($matches[2]);
        if (empty($unit)) {
            PMSELogger::getInstance()->error('Invalid Business Center unit: ' . $matches[2]);
            throw new PMSEExpressionEvaluationException(
                'Invalid Business Center unit:' . $matches[2],
                func_get_args(),
                static::getExceptionCode('NO_BUSINESS_CENTER')
            );
        }

        $result = $bean->getIncrementedBusinessDatetime($beforeTime, $matches[1], $unit);
        return new DateTime($result);
    }

    /*
     * A function that calculates the sum of an interval and a date
     *
     * @param DateInterval/string   $value1     A DateInterval object or a string representation
     *                                          of a DateInterval value
     * @param string                $operator   '+' is the only logical choice here
     * @param string                $value2     A string representation of a DateTime value
     *
     * @return DateTime     The sum of the DateInterval and the DateTime values
     */
    public function executeSpanDateOp($value1, $operator, $value2)
    {
        $dateObject = null;
        if ($operator == '+') {
            $dateObject = new DateTime($value2);
            $intervalObject = $this->processDateInterval($value1);
            $dateObject->add($intervalObject);
        }
        return $dateObject;
    }

    /*
     * A function that calculates the sum of/difference between two intervals
     *
     * @param DateInterval/string   $value1     A DateInterval object or a string representation
     *                                          of a DateInterval value
     * @param string                $operator   '+' or '-'
     * @param DateInterval/string   $value1     A DateInterval object or a string representation
     *                                          of a DateInterval value
     *
     * @return DateInterval     The sum of/difference between the two DateInterval values (signed)
     */
    public function executeSpanSpanOp($value1, $operator, $value2)
    {
        $intervalObject1 = $this->processDateInterval($value1);
        $intervalObject2 = $this->processDateInterval($value2);
        $now = new \DateTime();
        $new = clone $now;
        $new = $new->add($intervalObject1);
        if ($operator == '+') {
            $new = $new->add($intervalObject2);
        } elseif ($operator == '-') {
            $new = $new->sub($intervalObject2);
        }
        return $now->diff($new);
    }

    /*
     * A function the converts the internal string representation of a DateInterval value
     * (such as '1y', '2m', '3w', '4d') into the corresponding DateInterval object
     *
     * @param DateInterval/string   $interval   A DateInterval object or a string representation
     *                                          of a DateInterval value
     *
     * @return DateInterval     The corresponding DateInterval object
     */
    public function processDateInterval($interval)
    {
        if (is_a($interval, 'DateInterval')) {
            return $interval;
        } else {
            $pattern = "/(\d*)(y|min|m|w|d|h)/";
            preg_match($pattern, $interval, $matches);
            $result = new DateInterval($this->processDateUnit($matches[1], $matches[2]));
            return $result;
        }
    }

    public function processDateUnit($value, $unit)
    {
        switch ($unit) {
            case 'y':
                return 'P' . $value . 'Y';
                break;
            case 'm':
                return 'P' . $value . 'M';
                break;
            case 'w':
                return 'P' . $value . 'W';
                break;
            case 'd':
                return 'P' . $value . 'D';
                break;
            case 'h':
                return 'PT' . $value . 'H';
                break;
            case 'min':
                return 'PT' . $value . 'M';
                break;
        }
    }

    /**
     * Evaluates if two arrays have the same content. Note that the array's elements are comared using the ===, so the
     * comparision is an identity comparison too.
     * @param $arr1
     * @param $arr2
     * @return boolean
     */
    public function evalEqualArrays($arr1, $arr2)
    {
        $d1 = array_diff($arr1, $arr2);
        $d2 = array_diff($arr2, $arr1);
        return empty($d1) && empty($d2);
    }

    /**
     * Method that evaluates the logic part
     * @param boolean $value1 takes the values ​​1 or 0
     * @param string $logical if evaluate AND, OR, NOT
     * @param boolean $value2 takes the values ​​1 or 0 in the case of NOT is null
     * @return int
     */
    public function executeLogicOp($value1, $logical, $value2 = null)
    {
        switch ($logical) {
            case 'AND':
                if ($value1 && $value2) {
                    $ret = 1;
                } else {
                    $ret = 0;
                }
                break;
            case 'OR':
                if ($value1 || $value2) {
                    $ret = 1;
                } else {
                    $ret = 0;
                }
                break;
            default:
                $ret = 0;
                break;
        }
        return (bool)$ret;
    }

    /**
     * Method that takes cast depending on the type of value
     * (int), (integer) - integer
     * (bool), (boolean) - boolean
     * (float), (double), (real) - float
     * (string) - string
     * (array) - array
     * (object) - object
     * (unset) - NULL (PHP 5)
     * @param type $value
     * @param type $tipeDate
     * @return type
     */
    public function typeData($value, $typeDate)
    {
        global $timedate;
        switch (strtolower((string)$typeDate)) {
            case 'address'://varchar
            case 'relate'://varchar
            case 'text':
            case 'url'://varchar
            case 'textfield'://varchar
            case 'name'://varchar
            case 'varchar'://varchar
            case 'radioenum': //varchar
            case 'parent_type'://varchar
                $newValue = (string)$value;
                break;
            case 'bool'://bool
            case 'boolean':
            case 'checkbox':
                if (!empty($value) && $value === 'false') {
                    $newValue = false;
                } else {
                    $newValue = (bool)$value;
                }
                break;
            case 'date'://date
                // Same as datetime fields, if we have a DateTime object, use it
                if ($value instanceof DateTime) {
                    $newValue = $value;
                } else {
                    // Otherwise try to get the value from the date string
                    $newValue = !empty($value) ? $timedate->fromString($value) : false;
                }
                break;
            case 'datetime'://datetime
            case 'datetimecombo'://datetime
                // Here we are assuming $value always will be
                // a DateTime or String instances
                if ($value instanceof DateTime) {
                    $newValue = $value;
                } else {
                    // If there is a date based criteria evaluation, but there is
                    // no date presented, this will fatal out at the tzGMT part
                    // So we set a reasonable default here and handle setting if
                    // there is something to set
                    $newValue = false;

                    // If there is an actual value given, use it
                    if (!empty($value)) {
                        // Assumption here is that the date value is in ISO format
                        $newDate = $timedate->fromIso($value);

                        // If the conversion worked, use THAT
                        if ($newDate) {
                            $newValue = $timedate->tzGMT($newDate);
                        }
                    }
                }
                break;
            case 'enum'://int
            case 'int':
                $newValue = (int)$value;
                break;
            case 'float':
                $newValue = (float)$value;
                break;
            case 'integer':
                $newValue = (int)$value;
                break;
            case 'decimal': //decimal
                $newValue = (float)$value;
                break;
            case 'currency': //double
                $newValue = (float)$value;
                break;
            case 'encrypt':
            case 'html':
            case 'iframe':
            case 'image':
            case 'multienum':
            case 'phone':
            default:
                $newValue = $value;
                break;
        }
        return $newValue;
    }
}
