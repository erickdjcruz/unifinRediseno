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

class TemplateParent extends TemplateEnum
{
    public $max_size = 36;
    public $type = 'parent';

    public function get_field_def()
    {
        $def = parent::get_field_def();
        $def['type_name'] = 'parent_type';
        $def['id_name'] = 'parent_id';
        $def['parent_type'] = 'record_type_display';
        $def['source'] = 'non-db';
        $def['studio'] = 'visible';
        return $def;
    }

    public function delete($df)
    {
        parent::delete($df);
        //currency id
        $parent_type = new TemplateText();
        $parent_type->name = 'parent_type';
        $parent_type->delete($df);

        $parent_id = new TemplateId();
        $parent_id->name = 'parent_id';
        $parent_id->delete($df);
    }

    public function save($df)
    {
        $this->ext1 = 'parent_type_display';
        $this->name = 'parent_name';
        $this->default_value = '';
        parent::save($df); // always save because we may have updates

        foreach ($this->createFields() as $field) {
            $field->save($df);
        }
    }

    /**
     * @param DynamicField $dynamicField
     * @return array
     */
    public function getContainedDefs(DynamicField $dynamicField): array
    {
        return array_map(function (TemplateField $tf): array {
            return $tf->get_field_def();
        }, $this->createFields());
    }

    /**
     * @return TemplateField[]
     */
    private function createFields(): array
    {
        $parent_type = new TemplateParentType();
        $parent_type->name = 'parent_type';
        $parent_type->vname = 'LBL_PARENT_TYPE';
        $parent_type->label = $parent_type->vname;
        $parent_type->len = 255;
        $parent_type->importable = $this->importable;

        $parent_id = new TemplateId();
        $parent_id->name = 'parent_id';
        $parent_id->vname = 'LBL_PARENT_ID';
        $parent_id->label = $parent_id->vname;
        $parent_id->len = 36;
        $parent_id->importable = $this->importable;

        return [$parent_type, $parent_id];
    }

    public function get_db_add_alter_table($table)
    {
        return '';
    }

    /**
     * mysql requires the datatype caluse in the alter statment.it will be no-op anyway.
     */
    public function get_db_modify_alter_table($table)
    {
        return '';
    }
}
