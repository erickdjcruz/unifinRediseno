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

class Portal implements Module
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var db
     */
    protected $db;

    /**
     * Constructor
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
        $this->db = \DBManagerFactory::getInstance();
    }

    public function addVisibilityQuery(\SugarQuery $query, array $options = [])
    {
    }

    /**
     * throws \Exception
     */
    public function addVisibilityFrom(string &$query, array $options = [])
    {
        throw new \Exception('Invalid portal visibility method call for ' . $this->context->getBean()->module_name);
    }

    /**
     * throws \Exception
     */
    public function addVisibilityWhere(string &$query, array $options = [])
    {
        throw new \Exception('Invalid portal visibility method call for ' . $this->context->getBean()->module_name);
    }

    /**
     * @param \User $user
     * @param \Elastica\Query\BoolQuery $filter
     * @param Visibility $provider
     */
    public function elasticAddFilters(\User $user, \Elastica\Query\BoolQuery $filter, Visibility $provider)
    {
        // to be overriden by sub classes
    }
}
