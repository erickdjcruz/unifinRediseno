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

use Sugarcrm\Sugarcrm\DependencyInjection\Container;

/**
 * Class PubSub_ModuleEvent_PushSub
 *
 * Sugar Connect subscribes to modules for the purpose of change data capture
 * using the Connections services. Each subscription is to a single module and
 * last for 7 days. A notification is sent to each subscription's webhook when a
 * record from the specified module is created, updated, deleted, or modified in
 * some way. A subscription that is not updated (i.e., touched) within 7 days
 * will expire and stop sending notifications. We don't want to perpetually send
 * notifications in the event of a lost subscriber.
 */
final class PubSub_ModuleEvent_PushSub extends Basic
{
    /**
     * Read-only field. Subscriptions last 7 days. Updating a subscription
     * extends the subscription another 7 days.
     */
    public string $expiration_date = '';

    /**
     * Subscriptions should never be imported.
     *
     * @inheritdoc
     */
    public $importable = false;

    /**
     * @inheritdoc
     */
    public $module_dir = 'PubSub_ModuleEvent_PushSubs';

    /**
     * @inheritdoc
     */
    public bool $new_schema = true;

    /**
     * @inheritdoc
     */
    public $object_name = 'PubSub_ModuleEvent_PushSub';

    /**
     * @inheritdoc
     */
    public $table_name = 'pubsub_moduleevent_pushsubs';

    /**
     * Send notifications regarding this module.
     */
    public string $target_module = '';

    /**
     * An arbitrary string, defined by the subscription, that is delivered to
     * the destination with each notification. The subscriber should not share
     * this token with anyone and may use it to assert that the notification
     * came from a trusted source.
     */
    public string $token = '';

    /**
     * Send notifications to this webhook. It must be an HTTP endpoint.
     */
    public string $webhook_url = '';

    /**
     * Only load the subscriptions once per run-time.
     */
    private static ?array $cache = null;

    /**
     * Find all subscriptions by module that have not expired.
     *
     * @param string $module The module name.
     *
     * @return array List of active subscriptions.
     */
    public static function findActiveSubscriptionsByModule(string $module): array
    {
        // Disable run-time caching.
        if (defined('SUGAR_PHPUNIT_RUNNER')) {
            static::$cache = null;
        }

        // Load all active subscriptions once.
        if (is_null(static::$cache)) {
            static::$cache = [];

            $container = Container::getInstance();
            $timedate = $container->get(TimeDate::class);
            $seed = BeanFactory::newBean('PubSub_ModuleEvent_PushSubs');

            $q = new SugarQuery();
            $q->from($seed, ['team_security' => false]);
            $q->where()->gt('expiration_date', $timedate->nowDb());

            $subs = $seed->fetchFromQuery($q);

            foreach ($subs as $id => $sub) {
                if (!array_key_exists($sub->target_module, static::$cache)) {
                    static::$cache[$sub->target_module] = [];
                }

                static::$cache[$sub->target_module][$id] = $sub;
            }
        }

        if (!array_key_exists($module, static::$cache)) {
            return [];
        }

        return static::$cache[$module];
    }

    /**
     * Restrict access to admins only.
     *
     * @param string $interface Interface name.
     *
     * @return bool TRUE if the bean implements the interface.
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }

        return false;
    }

    /**
     * Multiple subscriptions for the same module by the same subscriber are not
     * permitted. Delete subscriptions matching this one.
     *
     * @return int Number of duplicates deleted.
     */
    public function deleteDuplicates(): int
    {
        $duplicates = $this->findDuplicates();

        if (empty($duplicates['records'])) {
            return 0;
        }

        $beans = $duplicates['records'];

        foreach ($beans as $bean) {
            $bean->mark_deleted($bean->id);
        }

        return safeCount($beans);
    }

    /**
     * @inheritdoc
     */
    public function findDuplicates()
    {
        $seed = $this->getCleanCopy();

        $q = new SugarQuery();
        $q->from($seed, ['team_security' => false]);
        $q->where()->equals('target_module', $this->target_module);
        $q->where()->equals('webhook_url', $this->webhook_url);

        if (!empty($this->id)) {
            $q->where()->notEquals('id', $this->id);
        }

        return [
            'records' => $seed->fetchFromQuery($q),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRecordName()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function get_summary_text()
    {
        return $this->getRecordName();
    }

    /**
     * Physically delete the subscription.
     *
     * @return void
     */
    protected function doMarkDeleted(): void
    {
        // Mimic soft deletion while relationships are removed.
        $this->deleted = 1;

        // The creator should be present after removal.
        $createdBy = null;
        if (isset($this->field_defs['created_by'])) {
            $createdBy = $this->created_by;
        }

        $this->mark_relationships_deleted($this->id);

        if ($createdBy) {
            $this->created_by = $createdBy;
        }

        // The modifier is the user that deleted the record.
        if (!empty($GLOBALS['current_user'])) {
            $this->modified_user_id = $GLOBALS['current_user']->id;
        } else {
            $this->modified_user_id = '1';
        }

        // Physically delete the bean.
        $this->db->delete($this);

        // Remove the record from the recently viewed lists.
        $tracker = BeanFactory::newBean('Trackers');
        $tracker->makeInvisibleForAll($this->id);

        SugarRelationship::resaveRelatedBeans();
    }
}
