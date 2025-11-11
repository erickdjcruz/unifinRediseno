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

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;

final class TranslateVisitor extends NodeVisitorAbstract
{
    /**
     * @var Node\Stmt\Function_[]
     */
    private array $customFunctions = [];

    /**
     * @param Node $node
     * @return Node|void|null
     * @see \PhpParser\NodeVisitor::leaveNode()
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\MethodCall) {
            return $this->wrapMethodCall($node);
        } elseif ($node instanceof Node\Expr\FuncCall) {
            return $this->wrapFunctionCall($node);
        } elseif ($node instanceof Node\Expr\StaticCall) {
            return $this->wrapStaticMethodCall($node);
        } elseif ($node instanceof Node\Expr\Include_ && (!$node->expr instanceof String_)) {
            return $this->wrapIncludes($node);
        }
    }

    /**
     * @param Node $node
     * @return Node|Node\Stmt\Function_|void|null
     * @see \PhpParser\NodeVisitor::enterNode()
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Function_) {
            $functionName = $node->name->toString();
            if (strpos($functionName, SweetShield::SWEET_PREFIX) === 0) {
                // if has prefix - probably it was translated already, save name to check corresponding proxy
                $this->addCustomFunction($node, $functionName, false);
                return $node;
            }

            if ($this->isProxy($node)) {
                return $node;
            }

            // non-translated yet function, rename and create proxy
            $functionName = SweetShield::SWEET_PREFIX . $functionName;
            $factory = new BuilderFactory();
            $proxy = $this->createProxyFunction($factory, $node);
            $this->addCustomFunction($node, $functionName, $proxy);

            $node->name = $functionName;
            return $node;
        }
        if ($node instanceof Node\Stmt\Class_) {
            foreach (($node->implements ?? []) as $item) {
                if ('\\' . $item->toString() === SweetShield::SWEET_INTERFACE) {
                    return null;
                }
            }
            $sweetInterface = [new Node\Name(SweetShield::SWEET_INTERFACE)];
            $node->implements = $node->implements ? array_merge($node->implements, $sweetInterface) : $sweetInterface;
        }
    }

    /**
     * Checks if the provided name can be statically resolved
     * @param Node $name Name of the function, method or class
     * @return bool
     */
    private function isNameDynamicallyCalled(Node $name): bool
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

    /**
     * Add proxy functions with original function names for all custom functions in the script to prevent BC breaks
     * @param array $nodes
     * @return array|null
     * @see \PhpParser\NodeVisitor::afterTraverse()
     */
    public function afterTraverse(array $nodes)
    {
        // functions in the namespace
        foreach ($nodes as $node) {
            $functions = array_filter($node->getAttribute('customFunctions', []));
            if (!$functions) {
                continue;
            }
            foreach ($functions as $function) {
                $node->stmts[] = $function;
            }
        }
        // functions in the global scope
        foreach (array_filter($this->customFunctions) as $function) {
            $nodes[] = $function;
        }
        return $nodes;
    }

    /**
     * Wraps method call with a proxy method that checks in the runtime if the target method is allowed to be called
     * @param Node\Expr\MethodCall $node
     * @return Node
     */
    protected function wrapMethodCall(Node\Expr\MethodCall $node): Node
    {
        $factory = new BuilderFactory();
        if ($node->name instanceof Node\Identifier) {
            $methodName = $node->name->name;
        } else {
            $methodName = $node->name;
        }
        $object = $node->var;
        // Allow calls to all methods inside the same object
        if ($object instanceof Variable && (string)$object->name === 'this') {
            return $node;
        }

        $methodArgs = $node->args;
        array_unshift($methodArgs, $object, $methodName);
        return $factory->staticCall('\\' . SweetShield::class, 'callMethod', $methodArgs);
    }

    /**
     * Wraps function call with a proxy method that checks in the runtime if the target function is allowed to be called
     * @param Node\Expr\FuncCall $node
     * @return Node
     */
    protected function wrapFunctionCall(Node\Expr\FuncCall $node): Node
    {
        $funcName = $node->name instanceof Node\Name ? $node->name->toString() : $node->name;
        if ($funcName === 'unserialize') {
            return $this->wrapUnserializeCall($node);
        } else {
            $factory = new BuilderFactory();
            // Do not wrap allowed functions with callFunction()

            if ($node->name instanceof Node\Name && SweetShield::isAllowedFunction($node->name->toString())) {
                return $node;
            }
            $funcArgs = $node->args;
            array_unshift($funcArgs, $funcName);
            return $factory->staticCall('\\' . SweetShield::class, 'callFunction', $funcArgs);
        }
    }

