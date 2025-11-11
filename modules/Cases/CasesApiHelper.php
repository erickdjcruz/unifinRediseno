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

use Sugarcrm\Sugarcrm\Portal\Factory as PortalFactory;

class CasesApiHelper extends SugarBeanApiHelper
{
    /**
     * This function sets source to 'Portal' for new Cases submitted via portal users.
     *
     * @param SugarBean $bean
     * @param array $submittedData
     * @param array $options
     * @return array
     */
    public function populateFromApi(SugarBean $bean, array $submittedData, array $options = [])
    {
        $data = parent::populateFromApi($bean, $submittedData, $options);

        //Only needed for Portal sessions
        $portalSession = PortalFactory::getInstance('Session');
        if ($portalSession->isActive()) {
            $bean->source = 'Portal';
        }

        return $data;
    }
}
