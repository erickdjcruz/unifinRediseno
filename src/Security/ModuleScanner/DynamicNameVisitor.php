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
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\DynamicallyNamedClassUsed;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\DynamicallyNamedClassInstantiated;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\DynamicallyNamedFunctionCalled;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\DynamicallyNamedMethodCalled;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\DynamicallyNamedStaticMethodCalled;

class DynamicNameVisitor extends ForbiddenStatementVisitor
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\MethodCall) {
            if ($this->isNameDynamicallyCalled($node->name)) {
                $this->issues[] = new DynamicallyNamedMethodCalled($node->getLine());
            }
        } elseif ($node instanceof Node\Expr\FuncCall) {
            if ($this->isNameDynamicallyCalled($node->name)) {
                $this->issues[] = new DynamicallyNamedFunctionCalled($node->getLine());
            }
        } elseif ($node instanceof Node\Expr\StaticCall) {
            $class = $node->class;
            $method = $node->name;
            if ($this->isNameDynamicallyCalled($class)) {
                $this->issues[] = new DynamicallyNamedClassUsed($node->getLine());
            }
            if ($this->isNameDynamicallyCalled($method)) {
                $this->issues[] = new DynamicallyNamedStaticMethodCalled($node->getLine());
            }
        } elseif ($node instanceof Node\Expr\New_) {
            if ($this->isNameDynamicallyCalled($node->class)) {
                $this->issues[] = new DynamicallyNamedClassInstantiated($node->getLine());
            }
        }
    }

    public function isNameDynamicallyCalled($name): bool
    {
        if ($name instanceof Node\Expr\Variable ||
            $name instanceof Node\Expr\ConstFetch ||
            $name instanceof Node\Expr\FuncCall ||
            $name instanceof Node\Expr\PropertyFetch ||
            $name instanceof Node\Expr\ClassConstFetch ||
            $name instanceof Node\Expr\MethodCall ||
            $name instanceof Node\Expr\StaticCall ||
            $name instanceof Node\Expr\BinaryOp\Concat ||
            $name instanceof Node\Expr\Cast\String_ ||
            $name instanceof Node\Scalar\String_ ||
            $name instanceof Node\Expr\ArrayDimFetch ||
            $name instanceof Node\Scalar\Encapsed
        ) {
            return true;
        }

        return false;
    }
}
