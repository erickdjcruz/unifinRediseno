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

use Administration;
use MetaDataManager;
use ModuleInstaller;
use SugarAutoLoader;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Db\Db;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Db\OfflineOperations;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Hook\DatabaseConfiguration;
use Sugarcrm\Sugarcrm\Denormalization\Relate\Process\Entity;
use BeanFactory;
use VardefManager;

final class Process
{
    /** @var ModuleInstaller */
    private $moduleInstaller;

    /** @var SynchronizationManager */
    private $synchronizationJob;

    /** @var OfflineOperations */
    private $db;

    /** @var string */
    private $tmpTableName;

    public function __construct()
    {
        $this->db = Db::getInstance();
        $this->synchronizationJob = new SynchronizationManager();
        $this->moduleInstaller = new ModuleInstaller();
        $this->moduleInstaller->silent = true;
    }

    public function denormalize(Entity $entity): void
    {
        $this->configSetField($entity, true);
        $this->synchronizationJob->setUpJob($entity);
    }

    public function normalize(Entity $entity): void
    {
        $rebuildVarDefRequired = false;
        if ($this->fieldDefExists($entity)) {
            $rebuildVarDefRequired = true;
        }

        $this->removeLogicHook($entity);
        $this->removeFieldDefExt($entity);
        $this->unsetLogicHookConfiguration($entity);
        $this->configSetField($entity, false);
        $this->synchronizationJob->removeJobIfExists();

        if ($rebuildVarDefRequired) {
            $this->rebuildVardefs($entity);
        }
    }

    /**
     * Single action for making a field denormalized ignoring value synchronization (will have an empty value)
     *
     * @param array $fieldDef
     */
    public function turnOnDenormalizationWithoutCopying(Entity $entity): void
    {
        $this->configSetField($entity, true);
        $this->alterTable($entity);
        $this->prepareForCopy($entity);
        $this->onDataSetCopied($entity);
    }

    /**
     * Starts the migration process
     */
    public function prepareForCopy(Entity $entity): void
    {
        $this->createFieldDefExt($entity);
        $this->rebuildVardefs($entity);

        // tmp table must exist before logic hook is active
        $this->db->createTemporaryTable($entity->getTargetFieldDef());
        // enable logic hook functionality before population started, the hook will update tmp table too
        $this->configureLogicHook($entity, true);
        $this->setupLogicHook($entity);
        // now it's safe to populate
        $this->populateTemporaryTable($entity);
        // create index for tmp table
        $this->db->ensureTemporaryTableIndex();
        $this->db->finalizeTemporaryTable();
    }

    /**
     * Finalizes the migration process after everything is copied
     */
    public function onDataSetCopied(Entity $entity): void
    {
        // add DB index
        $this->createIndex($entity);

        // now logic hook will ignore tmp table
        $this->configureLogicHook($entity, false);

        // activate new field in FilterApi
        $this->configSetField($entity, true, false);

        // we don't need tmp table anymore
        $this->db->dropTemporaryTable();
    }

    public function alterTable(Entity $entity): void
    {
        $this->db->ensureColumnExists(
            $entity->getTargetTableName(),
            $entity->getTargetFieldDef()
        );
    }

    public function getTemporaryTableCount(): int
    {
        return $this->db->getTemporaryTableCount();
    }

    public function setTemporaryTableName(string $name): void
    {
        $this->tmpTableName = $name;
        $this->db->setTmpTableName($name);
    }

    public function migrateTemporaryTableChunk(Entity $entity, int $offset, int $limit): int
    {
        return $this->db->migrateTemporaryTableChunk(
            $entity->getTargetTableName(),
            $entity->targetFieldName,
            $offset,
            $limit
        );
    }

    public function isAltered(Entity $entity): bool
    {
        return $this->db->isAltered($entity->getTargetTableName(), $entity->targetFieldName);
    }

