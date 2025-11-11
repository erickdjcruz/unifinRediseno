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

$runtimeTextOperators = [
    'empty' => 'LBL_IS_EMPTY',
    'not_empty' => 'LBL_IS_NOT_EMPTY',
    'equals' => 'LBL_EQUALS',
    'not_equals_str' => 'LBL_DOES_NOT_EQUAL',
    'contains' => 'LBL_CONTAINS',
    'does_not_contain' => 'LBL_DOES_NOT_CONTAIN',
    'starts_with' => 'LBL_STARTS_WITH',
    'ends_with' => 'LBL_ENDS_WITH',
    'anything' => 'LBL_ANYTHING',
];

$runtimeIsIsntOperators = [
    'is' => 'LBL_IS',
    'is_not' => 'LBL_IS_NOT',
    'anything' => 'LBL_ANYTHING',
];

$runtimeUsernameOperators = [
    'empty' => 'LBL_IS_EMPTY',
    'not_empty' => 'LBL_IS_NOT_EMPTY',
    'one_of' => 'LBL_ONE_OF',
    'not_one_of' => 'LBL_IS_NOT_ONE_OF',
    'anything' => 'LBL_ANYTHING',
];

$runtimeDateOperators = [
    'empty' => 'LBL_IS_EMPTY',
    'not_empty' => 'LBL_IS_NOT_EMPTY',
    'on' => 'LBL_ON',
    'before' => 'LBL_BEFORE',
    'after' => 'LBL_AFTER',
    'not_equals_str' => 'LBL_NOT_ON',
    'tp_yesterday' => 'LBL_YESTERDAY',
    'tp_today' => 'LBL_TODAY',
    'tp_tomorrow' => 'LBL_TOMORROW',
    'tp_last_n_days' => 'LBL_LAST_N_DAYS',
    'tp_next_n_days' => 'LBL_NEXT_N_DAYS',
    'tp_last_7_days' => 'LBL_LAST_7_DAYS',
    'tp_next_7_days' => 'LBL_NEXT_7_DAYS',
    'tp_last_month' => 'LBL_LAST_MONTH',
    'tp_this_month' => 'LBL_THIS_MONTH',
    'tp_next_month' => 'LBL_NEXT_MONTH',
    'tp_last_30_days' => 'LBL_LAST_30_DAYS',
    'tp_next_30_days' => 'LBL_NEXT_30_DAYS',
    'tp_last_quarter' => 'LBL_LAST_QUARTER',
    'tp_this_quarter' => 'LBL_THIS_QUARTER',
    'tp_next_quarter' => 'LBL_NEXT_QUARTER',
    'tp_last_year' => 'LBL_LAST_YEAR',
    'tp_this_year' => 'LBL_THIS_YEAR',
    'tp_next_year' => 'LBL_NEXT_YEAR',
    'tp_previous_fiscal_year' => 'LBL_PREVIOUS_FISCAL_YEAR',
    'tp_previous_fiscal_quarter' => 'LBL_PREVIOUS_FISCAL_QUARTER',
    'tp_current_fiscal_year' => 'LBL_CURRENT_FISCAL_YEAR',
    'tp_current_fiscal_quarter' => 'LBL_CURRENT_FISCAL_QUARTER',
    'tp_next_fiscal_year' => 'LBL_NEXT_FISCAL_YEAR',
    'tp_next_fiscal_quarter' => 'LBL_NEXT_FISCAL_QUARTER',
    'anything' => 'LBL_ANYTHING',
];

$runtimeNumberOperators = [
    'empty' => 'LBL_IS_EMPTY',
    'not_empty' => 'LBL_IS_NOT_EMPTY',
    'equals' => 'LBL_EQUALS',
    'not_equals' => 'LBL_DOES_NOT_EQUAL',
    'less' => 'LBL_LESS_THAN',
    'less_equal' => 'LBL_LESS_THAN_EQUAL',
    'greater_equal' => 'LBL_GREATER_THAN_EQUAL',
    'greater' => 'LBL_GREATER_THAN',
    'between' => 'LBL_IS_BETWEEN',
    'anything' => 'LBL_ANYTHING',
];

