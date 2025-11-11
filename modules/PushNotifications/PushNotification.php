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

use Sugarcrm\Sugarcrm\PushNotification\ServiceFactory;

class PushNotification extends Basic
{
    /**
     * Supported mobile platforms.
     *
     * @var array
     */
    public static $PLATFORMS = ['android', 'ios'];

    public $module_dir = 'PushNotifications';
    public $module_name = 'PushNotifications';
    public $object_name = 'PushNotification';
    public $table_name = 'push_notifications';
    public $importable = false;

    /**
     * Sends this notification.
     *
     * @return bool
     */
    public function send(): bool
    {
        $service = $this->getService();
        $user = $this->getAssignedUser();

        if ($service && $user) {
            $extraData = $this->extra_data ? json_decode($this->extra_data, true) : [];

            $data = [
                'notification_type' => $this->notification_type,
                'module_name' => $this->parent_type,
                'record_id' => $this->parent_id,
                'extra_data' => isset($extraData['data']) ? json_encode($extraData['data']) : '',
            ];

            $message = [
                'title' => $this->name,
                'body' => $this->description,
                'data' => $data,
            ];

            foreach (self::$PLATFORMS as $platform) {
                if (!empty($extraData[$platform])) {
                    $message[$platform] = $extraData[$platform];
                }
            }

            return $service->send(
                [$user->id],
                $message
            );
        }

        return false;
    }

    /**
     * Gets push notification service.
     *
     * @return mixed
     */
    protected function getService()
    {
        return ServiceFactory::getService();
    }

    /**
     * Gets assigned user.
     *
     * @return SugarBean|NULL
     */
    protected function getAssignedUser()
    {
        return BeanFactory::retrieveBean('Users', $this->assigned_user_id);
    }
}
