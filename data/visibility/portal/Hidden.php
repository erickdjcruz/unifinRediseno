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

/**
 * Module is not visible in the portal
 */
class Hidden extends Portal
{
    public function addVisibilityQuery(\SugarQuery $query, array $options = [])
    {
        $query->where()->addRaw('1 = 0');
    }
}
