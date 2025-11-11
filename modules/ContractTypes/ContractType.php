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
 * Description: The primary Function of this file is to manage all the data
 * used by other files in this nodule. It should extend the SugarBean which implements
 * all the basic database operations. Any custom behaviors can be implemented here by
 * implementing functions available in the SugarBean.
 ********************************************************************************/
class ContractType extends SugarBean
{
    public $id;
    public $date_entered;
    public $created_by;
    public $date_modified;
    public $modified_by;
    public $deleted;
    public $modified_user_id;

    public $name;
    public $list_order;
    /* End field definitions*/

    /* variable $table_name is used by SugarBean and methods in this file to constructs queries
     * set this variables value to the table associated with this bean.
     */
    public $table_name = 'contract_types';

    /*This  variable overrides the object_name variable in SugarBean, wher it has a value of null.*/
    public $object_name = 'ContractType';

    /**/
    public $module_dir = 'ContractTypes';

    /* This is a legacy variable, set its value to true for new modules*/
    public $new_schema = true;

    // This is used to retrieve related fields from form posts.
    public $relationship_fields = [];

    public $required_fields = [];


    /*This bean's constructor*/
    public function __construct()
    {
        parent::__construct();
        $this->disable_row_level_security = true;
    }

    /* This method should return the summary text which is used to build the bread crumb navigation*/
    /* Generally from this method you would return value of a field that is required and is of type string*/
    public function get_summary_text()
    {
        return "$this->name";
    }

    /**
     * Returns next list order
     * @return int Next list order
     */
    public function get_next_list_order()
    {
        $retval = 1;
        $query = "SELECT MAX(list_order) AS max_list_order FROM `$this->table_name` WHERE list_order IS NOT NULL AND deleted=0";
        $result = $this->db->query($query, false);
        $row = $this->db->fetchByAssoc($result);

        if (!empty($row['max_list_order'])) {
            $retval += intval($row['max_list_order']);
        }

        return $retval;
    }

    public function get_contractTypes($add_blank = false)
    {
        $query = 'select id,name,list_order from contract_types where deleted = 0 order by list_order ';
        $result = $this->db->query($query, false);
        $list = [];
        if ($add_blank) {
            $list[''] = '';
        }
        while (($row = $this->db->fetchByAssoc($result)) != null) {
            $list[$row['id']] = $row['name'];
        }
        return $list;
    }

    /**
     * @inheritdoc
     */
    public function save($check_notify = false)
    {
        if (trim($this->list_order) === '' || is_null($this->list_order)) {
            $this->list_order = $this->get_next_list_order();
        }
        parent::save($check_notify);
    }
}
