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

namespace Sugarcrm\Sugarcrm\UserUtils\Invoker\payloads;

/**
 * The InvokerFiltersPayload class handles payloads related to filters
 */
class InvokerFiltersPayload extends InvokerBasePayload
{
    /**
     * The modules in the command
     *
     * @var mixed
     */
    protected $modules;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        parent::__construct($options);
        $this->filters = $options['filters'] ?? [];
        $this->modules = $options['modules'] ?? [];
    }

    /**
     * Setter for filters involved in the command
     *
     * @param array $dashboards
     */
    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * Setter for modules
     *
     * @param array $modules
     */
    public function setModules(array $modules): void
    {
        $this->modules = $modules;
    }

    /**
     * Getter for filters
     *
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Getter for modules
     *
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }
}
