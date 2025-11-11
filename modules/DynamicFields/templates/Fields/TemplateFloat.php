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

class TemplateFloat extends TemplateRange
{
    public $type = 'float';
    public $default = null;
    public $default_value = null;
    public $len = '18';
    public $precision = '8';

    public function __construct()
    {
        parent::__construct();
        $this->vardef_map['precision'] = 'ext1';
        //$this->vardef_map['precision']='precision';
    }

    public function get_field_def()
    {
        $def = parent::get_field_def();
        $def['precision'] = isset($this->ext1) && is_numeric($this->ext1) ? (int)$this->ext1 : $this->precision;
        return $def;
    }

    public function get_db_type()
    {
        $precision = (!empty($this->precision)) ? $this->precision : 8;
        if (empty($this->len)) {
            return parent::get_db_type();
        }
        return ' ' . sprintf($GLOBALS['db']->getColumnType('decimal_tpl'), $this->len, $precision);
    }
}
