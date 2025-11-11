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
use Sugarcrm\Sugarcrm\Denormalization\Relate\Process\Entity;

class FieldConfig
{
    private const CONFIG_KEY = 'field_list';

    public function markFieldAsDenormalized(Entity $entity, bool $isInSync): void
    {
        $fieldList = $this->getList();

        $fieldList[$entity->getTargetModuleName()][$entity->fieldName] =
            [
                'denorm_field_name' => $entity->targetFieldName,
                'synchronization_in_progress' => $isInSync,
            ];

        $this->saveList($fieldList);
    }

    public function markFieldAsNormalized(Entity $entity): void
    {
        $fieldList = $this->getList();
        $moduleList = $entity->getTargetModuleName();

        unset($fieldList[$moduleList][$entity->fieldName]);
        if (empty($fieldList[$moduleList])) {
            unset($fieldList[$moduleList]);
        }

        $this->saveList($fieldList);
    }

    public function getList(): array
    {
        $administration = Administration::getSettings('denormalization');
        return $administration->settings['denormalization_' . self::CONFIG_KEY] ?? [];
    }

    /**
     * Returns a list of denormalized fields which can be used safely on a List View or any page
     */
    public function getSynchronizedModuleFields(string $module): array
    {
        $fields = $this->getList()[$module] ?? [];

        return array_filter($fields, function ($def) {
            return empty($def['synchronization_in_progress']);
        });
    }

    protected function saveList(array $fieldList): void
    {
        $administration = Administration::getSettings('denormalization');
        $administration->saveSetting('denormalization', self::CONFIG_KEY, $fieldList, 'base');
    }
}
