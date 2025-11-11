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

function get_expression($express_type, $first, $second)
{

    if ($express_type == '+') {
        return express_add($first, $second);
    }
    if ($express_type == '-') {
        return express_subtract($first, $second);
    }
    if ($express_type == '*') {
        return express_multiple($first, $second);
    }
    if ($express_type == '/') {
        return express_divide($first, $second);
    }
    //end function get_expression
}

function express_add($first, $second)
{
    [$first, $second] = express_prepare_params($first, $second);
    return $first + $second;
}

function express_subtract($first, $second)
{
    [$first, $second] = express_prepare_params($first, $second);
    return $first - $second;
}

function express_multiple($first, $second)
{
    [$first, $second] = express_prepare_params($first, $second);
    return $first * $second;
}

function express_divide($first, $second)
{
    [$first, $second] = express_prepare_params($first, $second);
    if ($second == 0) {
        LoggerManager::getLogger()->warn('Division by zero: ' . (new Exception())->getTraceAsString());
        return $first;
    }
    return $first / $second;
}

/**
 * @param $first
 * @param $second
 * @return float[]|int[]
 */
function express_prepare_params($first, $second): array
{
    if (!is_numeric($first)) {
        $first = (float)$first;
    }
    if (!is_numeric($second)) {
        $second = (float)$second;
    }
    return [$first, $second];
}
