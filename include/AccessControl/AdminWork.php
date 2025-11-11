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

namespace Sugarcrm\Sugarcrm\AccessControl;

// This section of code is a portion of the code referred
// to as Critical Control Software under the End User
// License Agreement.  Neither the Company nor the Users
// may modify any portion of the Critical Control Software.


/**
 * Class AdminWork
 *
 * this class is for gaining full access control of Sugar objects
 *
 * instantiate an instance at the very beginning of the scope you want to turn access control off
 *
 * @package Sugarcrm\Sugarcrm\AccessControl
 */
class AdminWork
{
    /**
     * current admin work state
     * @var bool
     */
    protected $currentAdminWorkState = false;

    /**
     * set admin work to true
     * AdminWork constructor.
     */
    public function __construct()
    {
        // remember the current state
        $this->currentAdminWorkState = AccessControlManager::instance()->getAdminWork();
    }

    /**
     * end admin work, and reset back to original state
     */
    public function __destruct()
    {
        $this->endAdminWork();
    }

    /**
     * start admin work, take control everything
     */
    public function startAdminWork()
    {
        // set admin work, make it controls everything
        AccessControlManager::instance()->setAdminWork(true);
    }

    /**
     * end admin work, reset admin work to original state
     */
    public function endAdminWork()
    {
        AccessControlManager::instance()->setAdminWork($this->currentAdminWorkState);
    }

    /**
     * reset admin work
     */
    public function reset(bool $adminWork)
    {
        AccessControlManager::instance()->setAdminWork($adminWork);
    }
}
//END REQUIRED CODE DO NOT MODIFY
