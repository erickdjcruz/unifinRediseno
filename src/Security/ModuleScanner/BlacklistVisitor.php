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
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\BlacklistedClassExtended;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\BlacklistedClassInstantiated;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\BlacklistedFunctionCalled;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\BlacklistedMethodCalled;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\BlacklistedStaticMethodCalled;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\BlacklistedStaticMethodOfClassCalled;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\EvalUsed;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\CompilerHalted;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\ShellExecUsed;

class BlacklistVisitor extends ForbiddenStatementVisitor
{
    private $classesBlackList;

    private $functionsBlackList;

    private $methodsBlackList;

    public function __construct(array $classesBlackList, array $functionsBlackList, array $methodsBlackList)
    {
        $this->classesBlackList = $classesBlackList;
        $this->functionsBlackList = $functionsBlackList;
        $this->methodsBlackList = $methodsBlackList;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\Eval_) {
            $this->issues[] = new EvalUsed($node->getLine());
        }

        if ($node instanceof Node\Stmt\HaltCompiler) {
            $this->issues[] = new CompilerHalted($node->getLine());
        }
        if ($node instanceof Node\Expr\ShellExec) {
            $this->issues[] = new ShellExecUsed($node->getLine());
        }

        if ($node instanceof Node\Expr\MethodCall) {
            $method = null;
            if ($node->name instanceof Node\Identifier) {
                $method = $node->name->toString();
            }
            if ($node->name instanceof Node\Scalar\String_) {
                $method = $node->name->value;
            }
            if ($method !== null && in_array(strtolower($method), $this->methodsBlackList, true)) {
                $this->issues[] = new BlacklistedMethodCalled($method, $node->getLine());
            }
        } elseif ($node instanceof Node\Expr\FuncCall) {
            $function = null;
            if ($node->name instanceof Node\Name) {
                $function = $node->name->toString();
            }
            if ($node->name instanceof Node\Scalar\String_) {
                $function = $node->name->value;
            }
            if ($function !== null && in_array(strtolower($function), $this->functionsBlackList, true)) {
                $this->issues[] = new BlacklistedFunctionCalled($function, $node->getLine());
            }
        } elseif ($node instanceof Node\Stmt\Class_) {
            if ($node->extends instanceof Node\Name) {
                $class = $node->extends->toString();
                if (in_array(strtolower($class), $this->classesBlackList, true)) {
                    $this->issues[] = new BlacklistedClassExtended($class, $node->getLine());
                }
            }
        } elseif ($node instanceof Node\Expr\New_) {
            $class = null;
            if ($node->class instanceof Node\Name) {
                $class = $node->class->toString();
            }
            if ($class !== null && in_array(strtolower($class), $this->classesBlackList, true)) {
                $this->issues[] = new BlacklistedClassInstantiated($class, $node->getLine());
            }
        } elseif ($node instanceof Node\Expr\StaticCall) {
            $method = null;
            $class = null;
            if ($node->class instanceof Node\Name) {
                $class = $node->class->toString();
            }
            if ($node->name instanceof Node\Identifier) {
                $method = $node->name->toString();
            }
            if ($node->class instanceof Node\Scalar\String_) {
                $class = $node->class->value;
            }
            if ($node->name instanceof Node\Scalar\String_) {
                $method = $node->name->value;
            }
            if ($method !== null && in_array(strtolower($method), $this->methodsBlackList, true)) {
                $this->issues[] = new BlacklistedStaticMethodCalled($method, $node->getLine());
            }
            if ($method !== null && $class !== null && isset($this->methodsBlackList[strtolower($method)]) && in_array(strtolower($class), $this->methodsBlackList[strtolower($method)], true)) {
                $this->issues[] = new BlacklistedStaticMethodOfClassCalled($class, $method, $node->getLine());
            }
        }
    }
}
