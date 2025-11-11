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
 * ResourceObserver.php
 * This class serves as the base class for the notifier/observable pattern used
 * by the resource management framework.
 */
class ResourceObserver
{
    public $dieOnError;
    public $module;
    public $limit;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function notify($msg = '')
    {
        if ($this->dieOnError) {
            die($GLOBALS['app_strings']['ERROR_NOTIFY_OVERRIDE']);
        } else {
            echo($GLOBALS['app_strings']['ERROR_NOTIFY_OVERRIDE']);
        }
    }
}
