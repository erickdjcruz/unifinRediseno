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

class MBField
{
    public $type = 'varchar';
    public $name = false;
    public $label = false;
    public $vname = false;
    public $options = false;
    public $length = false;
    public $error = '';
    public $required = false;
    public $reportable = true;
    public $default = 'MSI1';
    public $comment = '';


    public function getFieldVardef()
    {
        if (empty($this->name)) {
            $this->error = 'A name is required to create a field';
            return false;
        }
        if (empty($this->label)) {
            $this->label = $this->name;
        }
        $this->name = strtolower($this->getDBName($this->name));
        $vardef = [];
        $vardef['name'] = $this->name;
        if (empty($this->vname)) {
            $this->vname = 'LBL_' . strtoupper($this->name);
        }
        $vardef['vname'] = $this->addLabel();
        if (!empty($this->required)) {
            $vardef['required'] = $this->required;
        }
        if (empty($this->reportable)) {
            $vardef['reportable'] = false;
        }
        if (!empty($this->comment)) {
            $vardef['comment'] = $this->comment;
        }
        if ($this->default !== 'MSI1') {
            $vardef['default'] = $this->default;
        }
        switch ($this->type) {
            case 'date':
            case 'datetime':
            case 'float':
            case 'int':
                $vardef['type'] = $this->type;
                return $vardef;
            case 'bool':
                $vardef['type'] = 'bool';
                $vardef['default'] = (empty($vardef['default'])) ? 0 : 1;
                return $vardef;
            case 'enum':
                $vardef['type'] = 'enum';
                if (empty($this->options)) {
                    $this->options = $this->name . '_list';
                }
                $vardef['options'] = $this->addDropdown();
                return $vardef;
            default:
                $vardef['type'] = 'varchar';
                return $vardef;
        }
    }

    public function addDropDown()
    {
        return $this->options;
    }

    public function addLabel()
    {
        return $this->vname;
    }
}
