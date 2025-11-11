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

class ViewValidateUserField extends ViewValidateRelatedField
{
    public $vars = ['related'];
    public $related;

    /**
     * Respond with the field def if the field name is valid, and with an error message otherwise
     */
    public function display()
    {
        $userBean = BeanFactory::newBean('Users');
        $fieldDefs = $userBean->getFieldDefinitions();

        $validUserFieldNames = array_column(FormulaHelper::getValidUserFields($fieldDefs), 'name');

        if (in_array($this->related, $validUserFieldNames)) {
            echo(json_encode($fieldDefs[$this->related]));
        } else {
            echo(json_encode(translate('LBL_UNKNOWN_FIELD', 'ExpressionEngine') . ' : ' . $this->related));
        }
    }
}
