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
class Version extends SugarBean
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
    public $file_version;
    public $db_version;
    public $table_name = 'versions';
    public $module_dir = 'Versions';
    public $object_name = 'Version';

    public $new_schema = true;

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = [];


    public function __construct()
    {
        parent::__construct();
        $this->team_id = 1; // make the item globally accessible
        $this->disable_row_level_security = true;
    }


    /**
     * builds a generic search based on the query string using or
     * do not include any $this-> because this is called on without having the class instantiated
     */
    public function build_generic_where_clause($the_query_string)
    {
        $where_clauses = [];
        $the_query_string = addslashes($the_query_string);
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


    public function is_expected_version($expected_version)
    {
        foreach ($expected_version as $name => $val) {
            if ($this->$name != $val) {
                return false;
            }
        }
        return true;
    }

    /**
     * Updates the version info based on the information provided
     */
    public function mark_upgraded($name, $dbVersion, $fileVersion)
    {
        $query = "DELETE FROM versions WHERE name='$name'";
        $GLOBALS['db']->query($query);
        $version = BeanFactory::newBean('Versions');
        $version->name = $name;
        $version->file_version = $fileVersion;
        $version->db_version = $dbVersion;
        $version->save();
    }

    public function get_profile()
    {
        return ['name' => $this->name, 'file_version' => $this->file_version, 'db_version' => $this->db_version];
    }
}
