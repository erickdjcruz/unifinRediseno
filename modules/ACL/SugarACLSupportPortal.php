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
 * Kept class for backward compatibility only
 * @deprecated use SugarACLPortal instead
 */
class SugarACLSupportPortal extends SugarACLPortal
{
    public function __construct()
    {
        $msg = sprintf(
            '%s::%s is deprecated and will be removed in a future release.',
            self::class,
            __METHOD__
        );
        LoggerManager::getLogger()->deprecated($msg);
    }
}
