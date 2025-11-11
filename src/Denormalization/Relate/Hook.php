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

namespace Sugarcrm\Sugarcrm\Denormalization\Relate;

use SugarBean;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Db\Db;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Hook\Configuration;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Hook\DenormalizingEventHandler;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Hook\EventHandler;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Hook\DatabaseConfiguration;

/**
 *
 * Logic hook handler
 *
 */
final class Hook
{
    /** @var DenormalizingEventHandler */
    private $eventHandler;

    /** @var Configuration */
    private $config;

    public function __construct(EventHandler $eventHandler = null, Configuration $config = null)
    {
        $this->eventHandler = $eventHandler ?? new DenormalizingEventHandler(Db::getInstance());
        $this->config = $config ?? new DatabaseConfiguration();
    }

    /**
     * To be used from logic hooks
     *
     * @param SugarBean $bean
     * @param string $event Triggered event
     * @param array $arguments Optional arguments
     */
    public function handleBeforeUpdate(?SugarBean $bean, string $event, array $arguments): void
    {
        foreach ($this->getSettings($bean) as $sourceLinkedFieldName => $relationships) {
            foreach ($relationships as $relationshipName => $options) {
                if (!empty($options) && is_array($options)) {
                    $value = $bean->$sourceLinkedFieldName;
                    $isHookForPrimaryBean = !($options['is_main'] ?? false);

                    if ($isHookForPrimaryBean) {
                        // CASE: update denormalized field with relate field value
                        $this->eventHandler->handleBeforeUpdate($value, $bean, $options);
                    }
                }
            }
        }
    }

    /**
     * To be used from logic hooks
     *
     * @param SugarBean $bean
     * @param string $event Triggered event
     * @param array $arguments Optional arguments
     */
    public function handleAfterUpdate(?SugarBean $bean, string $event, array $arguments): void
    {
        foreach ($this->getSettings($bean) as $sourceLinkedFieldName => $relationships) {
            foreach ($relationships as $relationshipName => $options) {
                if (!empty($options) && is_array($options)) {
                    $isHookForPrimaryBean = !($options['is_main'] ?? false);
                    $isNewBean = empty($arguments['isUpdate']);
                    $dataChanges = $arguments['dataChanges'] ?? [];
                    $sourceFieldChanged = isset($dataChanges[$sourceLinkedFieldName]);

                    if (!$isHookForPrimaryBean && !$isNewBean && $sourceFieldChanged) {
                        // CASE: we're saving a linked bean and could change the source field,
                        // so the primary bean may need to be updated too
                        $this->eventHandler->handleAfterUpdateSourceField($bean, $sourceLinkedFieldName, $options);
                    } else {
                        // CASE: we modified link_id manually and saving a primary bean.
                        // In this case it's necessary to update related bean too.
                        // Note: we don't know the value of denormalized field so updating using link_id
                        $this->eventHandler->handleAfterUpdateTrackField($bean, $options, $dataChanges);
                    }
                }
            }
        }
    }

    /**
     * To be used from logic hooks
     *
     * @param SugarBean $bean
     * @param string $event Triggered event
     * @param array $arguments Optional arguments
     */
    public function handleDeleteRelationship(?SugarBean $bean, string $event, array $arguments): void
    {
        foreach ($this->getSettings($bean) as $sourceLinkedFieldName => $relationships) {
            foreach ($relationships as $relationshipName => $options) {
                if (!empty($options) && is_array($options)) {
                    if (isset($options['module'])
                        && isset($arguments['related_module'])
                        && $options['module'] !== $arguments['related_module']) {
                        continue;
                    }
                    if (!empty($options['link']['relationship_name'])
                        && isset($arguments['relationship'])
                        && $options['link']['relationship_name'] !== $arguments['relationship']) {
                        continue;
                    }
                    if (!empty($options['link']['relationship_name'])
                        && isset($arguments['relationship'])
                        && $options['link']['relationship_name'] !== $arguments['relationship']) {
                        continue;
                    }

                    $isHookForPrimaryBean = !($options['is_main'] ?? false);
                    if ($isHookForPrimaryBean) {
                        // CASE: relationship deleted and it's necessary to clear the value of the denormalized field
                        $this->eventHandler->handleDeleteRelationship($bean, $options);
                    }
                }
            }
        }
    }

    /**
     * To be used from logic hooks
     *
     * @param SugarBean $bean
     * @param string $event Triggered event
     * @param array $arguments Optional arguments
     */
    public function handleAddRelationship(?SugarBean $bean, string $event, array $arguments): void
    {
        foreach ($this->getSettings($bean) as $sourceLinkedFieldName => $relationships) {
            foreach ($relationships as $relationshipName => $options) {
                if (!empty($options) && is_array($options)) {
                    if (isset($options['module'])
                        && isset($arguments['related_module'])
                        && $options['module'] !== $arguments['related_module']) {
                        continue;
                    }
                    if (!empty($options['link']['relationship_name'])
                        && isset($arguments['relationship'])
                        && $options['link']['relationship_name'] !== $arguments['relationship']) {
                        continue;
                    }
                    if (!empty($options['link']['relationship_name'])
                        && isset($arguments['relationship'])
                        && $options['link']['relationship_name'] !== $arguments['relationship']) {
                        continue;
                    }

                    $isHookForPrimaryBean = !($options['is_main'] ?? false);

                    if ($isHookForPrimaryBean) {
                        // CASE #1: a relationship added for a primary bean AND:
                        // - the link ID was modified
                        // - the denormalized field still has the old value
                        // CASE #2: a relationship added for a primary bean AND link ID was not modified
                        // These cases handles by separate method
                        $this->eventHandler->handleAddRelationship($sourceLinkedFieldName, $bean, $options);
                    } else {
                        // CASE: relationship added for related bean and we know the value of denormalized field.
                        // So the primary bean should be updated with the value (which is present)
                        $value = $bean->$sourceLinkedFieldName;

                        if (isset($arguments['related_id'])) {
                            $this->eventHandler->handleAddRelationshipWithValue($bean, $options, $value, $arguments['related_id']);
                        }
                    }
                }
            }
        }
    }

    private function getSettings($bean): array
    {
        if (!$bean instanceof SugarBean) {
            return [];
        }

        return $this->config->getModuleConfiguration($bean->getModuleName());
    }
}
