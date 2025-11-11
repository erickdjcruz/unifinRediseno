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

interface Service
{
    /**
     * Registers a user's device.
     *
     * @param string $platform The device's platform.
     * @param string $deviceId The device's ID.
     * @return bool
     */
    public function register(string $platform, string $deviceId): bool;

    /**
     * Updates a user's device.
     *
     * @param string $platform The device's platform.
     * @param string $oldDeviceId The device's old ID.
     * @param string $newDeviceId The device's new ID.
     * @return bool
     */
    public function update(string $platform, string $oldDeviceId, string $newDeviceId = ''): bool;

    /**
     * Removes a user's device.
     *
     * @param string $platform The device's platform.
     * @param string $deviceId The device's ID.
     * @return bool
     */
    public function delete(string $platform, string $deviceId): bool;

    /**
     * Activates/deactivates a user (makes possible/impossible to receive push notifications on user's devices)
     * Important: This must not block user login/logout processes:
     * - use short timeouts
     * - do not throw errors or exceptions
     */
    public function setActive(string $userId, bool $flag): bool;

    /**
     * Sends a message to users.
     *
     * @param array $userIds The user ids.
     * @param array $message The message to send. Options are:
     *
     * $message['title'] string The message title (required)
     * $message['body'] string The message body (required)
     * $message['data'] array Extra data to send (optional)
     * $message['android'] array Android specific attributes (optional)
     * $message['ios'] array IOS specific attributes (optional)
     *
     * @return bool
     */
    public function send(array $userIds, array $message): bool;
}
