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


class SugarWidgetFieldDecimal extends SugarWidgetFieldInt
{
    public function displayListPlain($layout_def)
    {
        $val = parent::displayListPlain($layout_def);
        if ($val === '' || $val === null) {
            return '';
        }

        //Bug40995
        if (isset($layout_def['precision']) && $layout_def['precision'] != '') {
            return format_number($val, $layout_def['precision'], $layout_def['precision']);
        } else {
            //Bug40995
            return format_number($val, 2, 2);
        }
    }

    /**
     * Get decimal value for sidecar field
     *
     * @param array $layoutDef
     *
     * @return string
     */
    public function getFieldControllerData(array $layoutDef)
    {
        $value = $this->displayListPlain($layoutDef);

        return $value;
    }
}