$runtimeRelateOperators = [
    'is' => 'LBL_IS',
    'is_not' => 'LBL_IS_NOT',
    'empty' => 'LBL_IS_EMPTY',
    'not_empty' => 'LBL_IS_NOT_EMPTY',
    'anything' => 'LBL_ANYTHING',
];

$runtimeNameOperators = array_merge($runtimeTextOperators, $runtimeIsIsntOperators);
$runtimeEnumOperators = array_merge($runtimeIsIsntOperators, $runtimeUsernameOperators);

$viewdefs['Reports']['base']['filter']['runtime-operators'] = [
    'encrypt' => [
        'empty' => 'LBL_IS_EMPTY',
        'not_empty' => 'LBL_IS_NOT_EMPTY',
        'equals' => 'LBL_EQUALS',
        'not_equals_str' => 'LBL_DOES_NOT_EQUAL',
        'anything' => 'LBL_ANYTHING',
    ],
    'assigned_user_name' => [
        'is' => 'LBL_IS',
        'is_not' => 'LBL_IS_NOT',
        'one_of' => 'LBL_ONE_OF',
        'empty' => 'LBL_IS_EMPTY',
        'not_empty' => 'LBL_IS_NOT_EMPTY',
        'anything' => 'LBL_ANYTHING',
    ],
    'bool' => [
        'equals' => 'LBL_EQUALS',
        'empty' => 'LBL_IS_EMPTY',
        'not_empty' => 'LBL_IS_NOT_EMPTY',
        'anything' => 'LBL_ANYTHING',
    ],
    'team_set_id' => [
        'any' => 'LBL_ANY',
        'all' => 'LBL_ALL',
        'exact' => 'LBL_EXACT',
        'anything' => 'LBL_ANYTHING',
    ],
    'Tags:name' => [
        'equals' => 'LBL_EQUALS',
        'not_equals_str' => 'LBL_DOES_NOT_EQUAL',
        'contains' => 'LBL_CONTAINS',
        'does_not_contain' => 'LBL_DOES_NOT_CONTAIN',
        'anything' => 'LBL_ANYTHING',
    ],
    'file' => [
        'empty' => 'LBL_IS_EMPTY',
        'not_empty' => 'LBL_IS_NOT_EMPTY',
        'anything' => 'LBL_ANYTHING',
    ],
    'username' => array_merge(
        $runtimeUsernameOperators,
        $runtimeIsIsntOperators,
        ['reports_to' => 'LBL_REPORTS_TO'],
    ),
    'varchar' => $runtimeTextOperators,
    'char' => $runtimeTextOperators,
    'text' => $runtimeTextOperators,
    'email' => $runtimeTextOperators,
    'yim' => $runtimeTextOperators,
    'time' => $runtimeTextOperators,
    'phone' => $runtimeTextOperators,
    'url' => $runtimeTextOperators,
    'name' => $runtimeNameOperators,
    'fullname' => $runtimeNameOperators,
    'date' => array_merge(
        $runtimeDateOperators,
        ['between_dates' => 'LBL_IS_BETWEEN'],
    ),
    'datetime' => array_merge(
        $runtimeDateOperators,
        ['between_dates' => 'LBL_IS_BETWEEN'],
    ),
    'datetimecombo' => array_merge(
        $runtimeDateOperators,
        ['between_datetimes' => 'LBL_IS_BETWEEN'],
    ),
    'int' => $runtimeNumberOperators,
    'long' => $runtimeNumberOperators,
    'float' => $runtimeNumberOperators,
    'decimal' => $runtimeNumberOperators,
    'currency' => $runtimeNumberOperators,
    'num' => $runtimeNumberOperators,
    'autoincrement' => $runtimeNumberOperators,
    'enum' => $runtimeEnumOperators,
    'radioenum' => $runtimeEnumOperators,
    'parent_type' => $runtimeEnumOperators,
    'timeperiod' => $runtimeEnumOperators,
    'currency_id' => $runtimeEnumOperators,
    'multienum' => $runtimeEnumOperators,
    'relate' => $runtimeRelateOperators,
    'id' => $runtimeRelateOperators,
];
