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

use PhpParser\Node;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\IncompatibleMethodSignature;

class MethodSignatureVisitor extends ForbiddenStatementVisitor
{
    private $rules = [];

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function enterNode(Node $node)
    {
        if (safeCount($this->rules) === 0) {
            return;
        }
        if (!$node instanceof Node\Stmt\ClassMethod) {
            return;
        }
        $class = $node->getAttribute('parent');
        if (!$class instanceof Node\Stmt\Class_ || $class->extends === null
            || !isset($this->rules[$class->extends->toString()])) {
            return;
        }
        $rule = $this->rules[$class->extends->toString()][$node->name->toString()] ?? [];
        if (isset($rule['return']) && (!isset($node->returnType) || $node->returnType->toString() !== $rule['return'])) {
            $this->issues[] = new IncompatibleMethodSignature($class->extends->toString(), $node->name->toString(), $node->getLine());
        }
    }
}
