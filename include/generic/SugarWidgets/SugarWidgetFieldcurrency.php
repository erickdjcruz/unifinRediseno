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


global $current_user;

$global_currency_obj = null;

function get_currency()
{
    global $global_currency_obj;
    if (empty($global_currency_obj)) {
        $global_currency_obj = BeanFactory::newBean('Currencies')->getUserCurrency();
    }
    return $global_currency_obj;
}


class SugarWidgetFieldCurrency extends SugarWidgetFieldInt
{
    public function __construct(&$layout_manager)
    {
        parent::__construct($layout_manager);
        $this->reporter = $this->layout_manager->getAttribute('reporter');
    }


    public function displayList($layout_def)
    {
        $field_def = [];
        global $current_user;

        if (empty($layout_def['group_function'])) {
            $currency = $this->getCurrency($layout_def);
            $symbol = $currency['currency_symbol'];
            $currency_id = $currency['currency_id'];
        } else {
            $currency = $current_user->getPreference('currency_show_preferred')
                ? SugarCurrency::getUserLocaleCurrency()
                : SugarCurrency::getBaseCurrency();
            $currency_id = $currency->id;
            $symbol = $currency->symbol;
        }

        $layout_def['currency_symbol'] = $symbol;
        $layout_def['currency_id'] = $currency_id;
        $display = $this->displayListPlain($layout_def);

        if (!empty($layout_def['column_key'])) {
            $field_def = $this->reporter->all_fields[$layout_def['column_key']];
        } elseif (!empty($layout_def['fields'])) {
            $field_def = $layout_def['fields'];
        }
        $record = '';
        if ($layout_def['table_key'] == 'self' && isset($layout_def['fields']['PRIMARYID'])) {
            $record = $layout_def['fields']['PRIMARYID'];
        } elseif (isset($layout_def['fields'][strtoupper($layout_def['table_alias'] . '_id')])) {
            $record = $layout_def['fields'][strtoupper($layout_def['table_alias'] . '_id')];
        }
        if (!empty($record)) {
            $field_name = $layout_def['name'];
            $module = $field_def['module'];

            $div_id = htmlspecialchars($module . "&$record&$field_name", ENT_COMPAT);
            return <<<HTML
<div id="{$div_id}">{$display}</div>
HTML;
        } else {
            return $display;
        }
    }

    public function displayListPlain($layout_def)
    {
        $value = parent::displayListPlain($layout_def);
        $row_currency = $this->getCurrency($layout_def);
        $format_id = $row_currency['currency_id'];

        global $current_user;
        // when the group by function is empty, and we should show the user prefered currency, it should convert it
        if (empty($layout_def['group_function']) && $current_user->getPreference('currency_show_preferred')) {
            $user_currency = SugarCurrency::getUserLocaleCurrency();

            if ($user_currency->id != $row_currency['currency_id']) {
                $value = SugarCurrency::convertAmount($value, $row_currency['currency_id'], $user_currency->id);
                $format_id = $user_currency->id;
            }
        }

        return SugarCurrency::formatAmountUserLocale($value, $format_id);
    }

    /**
     * Get currency value for sidecar field
     *
     * @param array $layoutDef
     *
     * @return mixed
     */
    public function getFieldControllerData(array $layoutDef)
    {
        $value = $this->displayListPlain($layoutDef);

        return $value;
    }

    public function queryFilterEquals(&$layout_def)
    {
        $column = $this->_get_column_select($layout_def);
        $value = (float)unformat_number($layout_def['input_name0'] ?? '');

        $bottomInterval = $value - 0.01;
        $topInterval = $value + 0.01;

        $bottomCondition = '(' . $column . '>' . $GLOBALS['db']->quote($bottomInterval);
        $topCondition = $column . '<' . $GLOBALS['db']->quote($topInterval) . ')';

        return $bottomCondition . ' AND ' . $topCondition . ' ';
    }

    public function queryFilterNot_Equals(&$layout_def)
    {
        return $this->_get_column_select($layout_def) . '!=' . $GLOBALS['db']->quote(unformat_number($layout_def['input_name0'])) . "\n";
    }

    public function queryFilterGreater(&$layout_def)
    {
        return $this->_get_column_select($layout_def) . ' > ' . $GLOBALS['db']->quote(unformat_number($layout_def['input_name0'])) . "\n";
    }

    public function queryFilterLess(&$layout_def)
    {
        return $this->_get_column_select($layout_def) . ' < ' . $GLOBALS['db']->quote(unformat_number($layout_def['input_name0'])) . "\n";
    }

    public function queryFilterBetween(&$layout_def)
    {
        return $this->_get_column_select($layout_def) . ' > ' . $GLOBALS['db']->quote(unformat_number($layout_def['input_name0'])) . ' AND ' . $this->_get_column_select($layout_def) . ' < ' . $GLOBALS['db']->quote(unformat_number($layout_def['input_name1'])) . "\n";
    }

