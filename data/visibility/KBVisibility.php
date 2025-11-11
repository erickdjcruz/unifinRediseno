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

use Sugarcrm\Sugarcrm\Elasticsearch\Provider\Visibility\StrategyInterface;
use Sugarcrm\Sugarcrm\Elasticsearch\Provider\Visibility\Visibility;
use Sugarcrm\Sugarcrm\Elasticsearch\Analysis\AnalysisBuilder;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Mapping;
use Sugarcrm\Sugarcrm\Elasticsearch\Adapter\Document;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Property\MultiFieldProperty;

/**
 * Class KBVisibility
 * Additional visibility check for KB.
 */
class KBVisibility extends SugarVisibility implements StrategyInterface
{
    /**
     * {@inheritDoc}
     * Used in \SugarBean::create_new_list_query
     */
    public function addVisibilityWhere(&$query)
    {
        $addon = $this->getWhereVisibilityRaw();
        if (!empty($addon)) {
            if (!empty($query)) {
                $query .= " AND $addon";
            } else {
                $query = $addon;
            }
        }
        return $query;
    }

    /**
     * Create additional query for `where` part, if needed.
     * @return string Additional query or empty string.
     */
    protected function getWhereVisibilityRaw()
    {
        $db = DBManagerFactory::getInstance();
        if (!method_exists($this->bean, 'getPublishedStatuses') || !$this->shouldCheckVisibility()) {
            return '';
        } else {
            $statuses = implode(
                ',',
                array_map(function (string $status) use ($db): string {
                    return $db->quoted($status);
                }, $this->bean->getPublishedStatuses())
            );
            $ow = new OwnerVisibility($this->bean, $this->params);
            $addon = '';
            $ow->addVisibilityWhere($addon);
            return "({$addon} OR {$this->bean->table_name}.status IN ($statuses))";
        }
    }

    /**
     * {@inheritDoc}
     */
    public function addVisibilityWhereQuery(SugarQuery $query)
    {
        $addon = $this->getWhereVisibilityRaw();
        if (!empty($addon)) {
            $query->whereRaw($addon);
        }
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function elasticBuildAnalysis(AnalysisBuilder $analysisBuilder, Visibility $provider)
    {
        // no special analyzers needed
    }

    /**
     * {@inheritdoc}
     */
    public function elasticBuildMapping(Mapping $mapping, Visibility $provider)
    {
        $property = new MultiFieldProperty();
        $property->setType('keyword');
        $mapping->addModuleField('status', 'kbvis', $property);

        $property = new MultiFieldProperty();
        $property->setType('integer');
        $mapping->addModuleField('active_rev', 'kbvis', $property);

        $property = new MultiFieldProperty();
        $property->setType('keyword');
        $mapping->addModuleField('language', 'kbvis', $property);

        $property = new MultiFieldProperty();
        $property->setType('integer');
        $mapping->addModuleField('is_external', 'kbvis', $property);

        $property = new MultiFieldProperty();
        $property->setType('date');
        $mapping->addModuleField('exp_date', 'kbvis', $property);
    }

    /**
     * {@inheritdoc}
     */
    public function elasticProcessDocumentPreIndex(Document $document, SugarBean $bean, Visibility $provider)
    {
        // nothing to do here
    }

    /**
     * {@inheritdoc}
     */
    public function elasticGetBeanIndexFields($module, Visibility $provider)
    {
        return ['status' => 'id'];
    }

    /**
     * {@inheritdoc}
     */
    public function elasticAddFilters(User $user, \Elastica\Query\BoolQuery $filter, Visibility $provider)
    {
        $moduleName = $this->bean->module_name;

        // create owner filter
        $ownerFilter = $provider->createFilter('Owner', ['user' => $user]);

        if ($statuses = $this->getPublishedStatuses()) {
            $filter->addMust($provider->createFilter('KBStatus', [
                'published_statuses' => $statuses,
                'module' => $moduleName,
            ]));
        } else {
            $filter->addMust($ownerFilter);
        }

        $filter->addMust($provider->createFilter('KBActiveRevision', [
            'module' => $moduleName,
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
    protected function getPublishedStatuses()
    {
        if (!method_exists($this->bean, 'getPublishedStatuses')) {
            return [];
        }
        return $this->bean->getPublishedStatuses();
    }

    /**
     * Check whether we need to check visibility
     * @return bool Return true if need to check, false otherwise.
     */
    protected function shouldCheckVisibility()
    {
        $currentUser = $GLOBALS['current_user'];
        $portalUserId = BeanFactory::newBean('Users')->retrieve_user_id('SugarCustomerSupportPortalUser');
        return $currentUser->id == $portalUserId;
    }
}
