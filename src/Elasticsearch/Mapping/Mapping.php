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

use Sugarcrm\Sugarcrm\Elasticsearch\Index\IndexManager;
use Sugarcrm\Sugarcrm\Elasticsearch\Provider\ProviderCollection;
use Sugarcrm\Sugarcrm\Elasticsearch\Exception\MappingException;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Property\MultiFieldProperty;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Property\MultiFieldBaseProperty;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Property\RawProperty;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Property\PropertyInterface;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Property\ObjectProperty;

/**
 *
 * This class builds the mapping per module (type) based on the available
 * providers.
 *
 */
class Mapping implements MappingInterface
{
    /**
     * Property name prefix separator
     * @var string
     */
    public const PREFIX_SEP = '__';

    /**
     * Common prefix
     * @var string
     */
    public const PREFIX_COMMON = 'Common__';

    /**
     * command field names
     */
    public const COMMON_FIELD_NAMES = [
        'acl_team_set_id',
        'assigned_user_id',
        'created_by',
        'date_modified',
        'favorite_link',
        'modified_user_id',
        'owner_id',
        'tags',
        'user_favorites',
        'erased_fields',
    ];
    /**
     * Module Name field used in index, this name should be unique
     */
    public const MODULE_NAME_FIELD = 'sugar_module_name';

    /**
     * @var string Module name
     */
    protected $module;

    /**
     * @var \SugarBean
     */
    protected $bean;

    /**
     * Elasticsearch mapping properties
     * @var PropertyInterface[]
     */
    protected $properties = [];

    /**
     * Base mapping used for all multi fields
     * @var array
     */
    protected $multiFieldBase = [
        'type' => 'keyword',
        'index' => true,
    ];

    /**
     * Base mapping for not indexed fields, set doc_values to false, make it searchable
     * even the size is large than 32K
     * @var array
     */
    protected $notIndexedBase = [
        'type' => 'keyword',
        'index' => false,
    ];

    protected $notIndexedBaseNotDoc = [
        'type' => 'keyword',
        'index' => false,
        'doc_values' => false,
    ];

    /**
     * Excluded fields from _source
     * @var array
     */
    protected $sourceExcludes = [];

    /**
     * @param string $module
     */
    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * factory method, to get Mapping Object based on ES version and 'enable_one_index' in config
     * @return Mapping
     */
    public static function getMapping(string $module)
    {
        if (IndexManager::isOneIndexEnabled() && IndexManager::isEsServerV6Above()) {
            // enable_one_index is true and ES server is 6.0 and up
            return new MappingForOneIndex($module);
        }
        return new Mapping($module);
    }

