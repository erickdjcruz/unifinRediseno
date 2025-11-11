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

namespace Sugarcrm\Sugarcrm\Elasticsearch\Mapping;

use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Property\MultiFieldProperty;

/**
 *
 * This class builds the One Index per instance fields Mapping for ES 6+
 *
 * all the fields are either prefixed with module name or Common__, except id and sugar_module_name
 * also removed regular field names.
 * For instance: for Accounts' name field
 * the mapped field name is 'Accounts_name' and there is no 'name' in Elastic
 *
 */
class MappingForOneIndex extends Mapping
{
    /**
     * {@inheritdoc}
     */
    public function addModuleField($baseField, $field, MultiFieldProperty $property)
    {
        $moduleField = $this->module . self::PREFIX_SEP . $baseField;
        $this->createMultiFieldBase($moduleField, $this->notIndexedBase)->addField($field, $property);
    }

    /**
     * {@inheritdoc}
     */
    public function addModuleLongField($baseField, $field, MultiFieldProperty $property)
    {
        $moduleField = $this->module . self::PREFIX_SEP . $baseField;
        $notIndexedbase = $this->getNotIndexedBase(true);
        $this->createMultiFieldBase($moduleField, $notIndexedbase)->addField($field, $property);
    }

    /**
     * {@inheritdoc}
     */
    public function addCommonField($baseField, $field, MultiFieldProperty $property)
    {
        Mapping::checkCommonField($baseField);
        $commonField = self::PREFIX_COMMON . $baseField;
        $this->createMultiFieldBase($commonField, $this->notIndexedBase)->addField($field, $property);
    }
}
