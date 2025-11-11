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

class TemplateActionButton extends TemplateField
{
    public $type = 'actionbutton';

    /**
     * {@inheritDoc}
     */
    public function get_field_def()
    {
        $def = parent::get_field_def();
        $def['studio'] = [
            'editField' => true,
            'recordview' => true,
            'previewview' => true,
            'recorddashletview' => true,
            'listview' => false,
            'wirelesseditview' => true,
            'wirelesslistview' => false,
            'wirelessdetailview' => true,
            'wireless_basic_search' => false,
            'wireless_advanced_search' => false,
        ];
        $def['type'] = 'actionbutton';
        $def['dbType'] = 'text';
        $def['source'] = 'non-db';
        $def['readonly'] = true;
        $def['size'] = 50;

        // As the data can get really big we need to inflate/deflate it
        if (isset($this->ext4)) {
            // Handle the possibility of Sugar calling this function before the ext4 field gets encoded/compressed
            $ext4 = @gzinflate(base64_decode($this->ext4));
            if ($ext4 !== false) {
                $this->ext4 = $ext4;
            }

            $def['options'] = $this->ext4;
        }

        return $def;
    }

    /**
     * {@inheritDoc}
     */
    public function save($df)
    {
        $this->ext4 = base64_encode(gzdeflate($this->ext4));

        parent::save($df);
    }
}