    private function rebuildVardefs(Entity $entity): void
    {
        $this->moduleInstaller->rebuild_vardefs([$entity->getTargetModuleName()]);
        $this->clearApiCache();
        VardefManager::refreshVardefs(
            $entity->getTargetModuleName(),
            $entity->getTargetObjectName()
        );
        $entity->targetBean = BeanFactory::newBean($entity->getTargetModuleName());
    }

    private function configSetField(Entity $entity, bool $isDenormalized, bool $isInSync = true): void
    {
        $config = new FieldConfig();
        if ($isDenormalized) {
            $config->markFieldAsDenormalized($entity, $isInSync);
        } else {
            $config->markFieldAsNormalized($entity);
        }
    }

    /**
     * @param bool $syncInProgress
     */
    private function configureLogicHook(Entity $entity, bool $syncInProgress): void
    {
        $link = [
            'linked_field_name' => $entity->sourceFieldName,
            'join_table' => $entity->relationship->join_table,
            'relationship_name' => $entity->relationship->name,
        ];
        if ($entity->relationship->getLHSModule() === $entity->getTargetModuleName()) {
            $link['main_table'] = $entity->relationship->lhs_table;
            $link['main_key'] = $entity->relationship->lhs_key;
            $link['linked_table'] = $entity->relationship->rhs_table;
            $link['linked_key'] = $entity->relationship->rhs_key;
            $link['join_main_key'] = $entity->relationship->join_lhs;
            $link['join_linked_key'] = $entity->relationship->join_rhs;
        } else {
            $link['main_table'] = $entity->relationship->rhs_table;
            $link['main_key'] = $entity->relationship->rhs_key;
            $link['linked_table'] = $entity->relationship->lhs_table;
            $link['linked_key'] = $entity->relationship->lhs_key;
            $link['join_main_key'] = $entity->relationship->join_key_rhs;
            $link['join_linked_key'] = $entity->relationship->join_key_lhs;
        }

        $hookConfig = new DatabaseConfiguration();

        // linked bean update options
        $hookConfig->setFieldConfiguration(
            $entity->getSourceModuleName(),
            $entity->sourceFieldName,
            $link['relationship_name'],
            [
                'module' => $entity->getTargetModuleName(),
                'is_main' => true,
                'denorm_field_name' => $entity->targetFieldName,
                'link' => $link,
                // if synchronization process still alive we should update TMP table too
                'synchronization_in_progress' => $syncInProgress,
                'tmp_table_name' => $this->db->getTmpTableName(),
            ]
        );

        // main bean update options
        $hookConfig->setFieldConfiguration(
            $entity->getTargetModuleName(),
            $entity->fieldName,
            $link['relationship_name'],
            [
                'module' => $entity->getSourceModuleName(),
                'is_main' => false,
                'denorm_field_name' => $entity->targetFieldName,
                'link' => $link,
                'synchronization_in_progress' => $syncInProgress,
                // if O2M or O2O relationship changes directly by assigning a new ID - the hook should
                // update the denormalized field
                'track_field' => $entity->relationship->type === REL_MANY_MANY ? null : $link['main_key'],
                'tmp_table_name' => $this->db->getTmpTableName(),
            ]
        );
    }

    private function unsetLogicHookConfiguration(Entity $entity): void
    {
        $moduleName = $entity->getTargetModuleName();
        $sourceModuleName = $entity->getSourceModuleName();
        $relationshipName = $entity->getRelationshipName();
        $hookConfig = new DatabaseConfiguration();
        $hookConfig->unsetFieldConfiguration($moduleName, $entity->fieldName, $relationshipName);
        $hookConfig->unsetFieldConfiguration($sourceModuleName, $entity->sourceFieldName, $relationshipName);
    }

