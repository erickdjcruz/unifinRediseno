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

namespace Sugarcrm\Sugarcrm\PushNotification;

/**
 * Service factory for Push Notification.
 */
class ServiceFactory
{
    /**
     * Instantiated service
     *
     * @var Service
     */
    private static $service;

    /**
     * Logger instnace
     *
     * @var Log
     */
    private static $log;

    /**
     * Instantiates the service
     *
     * @return mixed
     */
    public static function getService()
    {
        if (!isset(self::$service)) {
            self::$service = false;
            $config = \SugarConfig::getInstance()->get('push_notification');

            if ($config && !empty($config['enabled']) && !empty($config['service_provider'])) {
                $serviceClass = __NAMESPACE__ . '\\' .
                    $config['service_provider'] . '\\' . $config['service_provider'];
                $log = self::$log ?? \LoggerManager::getLogger();

                if (class_exists($serviceClass)) {
                    try {
                        self::$service = new $serviceClass();
                    } catch (\Throwable $t) {
                        $log->error('push notification: ' . $t->getMessage());
                    }
                } else {
                    $log->error("push notification: service class '$serviceClass' doesn't exist.");
                }
            }
        }

        return self::$service;
    }

    /**
     * Sets logger.
     *
     * @param \LoggerManager $log
     */
    public static function setLogger($log)
    {
        self::$log = $log;
    }
}
