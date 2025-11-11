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

namespace Sugarcrm\Sugarcrm\DocumentMerge;

class ServiceFactory
{
    /**
     * Instantiated service
     *
     * @var Service
     */
    private static $service;

    /**
     * Instantiate the Service
     */
    public static function getService()
    {
        if (!isset(self::$service)) {
            self::$service = false;

            $log = \LoggerManager::getLogger();

            $serviceClass = __NAMESPACE__ . '\\Service';
            if (class_exists($serviceClass)) {
                try {
                    self::$service = new $serviceClass();
                } catch (\Throwable $t) {
                    $log->error('Document Merge: ' . $t->getMessage());
                }
            } else {
                $log->error("Document Merge: service class '$serviceClass' doesn't exist.");
            }
        }

        return self::$service;
    }
}
