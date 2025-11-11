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


$used_aliases = [];
$alias_map = [];

class SugarWidgetReportField extends SugarWidgetField
{
    /**
     * Layout manager reporter attribute
     * @var SugarBean
     */
    protected $reporter;

    public function __construct(&$layout_manager)
    {
        parent::__construct($layout_manager);
        $this->reporter = $this->layout_manager->getAttribute('reporter');
    }

    public function getSubClass($layout_def)
    {
        if (!empty($layout_def['type'])) {
            if ($layout_def['type'] == 'time') {
                $layout_def['widget_class'] = 'Fielddate';
            } else {
                $layout_def['widget_class'] = 'Field' . $layout_def['type'];
            }
            return $this->layout_manager->getClassFromWidgetDef($layout_def);
        } else {
            return $this;
        }
    }

    /**
     * Get field data for sidecar
     *
     * @param array $layoutDef
     *
     * @return mixed
     */
    public function getSidecarFieldData(array $layout_def)
    {
        $obj = $this->getSubClass($layout_def);

        return $obj->getFieldControllerData($layout_def);
    }

    public function display(array $layout_def)
    {
        $obj = $this->getSubClass($layout_def);

        $context = $this->layout_manager->getAttribute('context');
        $func_name = 'display' . $context;


        if (!empty($context) && method_exists($obj, $func_name)) {
            return $obj->$func_name($layout_def);
        } else {
            return 'display not found:' . $func_name;
        }
    }

    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    public function _get_column_select_special($layout_def)
    {
        $alias = '';
        if (!empty($layout_def['table_alias'])) {
            $alias = $layout_def['table_alias'];
        }

        if ($layout_def['name'] == 'weighted_sum') {
            return sprintf(
                'SUM(%s * %s * 0.01)',
                $this->reporter->db->convert("$alias.probability", 'IFNULL', [0]),
                $this->reporter->db->convert("$alias.amount_usdollar", 'IFNULL', [0])
            );
        }
        if ($layout_def['name'] == 'weighted_amount') {
            return sprintf(
                'AVG(%s * %s * 0.01)',
                $this->reporter->db->convert("$alias.probability", 'IFNULL', [0]),
                $this->reporter->db->convert("$alias.amount_usdollar", 'IFNULL', [0])
            );
        }
    }

    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    public function _get_column_select($layout_def)
    {
        global $reportAlias;
        if (!isset($reportAlias)) {
            $reportAlias = [];
        }

        if (!empty($layout_def['table_alias'])) {
            $alias = $layout_def['table_alias'] . '.' . $layout_def['name'];
        } elseif (!empty($layout_def['name'])) {
            $alias = $layout_def['name'];
        } else {
            $alias = '*';
        }

        if (!empty($layout_def['group_function'])) {
            if ($layout_def['name'] == 'weighted_sum' || $layout_def['name'] == 'weighted_amount') {
                $alias = $this->_get_column_select_special($layout_def);
                $reportAlias[$alias] = $layout_def;
                return $alias;
            }

            // Use IFNULL only if it's not AVG aggregate
            // because it adds NULL rows to the count when it should not, thus getting wrong result
            if ($layout_def['group_function'] != 'avg') {
                $alias = $this->reporter->db->convert($alias, 'IFNULL', [0]);
            }

            // for a field with type='currency' conversion of values into a user-preferred currency
            if ($layout_def['type'] == 'currency' && strpos($layout_def['name'], '_usdoll') === false) {
                $currency = $this->reporter->currency_obj;
                $currency_alias = $layout_def['currency_alias'] ?? $currency->table_name;
                $query = $this->getCurrencyRateQuery($layout_def, $currency_alias);
                // We need to use convert() for AVG because of Oracle
                if ($layout_def['group_function'] != 'avg') {
                    $alias = "{$layout_def['group_function']}($alias/{$query})";
                } else {
                    $alias = $this->reporter->db->convert("$alias/$query", 'AVG');
                }
            } else {
                // We need to use convert() for AVG because of Oracle
                if ($layout_def['group_function'] != 'avg') {
                    $alias = "{$layout_def['group_function']}($alias)";
                } else {
                    $alias = $this->reporter->db->convert($alias, 'AVG');
                }
            }
        }

        $reportAlias[$alias] = $layout_def;
        return $alias;
    }

