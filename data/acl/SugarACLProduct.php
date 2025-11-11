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

/**
 * This class is used to enforce ACLs for modules related to ProductTemplates.
 */
class SugarACLProduct extends SugarACLStrategy
{
    protected $aclModule = 'ProductTemplates';

    /**
     * @inheritdoc
     */
    public function checkAccess($module, $view, $context)
    {
        if ($view == 'team_security') {
            // Let the other modules decide
            return true;
        }

        if ($view === 'field') {
            // no field level check
            return true;
        }

        $aclBean = BeanFactory::getBean($this->aclModule);

        if (isset($context['bean'])) {
            $context['bean'] = $aclBean;
        }

        return $aclBean->ACLAccess($view, $context);
    }
}
