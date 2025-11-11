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

namespace Sugarcrm\Sugarcrm\Visibility\Portal;

use Sugarcrm\Sugarcrm\Portal\Factory as PortalFactory;

class Dashboards extends Portal
{
    /**
     * @param \SugarQuery $query Sugar query.
     * @param array $options Query options.
     * @throws \SugarQueryException
     */
    public function addVisibilityQuery(\SugarQuery $query, array $options = [])
    {
        // This section of code is a portion of the code referred
        // to as Critical Control Software under the End User
        // License Agreement.  Neither the Company nor the Users
        // may modify any portion of the Critical Control Software.
        if (PortalFactory::getInstance('Settings')->isServe()) {
            $serveHomeDashboardId = '0ca2d773-0bb3-4bf3-ae43-68569968af57'; // Serve Home
            $query->where()->equals($options['table_alias'] . '.id', $serveHomeDashboardId);
        } else {
            // If you do not have Serve, you don't get dashboards in your Portal
            // (the default Home view is NOT actually a dashboard)
            $query->where()->addRaw('1 = 0');
        }
        //END REQUIRED CODE DO NOT MODIFY
    }
}
