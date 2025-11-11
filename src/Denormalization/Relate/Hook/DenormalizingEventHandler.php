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

namespace Sugarcrm\Sugarcrm\Denormalization\Relate\Hook;

use SugarBean;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Db\OnlineOperations;

/**
 *
 * Helper class for logic hook handler
 *
 */
final class DenormalizingEventHandler implements EventHandler
{
    /** @var OnlineOperations */
    private $db;

    public function __construct(OnlineOperations $db)
    {
        $this->db = $db;
    }

    public function handleAddRelationship(string $fieldName, SugarBean $bean, array $options): void
    {
        $this->setUp($options);
        if (empty($options['denorm_field_name']) || !is_string($options['denorm_field_name'])) {
            return;
        }
        $modifiedLinkId = $this->getModifiedLinkId($fieldName, $bean);

        // if the link ID was modified but the value wasn't updated - update it
        if ($modifiedLinkId && isset($options['link']['linked_table']) && isset($options['link']['linked_field_name'])) {
            $bean->{$options['denorm_field_name']} = $bean->$fieldName = $this->db->fetchValue(
                $options['link']['linked_table'],
                $options['link']['linked_field_name'],
                $modifiedLinkId
            );
        } else {
            $bean->{$options['denorm_field_name']} = $bean->$fieldName;
            if (!empty($options['synchronization_in_progress'])) {
                $this->db->updateTemporaryTableWithValue($bean, $bean->$fieldName, $options['tmp_table_name'] ?? null);
            }
        }
    }

    public function handleAddRelationshipWithValue(SugarBean $bean, array $options, $value, $relatedId): void
    {
        $this->setUp($options);
        if (!empty($options['link']['linked_key'])
            && isset($options['denorm_field_name'])
            && isset($options['link']['main_table'])
            && isset($options['link']['main_key'])
        ) {
            $this->db->updateLinkedBean(
                $bean->{$options['link']['linked_key']},
                $options['link']['join_table'] ?? null,
                $options['link']['join_main_key'] ?? null,
                $options['link']['join_linked_key'] ?? null,
                $options['denorm_field_name'],
                $options['link']['main_table'],
                $options['link']['main_key'],
                $value,
                $relatedId
            );
        }
        if (!empty($options['synchronization_in_progress'])) {
            $this->db->updateTemporaryTableWithValue($bean, $value, $options['tmp_table_name'] ?? null);
        }
    }

    public function handleDeleteRelationship(SugarBean $bean, array $options): void
    {
        $this->setUp($options);
        if (empty($options['denorm_field_name']) || !is_string($options['denorm_field_name'])) {
            return;
        }

        $bean->{$options['denorm_field_name']} = '';
        $this->db->updateBean($bean, $options['denorm_field_name']);
        if (!empty($options['synchronization_in_progress'])
            && isset($options['link']['linked_field_name'])
            && isset($options['link']['linked_table'])
            && isset($options['link']['linked_key'])
        ) {
            $this->db->updateTemporaryTable(
                $bean,
                $options['link']['linked_field_name'],
                $options['link']['linked_table'],
                $options['link']['linked_key'],
                $options['tmp_table_name'] ?? null
            );
        }
    }

    public function handleBeforeUpdate($value, SugarBean $bean, array $options): void
    {
        $this->setUp($options);
        if (empty($options['denorm_field_name']) || !is_string($options['denorm_field_name'])) {
            return;
        }
        $bean->{$options['denorm_field_name']} = $value;
        if (!empty($options['synchronization_in_progress'])) {
            $this->db->updateTemporaryTableWithValue($bean, $value, $options['tmp_table_name'] ?? null);
        }
    }

    public function handleAfterUpdateSourceField(SugarBean $bean, string $sourceLinkedFieldName, array $options): void
    {
        $this->setUp($options);
        if (!empty($sourceLinkedFieldName)) {
            $value = $bean->$sourceLinkedFieldName;
            if (!empty($options['link']['linked_key'])
                && isset($options['denorm_field_name'])
                && isset($options['link']['main_table'])
                && isset($options['link']['main_key'])
            ) {
                $this->db->updateLinkedBean(
                    $bean->{$options['link']['linked_key']},
                    $options['link']['join_table'] ?? null,
                    $options['link']['join_main_key'] ?? null,
                    $options['link']['join_linked_key'] ?? null,
                    $options['denorm_field_name'],
                    $options['link']['main_table'],
                    $options['link']['main_key'],
                    $value
                );
            }
            if (!empty($options['synchronization_in_progress'])) {
                $this->db->updateTemporaryTableWithValue($bean, $value, $options['tmp_table_name'] ?? null);
            }
        }
    }

    public function handleAfterUpdateTrackField(SugarBean $bean, array $options, array $dataChanges): void
    {
        $this->setUp($options);
        // "track_field" uses to track direct bean->link_id modification and update appropriate denormalized field
        if (isset($options['track_field']) && isset($dataChanges[$options['track_field']])) {
            $isHookForPrimaryBean = !($options['is_main'] ?? false);
            if (!empty($options['track_field']) && !empty($bean->{$options['track_field']}) && $isHookForPrimaryBean) {
                if (isset($options['link']['linked_field_name'])
                    && isset($options['link']['linked_table'])
                    && isset($options['link']['linked_key'])
                    && isset($options['link']['main_table'])
                    && isset($options['denorm_field_name'])
                ) {
                    $this->db->updateBeanWithLinkId(
                        $bean,
                        $options['link']['linked_field_name'],
                        $options['link']['linked_table'],
                        $options['link']['linked_key'],
                        $options['link']['main_table'],
                        $options['denorm_field_name'],
                        $bean->{$options['track_field']}
                    );
                }
                if (!empty($options['synchronization_in_progress'])
                    && isset($options['link']['linked_field_name'])
                    && isset($options['link']['linked_table'])
                    && isset($options['link']['linked_key'])) {
                    $this->db->updateTemporaryTable(
                        $bean,
                        $options['link']['linked_field_name'],
                        $options['link']['linked_table'],
                        $options['link']['linked_key'],
                        $options['tmp_table_name'] ?? null
                    );
                }
            }
        }
    }

    private function getModifiedLinkId(string $fieldName, SugarBean $bean): ?string
    {
        $idName = $bean->getFieldDefinition($fieldName)['id_name'] ?? null;
        if (!$idName) {
            return null;
        }

        $oldId = $bean->fetched_rel_row[$idName] ?? null;
        $newId = $bean->$idName;

        $oldValue = $bean->fetched_rel_row[$fieldName] ?? null;
        $newValue = $bean->$fieldName;

        // the link ID was changed but the value still wasn't updated
        if ($oldId !== $newId && $oldValue === $newValue) {
            return $newId;
        }

        return null;
    }

    private function setUp(array $options): void
    {
        if (!empty($options['tmp_table_name'])) {
            $this->db->setTmpTableName($options['tmp_table_name']);
        }
    }
}
