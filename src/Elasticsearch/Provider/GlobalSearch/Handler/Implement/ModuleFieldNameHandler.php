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

namespace Sugarcrm\Sugarcrm\Elasticsearch\Provider\GlobalSearch\Handler\Implement;

use Sugarcrm\Sugarcrm\Elasticsearch\Adapter\Document;
use Sugarcrm\Sugarcrm\Elasticsearch\Index\IndexManager;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Mapping;
use Sugarcrm\Sugarcrm\Elasticsearch\Provider\GlobalSearch\Handler\AbstractHandler;
use Sugarcrm\Sugarcrm\Elasticsearch\Provider\GlobalSearch\Handler\MappingHandlerInterface;
use Sugarcrm\Sugarcrm\Elasticsearch\Provider\GlobalSearch\Handler\ProcessDocumentHandlerInterface;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Property\MultiFieldProperty;

/**
 *
 * Module Name handler, it adds 'sugar_module_name' data field
 *
 */
class ModuleFieldNameHandler extends AbstractHandler implements
    MappingHandlerInterface,
    ProcessDocumentHandlerInterface
{
    protected $noChangeFields = [
        'id',
        Mapping::MODULE_NAME_FIELD,
    ];

    /**
     * {@inheritdoc}
     */
    public function buildMapping(Mapping $mapping, $field, array $defs)
    {
        if (!$mapping->hasProperty(Mapping::MODULE_NAME_FIELD)) {
            // common field for denormalized ids
            $property = new MultiFieldProperty();
            $property->setType('keyword');
            $mapping->addMultiField(Mapping::MODULE_NAME_FIELD, Mapping::MODULE_NAME_FIELD, $property);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function processDocumentPreIndex(Document $document, \SugarBean $bean)
    {
        $document->setDataField(Mapping::MODULE_NAME_FIELD, $bean->getModuleName());
        $this->changeFieldNamePrefix($document, $bean);
    }

    /**
     * change field name using <module_name>__<field name> or <Common>__<field name>
     * @param Document $document
     * @param \SugarBean $bean
     */
    protected function changeFieldNamePrefix(Document $document, \SugarBean $bean)
    {
        if (!$this->isOneIndexEnabledAndEsV6Above()) {
            // do nothing for ES version < 6 or one index is not enabled
            return;
        }
        $data = $document->getData();
        foreach ($data as $field => $value) {
            if ((in_array($field, $this->noChangeFields))
                || preg_match('/' . Mapping::PREFIX_SEP . '/', $field)) {
                continue;
            } elseif (in_array($field, Mapping::COMMON_FIELD_NAMES)) {
                // common field
                $document->setDataField(Mapping::PREFIX_COMMON . $field, $value);
                $document->removeDataField($field);
            } else {
                // general field, preefixed with module name
                $document->setDataField($bean->getModuleName() . Mapping::PREFIX_SEP . $field, $value);
                $document->removeDataField($field);
            }
        }
    }

    /**
     * check if ES version is 6.0+ and one index is enabled
     * @return bool
     */
    protected function isOneIndexEnabledAndEsV6Above(): bool
    {
        return (IndexManager::isOneIndexEnabled() && IndexManager::isEsServerV6Above());
    }
}