    public function querySelect(&$layout_def)
    {
        return $this->_get_column_select($layout_def) . ' ' . $this->_get_column_alias($layout_def) . "\n";
    }

    public function queryGroupBy($layout_def)
    {
        return $this->_get_column_select($layout_def) . " \n";
    }

    /**
     * Checks if base rate exists in report bean, returns def if it exists
     *
     * @param array $layoutDef
     * @return array
     */
    protected function getBaseRateDef(array $layoutDef): array
    {
        $baseRateDef = [];
        if (!empty($layoutDef['table_key'])) {
            $baseRateDef = $this->reporter->full_bean_list[$layoutDef['table_key']]
                ->field_defs['base_rate'] ?? [];
        }
        return $baseRateDef;
    }

    /**
     * Determines what table the base_rate field for the report bean lives
     *
     * @param array $baseRateDef the Report's base_rate field definition
     * @param array $layoutDef the Report column's definition
     * @return string
     */
    protected function getBaseRateTable(array $baseRateDef, array $layoutDef)
    {
        // Check whether the base_rate field is in the base table or the custom
        // table of the module
        $baseRateTable = $this->reporter->getTableFromField($layoutDef);
        if (isset($baseRateDef['source']) && $baseRateDef['source'] === 'custom_fields') {
            $baseRateTable .= '_cstm';
        }

        return $baseRateTable;
    }

    /**
     * Returns currency conversion rate query
     *
     * @param array $layoutDef
     * @param string $currencyAlias
     * @return string
     */
    protected function getCurrencyRateQuery(array $layoutDef, string $currencyAlias): string
    {
        // By default, use the rate from the currencies table
        $rate = $currencyAlias . '.conversion_rate';

        // If there is a base rate field in this module, use that instead
        $baseRateDef = $this->getBaseRateDef($layoutDef);
        if ($baseRateDef) {
            $rate = $this->getBaseRateTable($baseRateDef, $layoutDef) . '.base_rate';
        }

        return $this->reporter->db->convert(
            $rate,
            'IFNULL',
            [1]
        );
    }

    /**
     * Returns ORDER BY for given layout_def
     *
     * @param $layout_def
     * @return string
     */
    public function queryOrderBy($layout_def)
    {
        $field_def = [];
        if (!empty($this->reporter->all_fields[$layout_def['column_key']])) {
            $field_def = $this->reporter->all_fields[$layout_def['column_key']];
        }

        if (empty($layout_def['sort_dir']) || $layout_def['sort_dir'] == 'a') {
            $direction = ' ASC';
        } else {
            $direction = ' DESC';
        }

        $orderBy = [];

        if (!empty($field_def['sort_on'])) {
            $orderBy[] = $layout_def['table_alias'] . '.' . $field_def['sort_on'];
            if (!empty($field_def['sort_on2'])) {
                $orderBy[] = $layout_def['table_alias'] . '.' . $field_def['sort_on2'];
            }
        } else {
            $orderBy[] = $this->_get_column_alias($layout_def);
        }

        array_walk(
            $orderBy,
            function (&$order, $key, $direction) {
                $order = $order . $direction;
            },
            $direction
        );

        return implode(', ', $orderBy);
    }


    public function queryFilter($layout_def)
    {
        $method_name = 'queryFilter' . $layout_def['qualifier_name'];
        return $this->$method_name($layout_def);
    }

    public function displayHeaderCell($layout_def)
    {
        global $start_link_wrapper, $end_link_wrapper;


        // don't show sort links if name isn't defined
        $no_sort = $this->layout_manager->getAttribute('no_sort');
        if (empty($layout_def['name']) || !empty($no_sort) || !empty($layout_def['no_sort'])) {
            return $layout_def['label'];
        }


        $sort_by = '';
        if (!empty($layout_def['table_key']) && !empty($layout_def['name'])) {
            if (!empty($layout_def['group_function']) && $layout_def['group_function'] == 'count') {
                $sort_by = $layout_def['table_key'] . ':' . 'count';
            } else {
                $sort_by = $layout_def['table_key'] . ':' . $layout_def['name'];
                if (!empty($layout_def['column_function'])) {
                    $sort_by .= ':' . $layout_def['column_function'];
                } elseif (!empty($layout_def['group_function'])) {
                    $sort_by .= ':' . $layout_def['group_function'];
                }
            }
        } else {
            return $this->displayHeaderCellPlain($layout_def);
        }

        $start = empty($start_link_wrapper) ? '' : $start_link_wrapper;
        $end = empty($end_link_wrapper) ? '' : $end_link_wrapper;

        // unable to retrieve the vardef here, exclude columns of type clob/text from being sortable

        if (!in_array($layout_def['name'], ['description', 'account_description', 'lead_source_description', 'status_description', 'to_addrs', 'cc_addrs', 'bcc_addrs', 'work_log', 'objective', 'resolution'])) {
            $header_cell = '<a class="listViewThLinkS1" href="' . $start . $sort_by . $end . '">';
            $header_cell .= $this->displayHeaderCellPlain($layout_def);
            $header_cell .= ListView::getArrowUpDownStart($layout_def['sort'] ?? '');
            $header_cell .= ListView::getArrowUpDownEnd($layout_def['sort'] ?? '');
            $header_cell .= '</a>';
            return $header_cell;
        }

        return $this->displayHeaderCellPlain($layout_def);
    }

