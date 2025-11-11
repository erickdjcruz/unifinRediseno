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

namespace Sugarcrm\Sugarcrm\Security\ModuleScanner;

use PhpParser\NodeVisitorAbstract;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\Issue;

abstract class ForbiddenStatementVisitor extends NodeVisitorAbstract
{
    /**
     * @var Issue[]
     */
    protected $issues = [];

    /**
     * @return Issue[]
     */
    public function getIssues(): array
    {
        return $this->issues;
    }

    /**
     * Reset issues list before traversing to prevent issues sharing between different calls
     * @param array $nodes
     * @return null
     */
    public function beforeTraverse(array $nodes)
    {
        $this->issues = [];
        return null;
    }
}
