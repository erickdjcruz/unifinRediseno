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

use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;
use Sugarcrm\Sugarcrm\Security\InputValidation\Request;

class TemplateAutoIncrement extends TemplateRange
{
    public $max;
    public $type = 'autoincrement';
    public $auto_increment = true;
    public $disable_num_format = true;
    public $supports_unified_search = true;

    /**
     * @var int|null
     */
    public $len = null;
    /** @var array */
    public $validation;

    public function __construct()
    {
        parent::__construct();
        $this->vardef_map['autoinc_next'] = 'autoinc_next';
        $this->vardef_map['autoinc_start'] = 'autoinc_start';
        $this->vardef_map['auto_increment'] = 'auto_increment';

        $this->vardef_map['min'] = 'ext1';
        $this->vardef_map['max'] = 'ext2';
        $this->vardef_map['disable_num_format'] = 'ext3';
    }

    /**
     * Defines the custom HTML edit markup that defines the field in a display view/layout. Ex. editing a module's
     * record layout in Studio.
     */
    public function get_html_edit()
    {
        $this->prepare();
        return "<input type='text' name='" . $this->name . "' id='" . $this->name . "' title='{" . strtoupper($this->name) . "_HELP}' size='" . $this->size . "' maxlength='" . $this->len . "' value='{" . strtoupper($this->name) . "}'>";
    }

    /**
     * Determines and returns the metadata that defines this field. The resulting $vardefs object is what gets used
     * to populate the field in Studio as well as being saved to the DB.
     */
    public function get_field_def()
    {
        $vardef = parent::get_field_def();
        $vardef['auto_increment'] = $this->auto_increment;
        $vardef['dbType'] = 'int';
        $vardef['disable_num_format'] = $this->disable_num_format ?? $this->ext3;
        $vardef['importable'] = false;
        $vardef['readonly'] = true;

        $vardef['auto_increment_platform_options'] = ['unique' => true];

        $vardef['min'] = 1;
        $vardef['max'] = $this->max ?? $this->ext2;
        if ($vardef['min'] !== false || $vardef['max'] !== false) {
            $vardef['validation'] = [
                'type' => 'range',
                'min' => $vardef['min'],
                'max' => $vardef['max'],
            ];
        }

        $tablename = parent::getTableName();

        if (!empty($tablename)) {
            $db = $this->getDbObject();
            if ($db) {
                $next = $db->getAutoIncrement($tablename, $this->name);

                if ($this->autoinc_next > $next) {
                    $db->setAutoIncrementStart($tablename, $this->name, $this->autoinc_next);
                } else {
                    $this->autoinc_next = $vardef['autoinc_next'] = $next;
                }
            }
        }

        return $vardef;
    }

    public function getDbObject()
    {
        return DBManagerFactory::getInstance();
    }
}