    public function query($layout_def)
    {
        $obj = $this->getSubClass($layout_def);

        $context = $this->layout_manager->getAttribute('context');
        $func_name = 'query' . $context;

        if (!empty($context) && method_exists($obj, $func_name)) {
            return $obj->$func_name($layout_def);
        } else {
            return '';
        }
    }

    // @codingStandardsIgnoreLine PSR2.Methods.MethodDeclaration.Underscore
    public function _get_column_alias($layout_def)
    {
        $alias_arr = [];

        if (!empty($layout_def['table_key']) && $layout_def['table_key'] == 'self' && !empty($layout_def['name']) && $layout_def['name'] == 'id') {
            return 'primaryid';
        }

        // Bug: 44605
        // this comment is being added to trigger the upgrade package
        if (!empty($layout_def['group_function']) && $layout_def['group_function'] == 'count') {
            return $layout_def['table_alias'] . '__count';
        }

        if (!empty($layout_def['table_alias'])) {
            array_push($alias_arr, $layout_def['table_alias']);
        }

        if (!empty($layout_def['group_function']) && $layout_def['group_function'] != 'weighted_amount' && $layout_def['group_function'] != 'weighted_sum') {
            array_push($alias_arr, $layout_def['group_function']);
        } elseif (!empty($layout_def['column_function'])) {
            array_push($alias_arr, $layout_def['column_function']);
        } elseif (!empty($layout_def['qualifier'])) {
            array_push($alias_arr, $layout_def['qualifier']);
        }

        if (!empty($layout_def['name'])) {
            array_push($alias_arr, $layout_def['name']);
        }

        global $used_aliases, $alias_map;

        $alias = strtolower(implode('_', $alias_arr));

        $short_alias = $this->getTruncatedColumnAlias($alias);

        if (empty($used_aliases[$short_alias])) {
            $alias_map[$alias] = $short_alias;
            $used_aliases[$short_alias] = 1;
            return $short_alias;
        } elseif (!empty($alias_map[$alias])) {
            return $alias_map[$alias];
        } else {
            $alias_map[$alias] = $short_alias . '_' . $used_aliases[$short_alias];
            $used_aliases[$short_alias]++;
            return $alias_map[$alias];
        }
    }

    public function queryFilterEmpty($layout_def)
    {
        $column = $this->_get_column_select($layout_def);
        return "(coalesce({$this->reporter->db->convert($column, 'length')}, 0) = 0)";
    }

    public function queryFilterIs($layout_def)
    {
        return '( ' . $this->_get_column_select($layout_def) . "='" . $GLOBALS['db']->quote($layout_def['input_name0']) . "')\n";
    }

    public function queryFilteris_not($layout_def)
    {
        return '( ' . $this->_get_column_select($layout_def) . "<>'" . $GLOBALS['db']->quote($layout_def['input_name0']) . "')\n";
    }

    public function queryFilterNot_Empty($layout_def)
    {
        $column = $this->_get_column_select($layout_def);
        return "(coalesce({$this->reporter->db->convert($column, 'length')}, 0) <> 0)";
    }

    protected function getInputValue($layout_def)
    {
        $input_name0 = $layout_def['input_name0'];
        if (is_array($layout_def['input_name0'])) {
            $input_name0 = $layout_def['input_name0'][0];
        }
        return $input_name0;
    }
}
