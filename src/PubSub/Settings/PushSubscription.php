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

namespace Sugarcrm\Sugarcrm\PubSub\Settings;

use SugarConfig;

final class PushSubscription
{
    /**
     * The configuration repository.
     */
    private SugarConfig $config;

    /**
     * Default settings. The keys match settings from SugarConfig found under
     * "pubsub.push".
     */
    private array $defaults = [
        /**
         * @var int Maximum number of events the buffer can hold before it must
         *          be flushed.
         */
        'buffer_capacity' => 20,

        /**
         * @var int Maximum number of seconds that a events are held in the
         *          buffer before it must be flushed.
         */
        'buffer_timeout' => 30,

        /**
         * @var int Maximum number of retries for a failed request.
         */
        'max_retries' => 2,

        /**
         * @var float Timeout (in seconds) for a request.
         */
        'request_timeout' => 30.0,
    ];

    /**
     * Access settings found under "pubsub.push".
     *
     * @param SugarConfig $config The configuration repository.
     */
    public function __construct(SugarConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Get a setting under "pubsub.push". For example,
     * "pubsub.push.request_timeout" is the request timeout used by HTTP clients
     * when sending events to Pub/Sub push subscribers. The default value is
     * returned when the setting is undefined.
     *
     * @param string $key The name of the setting.
     *
     * @return mixed The value of the setting or its default value.
     */
    public function getSetting(string $key)
    {
        return $this->config->get("pubsub.push.{$key}", $this->getDefaultValue($key));
    }

    /**
     * Get the default value for a setting.
     *
     * @param string $key The name of the setting.
     *
     * @return mixed The default value or null.
     */
    private function getDefaultValue(string $key)
    {
        return $this->defaults[$key] ?? null;
    }
}
