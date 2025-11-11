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

namespace Sugarcrm\Sugarcrm\PubSub\Buffer;

interface PushSubscriptionBufferInterface
{
    /**
     * Clears the buffer and returns the events. The keys are webhook URLs. The
     * values are lists of events to send to that webhook.
     *
     * <code>
     * [
     *     'https://webhook.service.sugarcrm.com/' => [
     *         [
     *             'timestamp' => '2022-11-17T02:51:07Z',
     *             'site_url' => 'https://cloudsi.sugarondemand.com',
     *             'data' => [
     *                 'module_name' => 'Calls',
     *                 'id' => 'b6df61f0-ca62-4c62-96a6-c4c2a7ea77c6',
     *                 'change_type' => 'after_save',
     *                 'arguments' => [...],
     *             ],
     *         ],
     *         ...
     *     ],
     *     'https://webhook.k8s-usw2.dev.sugar.build/' => [
     *         ...
     *     ],
     * ]
     * </code>
     *
     * @return array
     */
    public function flushEvents(): array;

    /**
     * Reports whether or not events have been in the buffer for too long.
     *
     * @return bool
     */
    public function isExpired(): bool;

    /**
     * Reports whether or not the buffer is full.
     *
     * @return bool
     */
    public function isFull(): bool;

    /**
     * Writes events to the buffer.
     *
     * @param string $url The webhook URL.
     * @param array $events The list of events to send.
     *
     * @return void
     */
    public function writeEvents(string $url, array $events): void;
}