    /**
     * Special case for 'unserialize'. Enforce the second param ['allowed_classes' => false]
     * to prevent PHP Object injection attacks
     *
     * @param Node\Expr\FuncCall $node
     * @return Node\Expr\FuncCall
     */
    protected function wrapUnserializeCall(Node\Expr\FuncCall $node): Node
    {
        $node->args[1] = new Node\Arg(new Node\Expr\Array_(
            [
                new Node\Expr\ArrayItem(
                    new Node\Expr\ConstFetch(new Node\Name('false')),
                    new Node\Scalar\String_('allowed_classes')
                ),
            ]
        ));
        return $node;
    }

    /**
     * Wraps static method call with a proxy method that checks in the runtime if the target method is allowed to be called
     * @param Node\Expr\StaticCall $node
     * @return Node
     */
    protected function wrapStaticMethodCall(Node\Expr\StaticCall $node): Node
    {
        $class = $node->class;
        $method = $node->name;

        /**
         * Taking care only about dynamically named classes and methods, statically resolvable cases
         * should be handled in \Sugarcrm\Sugarcrm\Security\ModuleScanner\BlacklistVisitor
         */
        if ($this->isNameDynamicallyCalled($class) || $this->isNameDynamicallyCalled($method)) {
            $factory = new BuilderFactory();
            if ($class instanceof Node\Name && in_array($class->toString(), ['self', 'static'], true)) {
                return $node;
            }
            if ($node->name instanceof Node\Identifier) {
                $methodName = $node->name->name;
            } else {
                $methodName = $node->name;
            }
            $methodArgs = $node->args;
            array_unshift($methodArgs, $class, $methodName);
            return $factory->staticCall('\\' . SweetShield::class, 'callMethod', $methodArgs);
        }
        return $node;
    }

    /**
     * Validate statically unresolvable filename passed to include/require to prevent Path Traversal attacks
     * and abusing of stream filters and stream wrappers
     * @param Node\Expr\Include_ $node
     * @return Node
     */
    protected function wrapIncludes(Node\Expr\Include_ $node): Node
    {
        if (!($node->expr instanceof Node\Expr\StaticCall
            && $node->expr->class instanceof Node\Name && $node->expr->class->toString() === SweetShield::class
            && $node->expr->name instanceof Node\Identifier && $node->expr->name->toString() === 'validPath')
        ) {
            $factory = new BuilderFactory();
            $node->expr = $factory->staticCall('\\' . SweetShield::class, 'validPath', [$node->expr]);
        }
        return $node;
    }

    /**
     * Creates a proxy function with the original name. Attributes and return type are ignored at the moment
     * @param BuilderFactory $factory
     * @param Node\Stmt\Function_ $node
     * @return Node
     */
    protected function createProxyFunction(BuilderFactory $factory, Node\Stmt\Function_ $node): Node
    {
        return $factory->function((string)$node->name)
            ->addParams($node->getParams())
            ->addStmt(new Node\Stmt\Return_(
                $factory->funcCall(
                    SweetShield::SWEET_PREFIX . $node->name,
                    array_map(function (Node\Param $param) {
                        return $param->var;
                    }, $node->getParams())
                )
            ))
            ->getNode();
    }

    /**
     * Check if the function is a proxy for translated custom function
     *
     * @param Node\Stmt\Function_ $node
     * @return true
     */
    protected function isProxy(Node\Stmt\Function_ $node): bool
    {
        $targetName = SweetShield::SWEET_PREFIX . $node->name->toString();
        if (safeCount($node->stmts) === 1
            && $node->stmts[0] instanceof Node\Stmt\Return_
            && $node->stmts[0]->expr instanceof Node\Expr\FuncCall
            && $node->stmts[0]->expr->name->toString() === $targetName
        ) {
            $parent = $node->getAttribute('parent');
            $functions = $parent ? $parent->getAttribute('customFunctions', []) : $this->customFunctions;
            return isset($functions[$targetName]);
        }
        return false;
    }

    /**
     * @param Node\Stmt\Function_ $node
     * @param string $name
     * @param Node|false $value
     * @return void
     */
    protected function addCustomFunction(Node\Stmt\Function_ $node, string $name, $value): void
    {
        $parent = $node->getAttribute('parent');
        if ($parent) {
            // We are inside the namespace
            $parent->setAttribute('customFunctions', array_merge($parent->getAttribute('customFunctions', []), [$name => $value]));
        } else {
            $this->customFunctions[$name] = $value;
        }
    }
}
