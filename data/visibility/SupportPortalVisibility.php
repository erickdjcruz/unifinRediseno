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


use Sugarcrm\Sugarcrm\Visibility\Portal as PortalStrategy;
use Sugarcrm\Sugarcrm\Portal\Factory as PortalFactory;
use Sugarcrm\Sugarcrm\Elasticsearch\Provider\Visibility\StrategyInterface;
use Sugarcrm\Sugarcrm\Elasticsearch\Analysis\AnalysisBuilder;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Mapping;
use Sugarcrm\Sugarcrm\Elasticsearch\Adapter\Document;
use Sugarcrm\Sugarcrm\Elasticsearch\Provider\Visibility\Visibility;

/**
 * Portal visibility class replaces the team security restrictions for portal users
 * For non-portal users this class will not modify the query in any way.
 */
class SupportPortalVisibility extends SugarVisibility implements StrategyInterface
{
    /**
     * Ignore visibilty query for the following beans
     * @var array
     */
    protected $modulesToIgnore = [
        'Teams',
        'TeamSets',
    ];

    /**
     * Mapping base to use in locating the proper visibility strategy classes
     * @var array
     */
    protected $classMap = [
        'path' => 'data/visibility/portal/%s.php',
        'ns' => 'Sugarcrm\\Sugarcrm\\Visibility\\Portal\\%s',
    ];

    /**
     * Ignore visibility query for specific beans
     *
     * @param \SugarBean $bean
     *
     * @return bool
     */
    protected function ignoreVisibilityQuery(\SugarBean $bean)
    {
        return (safeInArray($bean->getModuleName(), $this->modulesToIgnore)) ? true : false;
    }

    /**
     * Add Visibility to a SugarQuery Object
     *
     * @param SugarQuery $sugarQuery
     * @param array $options
     *
     * @return SugarQuery
     */
    public function addVisibilityQuery(SugarQuery $sugarQuery, $options = [])
    {
        if (!PortalFactory::getInstance('Session')->isActive()) {
            $GLOBALS['log']->error('Not a portal user, but running through the portal visibility class.');
            return $sugarQuery;
        }

        if ($this->ignoreVisibilityQuery($this->bean)) {
            return $sugarQuery;
        }

        $strategy = $this->getVisibilityStrategy();
        if (empty($options['table_alias'])) {
            $options['table_alias'] = $this->getOption('table_alias');
            if (empty($options['table_alias'])) {
                $options['table_alias'] = DBManagerFactory::getInstance()->getValidDBName($this->bean->getTableName(), true, 'table');
            }
        }

        $strategy->addVisibilityQuery($sugarQuery, $options);
        return $sugarQuery;
    }

    /**
     * Get module-specific strategy
     *
     * @return Sugarcrm\Sugarcrm\Visibility\Portal\ModuleVisibility|null;
     */
    protected function getVisibilityStrategy()
    {
        $class = $this->getVisibilityStrategyClass($this->bean);
        $strategy = new $class(PortalFactory::getInstance('Session')->getVisibilityContext($this->bean));
        if (!($strategy instanceof PortalStrategy\Module)) {
            throw new \Exception('Invalid portal visibility strategy for ' . $this->bean->module_name);
        }
        return $strategy;
    }

    /**
     * Get strategy class name
     *
     * @param \SugarBean $bean Bean
     *
     * @return string
     */
    protected function getVisibilityStrategyClass(\SugarBean $bean)
    {
        $hiddenVisibility = 'Hidden';
        $visibilityClass = $GLOBALS['dictionary'][$bean->getObjectName()]['portal_visibility']['class'] ?? $hiddenVisibility;

        \SugarAutoLoader::requireWithCustom(sprintf($this->classMap['path'], $visibilityClass));
        $returnClass = \SugarAutoLoader::customClass(sprintf($this->classMap['ns'], $visibilityClass));

        if (empty($returnClass) || !class_exists($returnClass)) {
            \SugarAutoLoader::requireWithCustom(sprintf($this->classMap['path'], $hiddenVisibility));
            $returnClass = \SugarAutoLoader::customClass(sprintf($this->classMap['ns'], $hiddenVisibility));
        }

        return $returnClass;
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
        // nothing to do here
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
        $strategy = $this->getVisibilityStrategy();
        if (!method_exists($strategy, 'elasticAddFilters')) {
            return;
        }
        $strategy->elasticAddFilters($user, $filter, $provider);
    }
}
