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

class TemplateEncrypt extends TemplateField
{
    public $type = 'encrypt';
    public $massupdate = 1;

    /**
     * {@inheritDoc}
     */
    public $len = 255;

    public function save($df)
    {
        $this->type = 'encrypt';
        $this->ext3 = 'varchar';
        parent::save($df);
    }

    public function get_field_def()
    {
        $vardef = parent::get_field_def();
        $vardef['dbType'] = $this->ext3;
        return $vardef;
    }

    /**
     * @inheritdoc
     */
    public function populateFromPost(Request $request = null)
    {
        if (!$request) {
            $request = InputValidation::getService();
        }

        parent::populateFromPost($request);
        $this->massupdate = !empty($_REQUEST['massupdate']);
    }
}