    public function queryFilterGreater_Equal(&$layout_def)
    {
        return $this->_get_column_select($layout_def) . ' >= ' . $GLOBALS['db']->quote(unformat_number($layout_def['input_name0'])) . "\n";
    }

    public function queryFilterLess_Equal(&$layout_def)
    {
        return $this->_get_column_select($layout_def) . ' <= ' . $GLOBALS['db']->quote(unformat_number($layout_def['input_name0'])) . "\n";
    }

    public function isSystemCurrency(&$layout_def)
    {
        if (strpos($layout_def['name'], '_usdoll') === false) {
            return false;
        } else {
            return true;
        }
    }

    public function querySelect(&$layout_def)
    {
        // add currency column to select
        $table = $this->getCurrencyIdTable($layout_def);
        if ($table) {
            return $this->_get_column_select($layout_def) . ' ' . $this->_get_column_alias($layout_def) . ' , ' . $table . '.currency_id ' . $this->getTruncatedColumnAlias($this->_get_column_alias($layout_def) . '_currency') . "\n";
        }
        return $this->_get_column_select($layout_def) . ' ' . $this->_get_column_alias($layout_def) . "\n";
    }

    public function queryGroupBy($layout_def)
    {
        // add currency column to group by
        $table = $this->getCurrencyIdTable($layout_def);
        if ($table) {
            return $this->_get_column_select($layout_def) . ' , ' . $table . ".currency_id \n";
        }
        return $this->_get_column_select($layout_def) . " \n";
    }

    /**
     * Returns table alias for currency id if id exists in stock table
     * @param array $layout_def layout definition
     * @return string|false table alias for currency id
     */
    public function getCurrencyIdTable($layoutDef)
    {
        $db = DBManagerFactory::getInstance();

        // We need to fetch the currency id as well
        if (!$this->isSystemCurrency($layoutDef) && empty($layoutDef['group_function'])) {
            if (!empty($layoutDef['table_alias'])) {
                $tableAlias = $layoutDef['table_alias'];
            } else {
                $tableAlias = '';
            }

            $realTable = '';
            if (!empty($this->reporter->all_fields[$layoutDef['column_key']]['real_table'])) {
                $realTable = $this->reporter->all_fields[$layoutDef['column_key']]['real_table'];
            }

            if (!empty($tableAlias)) {
                $tableColumns = $db->get_columns($realTable);
                $cstmTableColumns = [];
                if (preg_match('/.*?_cstm$/i', $realTable)) {
                    $cstmTableColumns = $tableColumns;
                    $realTable = str_replace('_cstm', '', $realTable);
                    $tableColumns = $db->get_columns($realTable);
                }
                return $this->getCurrencyIdTableAlias(
                    $tableAlias,
                    $tableColumns,
                    $cstmTableColumns
                );
            }
        }
        return false;
    }

    /**
     * Returns currency id table alias based on associated table
     * @param string $tableAlias
     * @param array $tableColumns
     * @param array $cstmTableColumns
     * @return string|false currency id table alias
     */
    public function getCurrencyIdTableAlias(string $tableAlias, array $tableColumns, array $cstmTableColumns)
    {
        if (!empty($cstmTableColumns) && isset($cstmTableColumns['currency_id'])) {
            return $tableAlias;
        } elseif (!empty($tableColumns) && isset($tableColumns['currency_id'])) {
            return str_replace('_cstm', '', $tableAlias);
        } else {
            return false;
        }
    }

    /**
     * Return currency for layout_def
     * @param $layout_def mixed
     * @return array Array with currency symbol and currency ID
     */
    protected function getCurrency($layout_def)
    {
        $currency_id = false;
        $currency_symbol = false;
        if (isset($layout_def['currency_symbol']) && isset($layout_def['currency_id'])) {
            $currency_symbol = $layout_def['currency_symbol'];
            $currency_id = $layout_def['currency_id'];
        } else {
            $key = strtoupper($layout_def['varname'] ?? $this->_get_column_alias($layout_def));
            if ($this->isSystemCurrency($layout_def)) {
                $currency_id = '-99';
            } elseif (isset($layout_def['fields'][$key . '_CURRENCY'])) {
                $currency_id = $layout_def['fields'][$key . '_CURRENCY'];
            } elseif (isset($layout_def['fields'][$this->getTruncatedColumnAlias($this->_get_column_alias($layout_def) . '_currency')])) {
                $currency_id = $layout_def['fields'][$this->getTruncatedColumnAlias($this->_get_column_alias($layout_def) . '_currency')];
            }
            if ($currency_id) {
                $currency = BeanFactory::getBean('Currencies', $currency_id);
                if (!empty($currency->symbol)) {
                    $currency_symbol = $currency->symbol;
                }
            }
        }
        return ['currency_symbol' => $currency_symbol, 'currency_id' => $currency_id];
    }
}
