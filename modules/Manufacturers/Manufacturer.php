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
/*********************************************************************************
 * Description:
 ********************************************************************************/
// Manufacturer is used to store customer information.
class Manufacturer extends SugarBean
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
    public $status;

    public $table_name = 'manufacturers';
    public $rel_products = 'product_templates';

    public $object_name = 'Manufacturer';
    public $module_dir = 'Manufacturers';
    public $new_schema = true;

    public $importable = true;
    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = [];


    public function __construct()
    {

        parent::__construct();
        $this->disable_row_level_security = true;
    }


    public function get_summary_text()
    {
        return "$this->name";
    }

    public function get_manufacturers($add_blank = false, $status = 'Active')
    {
        $query = "SELECT id, name FROM $this->table_name where deleted=0 ";
        if ($status == 'Active') {
            $query .= " and status='Active' ";
        } elseif ($status == 'Inactive') {
            $query .= " and status='Inactive' ";
        } elseif ($status == 'All') {
        }
        $query .= ' order by list_order asc';
        $result = $this->db->query($query, false);
        $GLOBALS['log']->debug('get_manufacturers: result is ' . print_r($result, true));

        $list = [];
        if ($add_blank) {
            $list[''] = '';
        }
        // We have some data.
        while (($row = $this->db->fetchByAssoc($result)) != null) {
            $list[$row['id']] = $row['name'];
            $GLOBALS['log']->debug('row id is:' . $row['id']);
            $GLOBALS['log']->debug('row name is:' . $row['name']);
        }
        return $list;
    }

    public function save_relationship_changes($is_update, $exclude = [])
    {
    }

    public function clear_product_manufacturer_relationship($manufacturer_id)
    {
        $query = sprintf(
            "UPDATE %s SET manufacturer_id = '' WHERE manufacturer_id = %s AND deleted = 0",
            $this->rel_products,
            $this->db->quoted($manufacturer_id)
        );
        $this->db->query($query, true, 'Error clearing manufacturer to manufacturer relationship: ');
    }

    public function mark_relationships_deleted($id)
    {
        $this->clear_product_manufacturer_relationship($id);
    }

    public function fill_in_additional_list_fields()
    {
        $this->fill_in_additional_detail_fields();
    }

    public function fill_in_additional_detail_fields()
    {
    }

    public function get_list_view_data($filter_fields = [])
    {
        $temp_array = $this->get_list_view_array();
        $temp_array['ENCODED_NAME'] = $this->name;
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
     * Indicate this module implements ACL.
     * @param string $interface the interface name
     * @return bool
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
     *  override default behavior
     * {@inheritDoc}
     */
    public function isACLRoleEditable()
    {
        return false;
    }
}