    /**
     * {@inheritdoc}
     */
    public function excludeFromSource($field)
    {
        $this->sourceExcludes[$field] = $field;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceExcludes()
    {
        return array_values($this->sourceExcludes);
    }

    /**
     * {@inheritdoc}
     */
    public function buildMapping(ProviderCollection $providers)
    {
        foreach ($providers as $provider) {
            $provider->buildMapping($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * {@inheritdoc}
     */
    public function getBean()
    {
        // lazy load bean
        if ($this->bean === null) {
            $this->bean = \BeanFactory::newBean($this->module);
        }
        return $this->bean;
    }

    /**
     * {@inheritdoc}
     */
    public function compile()
    {
        $compiled = [];
        foreach ($this->properties as $field => $property) {
            $compiled[$field] = $property->getMapping();
        }
        return $compiled;
    }

    protected function getNotIndexedBase(bool $longField)
    {
        if ($longField && $this->isLongTextEnabled()) {
            return $this->notIndexedBaseNotDoc;
        }

        return $this->notIndexedBase;
    }

    /**
     * {@inheritdoc}
     */
    public function addModuleField($baseField, $field, MultiFieldProperty $property)
    {
        $moduleField = $this->module . self::PREFIX_SEP . $baseField;
        $this->createMultiFieldBase($moduleField, $this->notIndexedBase)->addField($field, $property);
        $this->createCopyToBase($baseField, $this->notIndexedBase, [$moduleField]);
    }

    /**
     * {@inheritdoc}
     */
    public function addModuleLongField($baseField, $field, MultiFieldProperty $property)
    {
        $moduleField = $this->module . self::PREFIX_SEP . $baseField;
        $notIndexedbase = $this->getNotIndexedBase(true);
        $this->createMultiFieldBase($moduleField, $notIndexedbase)->addField($field, $property);
        $this->createCopyToBase($baseField, $notIndexedbase, [$moduleField]);
    }

    /**
     * {@inheritdoc}
     */
    public function addCommonField($baseField, $field, MultiFieldProperty $property)
    {
        self::checkCommonField($baseField);
        $commonField = self::PREFIX_COMMON . $baseField;
        $this->createMultiFieldBase($commonField, $this->notIndexedBase)->addField($field, $property);
        $this->createCopyToBase($baseField, $this->notIndexedBase, [$commonField]);
    }

    /**
     * {@inheritdoc}
     */
    public function addModuleObjectProperty($field, ObjectProperty $property)
    {
        $this->addProperty($this->module . self::PREFIX_SEP . $field, $property);
    }

    /**
     * {@inheritdoc}
     */
    public function addCommonObjectProperty($field, ObjectProperty $property)
    {
        self::checkCommonField($field);
        $this->addProperty(self::PREFIX_COMMON . $field, $property);
    }

    /**
     * {@inheritdoc}
     */
    public function addNotIndexedField($field, array $copyTo = [])
    {
        $this->createMultiFieldBase($field, $this->notIndexedBase, $copyTo);
    }

    /**
     * {@inheritdoc}
     */
    public function addNotAnalyzedField($field, array $copyTo = [])
    {
        $this->createMultiFieldBase($field, $this->multiFieldBase, $copyTo);
    }

    /**
     * {@inheritdoc}
     */
    public function addMultiField($baseField, $field, MultiFieldProperty $property)
    {
        $this->createMultiFieldBase($baseField, $this->multiFieldBase, [])->addField($field, $property);
    }

    /**
     * {@inheritdoc}
     */
    public function addObjectProperty($field, ObjectProperty $property)
    {
        $this->addProperty($field, $property);
    }

    /**
     * {@inheritdoc}
     */
    public function addRawProperty($field, RawProperty $property)
    {
        $this->addProperty($field, $property);
    }

    /**
     * {@inheritdoc}
     */
    public function hasProperty($field)
    {
        return isset($this->properties[$field]);
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty($field)
    {
        if ($this->hasProperty($field)) {
            return $this->properties[$field];
        }
        throw new MappingException("Trying to get non-existing property '{$field}' for '{$this->module}'");
    }

    /**
     * Create base multi field object for given field. If the field already
     * exists we use the one which is present and only apply the copyTo fields
     * on top of the already existing one.
     *
     * @param string $field
     * @param array $mapping Mapping to apply on base field
     * @param array $copyTo Optional copy_to definition
     * @return MultiFieldBaseProperty
     * @throws MappingException
     */
    protected function createMultiFieldBase($field, array $mapping, array $copyTo = [])
    {
        // create multi field base if not set yet
        if (!$this->hasProperty($field)) {
            $property = new MultiFieldBaseProperty();
            $property->setMapping($mapping);
            $this->addRawProperty($field, $property);
        }

        // make sure we have a base multi field
        $property = $this->getProperty($field);
        if (!$property instanceof MultiFieldBaseProperty) {
            throw new MappingException("Field '{$field}' is not a multi field");
        }

        // append copy_to definitions
        foreach ($copyTo as $copyToField) {
            $property->addCopyTo($copyToField);
        }

        return $property;
    }

    /**
     * Create primary base field for indexing and _source purpose
     * copying the values into the given target field
     * @param string $field The primary field name
     * @param array $notIndexedBase the notindexedBase
     * @param string[] $targetFields Array of target fields
     */
    protected function createCopyToBase($field, array $notIndexedBase, array $targetFields = [])
    {
        $this->createMultiFieldBase($field, $notIndexedBase, $targetFields);
    }

    /**
     * Low level wrapper to add mapping properties
     *
     * @param string $field
     * @param PropertyInterface $property
     * @throws MappingException
     */
    protected function addProperty($field, PropertyInterface $property)
    {
        if (isset($this->properties[$field])) {
            throw new MappingException("Cannot redeclare field '{$field}' for module '{$this->module}'");
        }
        $this->properties[$field] = $property;
    }

    /**
     * check sugar config if 'enable_long_text_search' is enabled
     * @return bool
     */
    protected function isLongTextEnabled(): bool
    {
        global $sugar_config;
        if ($sugar_config && !empty($sugar_config['enable_long_text_search'])) {
            return true;
        }
        return false;
    }

    /**
     * this is common field check
     * @param string $field
     * @return bool
     */
    protected static function checkCommonField(?string $field): bool
    {
        if (!in_array($field, static::COMMON_FIELD_NAMES)) {
            if (!empty($GLOBALS['log'])) {
                $GLOBALS['log']->error('This field is not in the common field list: ' . $field);
            }
            return false;
        }
        return true;
    }
}
