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

use Doctrine\DBAL\Connection;

/**
 * Data access class for the product_template table
 */
class ProductTemplate extends SugarBean
{
    // Stored fields
    public $id;
    public $deleted;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $created_by;
    public $created_by_name;
    public $modified_by_name;

    public $name;
    public $description;
    public $vendor_part_num;
    public $cost_price;
    public $discount_price;
    public $list_price;
    public $list_usdollar;
    public $discount_usdollar;
    public $cost_usdollar;
    public $currency_id;
    public $base_rate;
    public $mft_part_num;
    public $status;
    public $date_available;
    public $weight;
    public $qty_in_stock;
    public $website;
    public $tax_class;
    public $support_name;
    public $support_description;
    public $support_contact;
    public $support_term;
    public $pricing_formula;
    public $pricing_factor;
    public $currency_symbol;
    public $default_currency_symbol;
    public $tax_class_name;
    public $team_id;


    // These are for related fields
    public $type_name;
    public $type_id;
    public $manufacturer_name;
    public $manufacturer_id;
    public $category_name;
    public $category_id;


    public $parent_node_id;
    public $node_id;
    public $parent_name;
    public $type;
    public $default_tree_type;     //specified in save_branch function
    public $category_tree_table = 'category_tree';

    public $table_name = 'product_templates';
    public $rel_manufacturers = 'manufacturers';
    public $rel_types = 'product_types';
    public $rel_categories = 'product_categories';
    public $module_dir = 'ProductTemplates';
    public $object_name = 'ProductTemplate';

    public $new_schema = true;

    public $importable = true;
    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = [
        'manufacturer_name'
        , 'parent_node_id'
        , 'parent_name'
        , 'node_id'
        , 'type',
    ];

    public function __construct()
    {
        parent::__construct();

        $currency = BeanFactory::newBean('Currencies');
        $this->default_currency_symbol = $currency->getDefaultCurrencySymbol();
    }

    public function get_summary_text()
    {
        return "$this->name";
    }

    /**
     * @param string $product_template_id
     * @deprecated
     */
    public function clear_note_product_template_relationship($product_template_id)
    {
        $GLOBALS['log']->deprecated('ProductTemplate::clear_note_product_template_relationship() has been deprecated in 7.8');
        $query = sprintf(
            "UPDATE notes SET parent_id='', parent_type='' WHERE parent_id = %s AND deleted = 0",
            $this->db->quoted($product_template_id)
        );
        $this->db->query($query, true, 'Error clearing note to product_template relationship: ');
    }

    public function fill_in_additional_list_fields()
    {
        $this->fill_in_additional_detail_fields();
    }

    public function fill_in_additional_detail_fields()
    {
        global $app_list_strings;
        global $locale;
        global $sugar_config;
        // this is for quotes quicksearching a product. json_server does not make app_list_strings available
        // by default. If this code were added to json_server it would increase each call all the time
        if (empty($app_list_strings)) {
            if (isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '') {
                $current_language = $_SESSION['authenticated_user_language'];
            } else {
                $current_language = $sugar_config['default_language'];
            }
            $GLOBALS['log']->debug('current_language is: ' . $current_language);

            //set module and application string arrays based upon selected language
            $app_list_strings = return_app_list_strings_language($current_language);
        }


        $currency = BeanFactory::getBean('Currencies', $this->currency_id);
        if ($currency->id != $this->currency_id || $currency->deleted == 1) {
            $this->cost_price = $this->cost_usdollar;
            $this->discount_price = $this->discount_usdollar;
            $this->list_price = $this->list_usdollar;
            $this->currency_id = $currency->id;
            $this->base_rate = $currency->conversion_rate;
        }

        if (isset($this->currency_id) && !empty($this->currency_id)) {
            $currency->retrieve($this->currency_id);
            if ($currency->deleted != 1) {
                $this->currency_symbol = $currency->symbol;
            }
        }

        $this->tax_class_name = (!empty($this->tax_class) && !empty($app_list_strings['tax_class_dom'][$this->tax_class])) ? $app_list_strings['tax_class_dom'][$this->tax_class] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }

