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

class SugarFieldText extends SugarFieldBase
{
    public function getDetailViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex)
    {
        $displayParams['nl2br'] = true;
        $displayParams['htmlescape'] = true;
        $displayParams['url2html'] = true;
        return parent::getDetailViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex);
    }

    public function getWirelessEditViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex)
    {
        $displayParams['nl2br'] = true;
        $displayParams['url2html'] = true;
        return parent::getWirelessEditViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex);
    }

    /**
     * Validates submitted data
     * @param SugarBean $bean
     * @param array $params
     * @param string $field
     * @param array $properties
     * @return boolean
     */
    public function apiValidate(SugarBean $bean, array $params, $field, $properties)
    {
        return $this->apiValidateFieldSize($bean, $params, $field, $properties);
    }
}
