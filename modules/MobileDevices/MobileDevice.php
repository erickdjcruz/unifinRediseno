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

/**
 * Class MobileDevice
 */
class MobileDevice extends Basic
{
    public $module_dir = 'MobileDevices';
    public $object_name = 'MobileDevice';
    public $table_name = 'mobile_devices';
    public $module_name = 'MobileDevices';
    public $importable = false;

    /**
     * @var bool | \Sugarcrm\Sugarcrm\PushNotification\Service
     */
    protected $service = false;

    /**
     * Designed to be called from Logic Hooks after a user logged out.
     * Sends user deactivation request to the Service
     */
    public function onLoggedOut(): void
    {
        $service = $this->getService();
        if ($service && $this->getCurrentUser()->hasRegisteredDevices()) {
            $result = $service->setActive($this->getCurrentUser()->id, false);
            if ($result === false) {
                $this->markDeletedAll($this->getCurrentUser()->id);
            }
        }
    }

    /**
     * Designed to be called from Logic Hooks after a user logged in.
     * Sends user activation request to the Service
     */
    public function onLoggedIn(): void
    {
        $service = $this->getService();
        if ($service && $this->getCurrentUser()->hasRegisteredDevices()) {
            $result = $service->setActive($this->getCurrentUser()->id, true);
            if ($result === false) {
                $this->markDeletedAll($this->getCurrentUser()->id);
            }
        }
    }

    /**
     * @param false $check_notify
     * @return string
     */
    public function save($check_notify = false)
    {
        global $current_user;

        if (empty($this->assigned_user_id)) {
            $this->assigned_user_id = $current_user->id;
        }

        // ensure uniqueness of assigned_user_id, device_platform and device_id combination
        // avoiding use of db unique key since we are using soft delete
        $id = $this->getIdOfSameCombo();
        if (!empty($id)) {
            $result = $this->relayUpdateRequest($this->device_id);
            if ($result === false) {
                parent::mark_deleted($id);
            } elseif ($result === true) {
                parent::mark_undeleted($id);
            }
            if ($result !== true) {
                return null;
            }
        }

        if (!$this->relayRegisterRequest()) {
            return null;
        }

        return parent::save(false);
    }

    /**
     * {@inheritDoc}
     */
    public function mark_deleted($id)
    {
        $service = $this->getService();
        if (!$service || $service->delete($this->device_platform, $this->device_id) === false) {
            return;
        }
        parent::mark_deleted($id);
    }

    /**
     * @return false|mixed|\Sugarcrm\Sugarcrm\PushNotification\Service
     */
    protected function getService()
    {
        if (empty($this->service)) {
            $this->service = ServiceFactory::getService();
        }
        return $this->service;
    }

    /**
     * Relays the register request to the SugarPush service
     * @return bool
     */
    protected function relayRegisterRequest(): bool
    {
        if ($service = $this->getService()) {
            return $service->register($this->device_platform, $this->device_id);
        }

        return false;
    }

    /**
     * Relays the update request to the SugarPush service
     */
    protected function relayUpdateRequest(string $oldId, string $newId = ''): ?bool
    {
        if ($service = $this->getService()) {
            return $service->update($this->device_platform, $oldId, $newId);
        }

        return null;
    }

    /**
     * @return string
     * @throws SugarQueryException
     */
    protected function getIdOfSameCombo(): string
    {
        $query = new SugarQuery();
        $query->select(['id']);
        $bean = BeanFactory::newBean('MobileDevices');

        $query->from($bean, ['team_security' => false, 'add_deleted' => true]);

        $query->where()->queryAnd()
            ->equals('assigned_user_id', $this->assigned_user_id)
            ->equals('device_id', $this->device_id)
            ->equals('device_platform', $this->device_platform);
        $query->limit(1);

        $rows = $query->execute();

        return empty($rows[0]['id']) ? '' : $rows[0]['id'];
    }

    protected function markDeletedAll(string $userId): void
    {
        $query = DBManagerFactory::getConnection()->createQueryBuilder();
        $query->update($this->getTableName())
            ->set('deleted', 1)
            ->where('assigned_user_id = ?')
            ->setParameter(0, $userId)
            ->executeStatement();
    }
}
