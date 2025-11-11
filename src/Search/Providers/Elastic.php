<?php

declare(strict_types=1);
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

namespace Sugarcrm\Sugarcrm\Search\Providers;

/**
 * Class Elastic
 */
class Elastic extends Search
{
    /**
     * @var array
     */
    protected $propertiesToCopy = [
        'next_offset',
        'total',
    ];

    /**
     * @var string
     */
    protected $platform;

    /**
     * {@inheritDoc}
     * @throws \SugarApiExceptionSearchRuntime
     */
    public function getData(\ServiceBase $api, array $args)
    {
        // unset unneeded params if provided
        unset($args['tags']);
        unset($args['tag_filters']);
        unset($args['module']);

        $this->platform = empty($api->platform) ? 'base' : $api->platform;

        $searchApi = new \GlobalSearchApi();
        $data = $searchApi->globalSearch($api, $args);
        return $this->formatData($data);
    }

    /**
     * @return array
     */
    public function getPropertiesToCopy(): array
    {
        return $this->propertiesToCopy ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public function formatData(array $data): array
    {
        $newData = [];

        // unset the properties we don't need
        foreach ($this->getPropertiesToCopy() as $property) {
            if (isset($data[$property])) {
                $newData[$property] = $data[$property];
            }
        }

        $newData['records'] = [];

        // massage each record
        foreach ($data['records'] as $record) {
            $newRecord = [];

            // handle mapping
            global $dictionary;
            $module = $record['_module'];
            $objectName = \BeanFactory::getObjectName($module);
            if (!isset($dictionary[$objectName])) {
                \VardefManager::loadVardef($module, $objectName, true);
            }
            $settings = \VardefManager::getModuleProperty($objectName, 'generic_search');
            $mapping = $settings['Elastic']['mapping'] ?? [];
            if (!empty($mapping) && is_array($mapping)) {
                foreach ($mapping as $new => $original) {
                    if (isset($record[$original])) {
                        $newRecord[$new] = $record[$original];
                    }
                }
            }

            // add url
            if ($this->platform == 'portal') {
                $platform = 'portal/index.php';
            } else {
                $platform = '';
            }
            $newRecord['url'] = $platform . '#' . $record['_module'] . '/' . $record['id'];

            $newData['records'][] = $newRecord;
        }
        return $newData;
    }
}