    /**
     * @param array $fromid
     * @param string $toid
     * @deprecated
     *
     */
    public function update_currency_id($fromid, $toid)
    {
        $GLOBALS['log']->deprecated('ProductTemplate::update_currency_id() has been deprecated in 7.8');
        $currency = BeanFactory::getBean('Currencies', $toid);
        if (empty($fromid)) {
            return;
        }

        $sql = <<<SQL
SELECT cost_price, list_price, discount_price, id FROM {$this->table_name} WHERE currency_id IN(?) AND deleted=0 
SQL;

        $rows = $this->db->getConnection()
            ->executeQuery(
                $sql,
                [$fromid],
                [Connection::PARAM_STR_ARRAY]
            );
        foreach ($rows->iterateAssociative() as $row) {
            $this->db->getConnection()
                ->update(
                    $this->table_name,
                    [
                        'currency_id' => $currency->id,
                        'cost_usdollar' => $currency->convertToDollar($row['cost_price']),
                        'list_usdollar' => $currency->convertToDollar($row['list_price']),
                        'discount_usdollar' => $currency->convertToDollar($row['discount_price']),
                    ],
                    ['id' => $row['id']]
                );
        }
    }

    public function get_list_view_data($filter_fields = [])
    {
        global $app_list_strings;

        $temp_array = parent::get_list_view_data();
        $temp_array['NAME'] = (($this->name == '') ? '<em>blank</em>' : $this->name);
        $temp_array['STATUS'] = !empty($this->status) ? $app_list_strings['product_template_status_dom'][$this->status] : '';
        $temp_array['TAX_CLASS_NAME'] = !empty($this->tax_class) ? $app_list_strings['tax_class_dom'][$this->tax_class] : '';
        $temp_array['PRICING_FORMULA_NAME'] = !empty($this->pricing_formula) ? $app_list_strings['pricing_formula_dom'][$this->pricing_formula] : '';
        $temp_array['ENCODED_NAME'] = $this->name;
        $temp_array['URL'] = $this->website;
        $temp_array['CATEGORY'] = $this->category_id;
        $temp_array['CATEGORY_NAME'] = $this->category_name;
        $temp_array['TYPE_NAME'] = $this->type_name;
        $temp_array['QTY_IN_STOCK'] = $this->qty_in_stock;

        return $temp_array;
    }

    /**
     * builds a generic search based on the query string using or
     * do not include any $this-> because this is called on without having the class instantiated
     */
    public function build_generic_where_clause($the_query_string)
    {
        $where_clauses = [];
        $the_query_string = $GLOBALS['db']->quote($the_query_string);
        array_push($where_clauses, "name like '$the_query_string%'");
        if (is_numeric($the_query_string)) {
            array_push($where_clauses, "mft_part_num like '%$the_query_string%'");
            array_push($where_clauses, "vendor_part_num like '%$the_query_string%'");
        }

        $the_where = '';
        foreach ($where_clauses as $clause) {
            if ($the_where != '') {
                $the_where .= ' or ';
            }
            $the_where .= $clause;
        }


        return $the_where;
    }

    /**
     * This function calculates any requested discount from the various formulas
     */
    public function calculateDiscountPrice()
    {
        if (!empty($this->pricing_formula)
            || !empty($this->cost_price)
            || !empty($this->list_price)
            || !empty($this->discount_price)
            || !empty($this->pricing_factor)) {
            $formula = $this->getPriceFormula($this->pricing_formula);

            if ($formula) {
                $this->discount_price = $formula->calculate_price(
                    $this->cost_price,
                    $this->list_price,
                    $this->discount_price,
                    $this->pricing_factor
                );
            }
        }
    }

    /**
     * Utiltity method to get the Pricing Formual Class
     *
     * @param string $formula
     * @param bool|false $refresh
     * @return bool|Object
     */
    protected function getPriceFormula($formula, $refresh = false)
    {
        if (!isset($GLOBALS['price_formulas']) || $refresh) {
            SugarAutoLoader::load('modules/ProductTemplates/Formulas.php');
            refresh_price_formulas();
        }


        if (!isset($GLOBALS['price_formulas'][$formula])) {
            return false;
        }

        SugarAutoLoader::load($GLOBALS['price_formulas'][$formula]);
        return new $formula();
    }
}