    private function removeFieldDefExt(Entity $entity): void
    {
        $path = $this->getFieldDefExtPath($entity);
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function createFieldDefExt(Entity $entity): void
    {
        $path = $this->getFieldDefExtPath($entity);
        $objectName = $entity->getTargetObjectName();

        $content = "<?php\n";
        $line = '$dictionary[' . var_export($objectName, true) . "]['fields']";

        foreach ($entity->fieldDefExt as $fieldName => $fieldData) {
            $content .= "\n// " . var_export($fieldName, true) . "\n";
            foreach ($fieldData as $key => $value) {
                $content .= "{$line}["
                    . var_export($fieldName, true) . ']['
                    . var_export($key, true) . '] = '
                    . var_export($value, true) . ";\n";
            }
        }

        file_put_contents($path, $content);
    }

    private function getFieldDefExtPath(Entity $entity): string
    {
        $dir = SugarAutoLoader::normalizeFilePath(
            "custom/Extension/modules/{$entity->getTargetModuleDir()}/Ext/Vardefs"
        );
        SugarAutoLoader::ensureDir($dir);

        return "$dir/denorm_{$entity->fieldName}.php";
    }

    private function fieldDefExists(Entity $entity): bool
    {
        $path = $this->getFieldDefExtPath($entity);
        return is_file($path);
    }

    private function setupLogicHook(Entity $entity): void
    {
        $hook = $this->getHookInstaller($entity);
        $hook->create();
    }

    private function removeLogicHook(Entity $entity): void
    {
        $hook = $this->getHookInstaller($entity);
        $hook->remove();
    }

    private function getHookInstaller(Entity $entity): HookInstaller
    {
        $moduleNames = array_unique([$entity->getTargetModuleName(), $entity->getSourceModuleName()]);

        return new HookInstaller($moduleNames);
    }

    private function clearApiCache(): void
    {
        MetaDataManager::clearAPICache();
    }

    private function populateTemporaryTable(Entity $entity): void
    {
        $relationship = $entity->relationship;
        /** @var string $relTable */
        $relTable = $relationship->join_table;
        $sourceTable = $entity->getSourceTableName();
        $targetTable = $entity->getTargetTableName();
        $sourceFieldName = $entity->sourceFieldName;

        if (!empty($relTable)) {
            $joinSourceField = $relationship->join_key_lhs;
            $joinTargetField = $relationship->join_key_rhs;

            $idField = $joinTargetField;
            $fromTable = $relTable;
            $joinTable = $sourceTable;
            $joinConditionTargetField = $joinSourceField;
            $joinConditionSourceField = 'id';
        } else {
            if ($targetTable === $relationship->rhs_table) {
                $joinTargetField = $relationship->rhs_key;
                $joinSourceField = $relationship->lhs_key;
            } else {
                $joinTargetField = $relationship->lhs_key;
                $joinSourceField = $relationship->rhs_key;
            }

            $idField = 'id';
            $fromTable = $targetTable;
            $joinTable = $sourceTable;
            $joinConditionTargetField = $joinTargetField;
            $joinConditionSourceField = $joinSourceField;
        }

        $this->db->populateTemporaryTable(
            $idField,
            $sourceFieldName,
            $fromTable,
            $joinTable,
            $joinConditionTargetField,
            $joinConditionSourceField
        );
    }

    private function createIndex(Entity $entity): void
    {
        $indexedFields = [$entity->targetFieldName, 'id', 'deleted'];
        $indexedFieldDefs = [];
        foreach ($indexedFields as $indexedField) {
            $indexedFieldDef = $entity->getFieldDefOnTarget($indexedField);
            if ($indexedFieldDef) {
                $indexedFieldDefs[] = $indexedFieldDef;
            }
        }
        $indexName = \DBManagerFactory::getInstance()->getValidDBName(
            'idx_' . $entity->getTargetTableName() . "_{$entity->targetFieldName}"
        );

        $this->db->replicateIndex(
            $entity->getTargetTableName(),
            $indexName,
            $entity->targetBean,
            $indexedFieldDefs
        );
    }
}
