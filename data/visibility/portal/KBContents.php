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

namespace Sugarcrm\Sugarcrm\Visibility\Portal;

use Sugarcrm\Sugarcrm\Elasticsearch\Provider\Visibility\Visibility;

/**
 * IMPORTANT NOTE: If the below logic is customised/changed, it will need to be updated also for the related object Notes on the Notes.php portal visibility rules
 * IMPORTANT NOTE: Notes have to be filtered based on the visible parent objects
 */
class KBContents extends Portal
{
    public function addVisibilityQuery(\SugarQuery $query, array $options = [])
    {
        $query->where()
            ->equals($options['table_alias'] . '.active_rev', 1)
            ->equals($options['table_alias'] . '.is_external', 1)
            ->equals($options['table_alias'] . '.status', \KBContent::ST_PUBLISHED);
    }

    /**
     * @param \User $user
     * @param \Elastica\Query\BoolQuery $filter
     * @param Visibility $provider
     */
    public function elasticAddFilters(\User $user, \Elastica\Query\BoolQuery $filter, Visibility $provider)
    {
        $moduleName = $this->context->getBean()->module_name;

        if ($statuses = $this->getPublishedStatuses()) {
            $filter->addMust($provider->createFilter('KBStatus', [
                'published_statuses' => $statuses,
                'module' => $moduleName,
            ]));
        }

        $filter->addMust($provider->createFilter('KBActiveRevision', [
            'module' => $moduleName,
        ]));

        $filter->addMust($provider->createFilter('KBIsExternal', [
            'module' => $moduleName,
            'expected' => 1,
        ]));

        $filter->addMust($provider->createFilter('KBExpDate', [
            'module' => $moduleName,
            'range' => ['gte' => 'now/d'],
        ]));
    }

    /**
     * Get published statuses
     * @return array
     */
    protected function getPublishedStatuses(): array
    {
        if (!method_exists($this->context->getBean(), 'getPublishedStatuses')) {
            return [];
        }
        return $this->context->getBean()->getPublishedStatuses();
    }
}
