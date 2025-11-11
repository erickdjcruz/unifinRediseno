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

namespace Sugarcrm\Sugarcrm\Denormalization\Relate\Process;

use BeanFactory;
use SugarBean;
use SugarRelationship;

final class Entity
{
    /** @var string */
    public $fieldName;

    /** @var SugarBean */
    public $targetBean;

    /** @var string */
    public $targetFieldName;

    /** @var SugarBean */
    public $sourceBean;

    /** @var string */
    public $sourceFieldName;

    /** @var array */
    public $sourceFieldDef;

    /** @var string */
    public $sourceFieldSource;

    /** @var array */
    public $fieldDef;

    /** @var array */
    public $fieldDefExt;

    /** @var SugarRelationship */
    public $relationship;

    public function __construct(SugarBean $bean, string $fieldName)
    {
        $this->targetBean = $bean;
        $fieldDef = $this->extractFieldDef($bean, $fieldName);
        if ($fieldDef) {
            $this->setFieldDef($fieldDef);
            $this->populateFieldDefExt();
            $this->populateRelationship();
        }
    }

    public function setFieldDef(array $fieldDef)
    {
        $this->fieldDef = $fieldDef;
        $this->fieldName = $this->fieldDef['name'];

        $this->sourceBean = BeanFactory::getBean($this->fieldDef['module']);
        $this->targetFieldName = "denorm_$this->fieldName";

        if ($this->fieldDef['source'] === 'non-db' && !empty($this->fieldDef['sort_on'])) {
            $this->sourceFieldName = reset($this->fieldDef['sort_on']);
        } else {
            if (!empty($this->fieldDef['db_concat_fields'])) {
                $this->sourceFieldName = end($this->fieldDef['db_concat_fields']);
            } else {
                $this->sourceFieldName = $this->fieldDef['rname'];
            }
        }

        $this->sourceFieldDef = $this->sourceBean->getFieldDefinition($this->sourceFieldName);
        $this->sourceFieldSource = $this->sourceFieldDef['source'] ?? '';
    }

    public function getTargetFieldDef(): array
    {
        return $this->fieldDefExt[$this->targetFieldName];
    }

    public function getTargetTableName(): string
    {
        return $this->targetBean->getTableName();
    }

    public function getSourceTableName(): string
    {
        return $this->sourceBean->getTableName();
    }

    public function getTargetModuleName(): string
    {
        return $this->targetBean->getModuleName();
    }

    public function getSourceModuleName(): string
    {
        return $this->sourceBean->getModuleName();
    }

    public function getTargetObjectName(): string
    {
        return $this->targetBean->getObjectName();
    }

    public function getTargetModuleDir(): string
    {
        return $this->targetBean->module_dir;
    }

    public function getFieldDefOnTarget(string $name): ?array
    {
        return $this->targetBean->getFieldDefinition($name) ?: null;
    }

    public function getRelationshipName(): string
    {
        return (string)$this->relationship->name;
    }

    protected function extractFieldDef(SugarBean $bean, string $fieldName): ?array
    {
        $result = $bean->getFieldDefinition($fieldName);
        if (is_array($result)) {
            return $result;
        } else {
            return null;
        }
    }

    protected function populateFieldDefExt(): void
    {
        $this->fieldDefExt = [
            $this->fieldName => [
                'is_denormalized' => true,
                'denormalized_field_name' => $this->targetFieldName,
            ],
        ];

        $dataToCopy = $this->sourceFieldDef;
        $dataToCopy['name'] = $this->targetFieldName;
        $dataToCopy['vname'] = $this->fieldDef['vname'];
        $dataToCopy['denorm_from_module'] = $this->sourceBean->getModuleName();
        if (isset($dataToCopy['source']) && $dataToCopy['source'] == 'non-db') {
            $dataToCopy['type'] = 'varchar';
            $dataToCopy['len'] = 510;
        }
        $dataToCopy['type'] = $dataToCopy['dbType'] ?? $dataToCopy['type'];
        $dataToCopy['required'] = false;
        $dataToCopy['studio'] = false;

        $this->fieldDefExt[$this->targetFieldName] = $dataToCopy;
    }

    protected function populateRelationship(): void
    {
        $linkName = $this->fieldDef['link'];
        $this->targetBean->load_relationship($linkName);
        $this->relationship = $this->targetBean->$linkName->getRelationshipObject();
    }
}
