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

namespace Sugarcrm\Sugarcrm\modules\HealthCheck\Scanner\Checks;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

/**
 * @coversDefaultClass HealthCheckScanner
 */
class Dbal
{
    public function check(string $contents)
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        try {
            $stmts = $parser->parse($contents);
        } catch (\PhpParser\Error $error) {
            return [];
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new ParentConnectingVisitor());
        $dbalUpgradeVisitor = new DbalUpgradeVisitor();
        $traverser->addVisitor($dbalUpgradeVisitor);
        $traverser->traverse($stmts);

        return $dbalUpgradeVisitor->getIssues();
    }
}

final class DbalUpgradeVisitor extends NodeVisitorAbstract
{
    /**
     * @var DbalUpgradeIssue[]
     */
    protected $issues = [];

    /**
     * @return DbalUpgradeIssue[]
     */
    public function getIssues(): array
    {
        return $this->issues;
    }

    /** @var VariableScopeStack */
    private $scope;

    private $bannedClassNames = [
        'Doctrine\DBAL\DBALException',
        'Doctrine\DBAL\Abstraction\Result',
        'Doctrine\DBAL\Schema\Synchronizer\SchemaSynchronizer',
        'Doctrine\DBAL\Connections\MasterSlaveConnection',
        'Doctrine\DBAL\Cache\ArrayStatement',
        'Doctrine\DBAL\Cache\ResultCacheStatement',
        \Doctrine\DBAL\FetchMode::class,
        'Doctrine\DBAL\Platforms\MsSQLKeywords',
        'Doctrine\DBAL\Version',
    ];

    private $bannedConstantNames = [
        'Doctrine\DBAL\Connection::TRANSACTION_READ_UNCOMMITTED',
        'Doctrine\DBAL\Connection::TRANSACTION_READ_COMMITTED',
        'Doctrine\DBAL\Connection::TRANSACTION_REPEATABLE_READ',
        'Doctrine\DBAL\Connection::TRANSACTION_SERIALIZABLE',
        'Doctrine\DBAL\Platforms\AbstractPlatform::DATE_INTERVAL_UNIT_SECOND',
        'Doctrine\DBAL\Platforms\AbstractPlatform::DATE_INTERVAL_UNIT_MINUTE',
        'Doctrine\DBAL\Platforms\AbstractPlatform::DATE_INTERVAL_UNIT_HOUR',
        'Doctrine\DBAL\Platforms\AbstractPlatform::DATE_INTERVAL_UNIT_DAY',
        'Doctrine\DBAL\Platforms\AbstractPlatform::DATE_INTERVAL_UNIT_WEEK',
        'Doctrine\DBAL\Platforms\AbstractPlatform::DATE_INTERVAL_UNIT_MONTH',
        'Doctrine\DBAL\Platforms\AbstractPlatform::DATE_INTERVAL_UNIT_QUARTER',
        'Doctrine\DBAL\Platforms\AbstractPlatform::DATE_INTERVAL_UNIT_YEAR',
        'Doctrine\DBAL\Platforms\AbstractPlatform::TRIM_UNSPECIFIED',
        'Doctrine\DBAL\Platforms\AbstractPlatform::TRIM_LEADING',
        'Doctrine\DBAL\Platforms\AbstractPlatform::TRIM_TRAILING',
        'Doctrine\DBAL\Platforms\AbstractPlatform::TRIM_BOTH',
    ];

    /**
     * List of methods, which accept substitution params for SQL query, with position number of that params argument
     * Used for finding one-based param lists and param names with leading colon
     *
     * @var array
     */
    private $methodsWithParams = [
        'fetchAssociative' => 1,
        'fetchNumeric' => 1,
        'fetchOne' => 1,
        'fetchAllNumeric' => 1,
        'fetchAllAssociative' => 1,
        'fetchAllKeyValue' => 1,
        'fetchAllAssociativeIndexed' => 1,
        'fetchFirstColumn' => 1,
        'iterateNumeric' => 1,
        'iterateAssociative' => 1,
        'iterateKeyValue' => 1,
        'iterateAssociativeIndexed' => 1,
        'iterateColumn' => 1,
        'executeQuery' => 1,
        'executeCacheQuery' => 1,
        'executeStatement' => 1,
        'executeUpdate' => 1,
        'bindValue' => 0,
        'bindParam' => 0,
        'execute' => 1,
    ];

    public function __construct()
    {
        $this->scope = new VariableScopeStack();

        $this->bannedClassNames = array_map('strtolower', $this->bannedClassNames);

        $constants = [];
        foreach ($this->bannedConstantNames as $const) {
            [$class, $name] = explode('::', $const);
            $constants[$name] = strtolower($class);
        }
        $this->bannedConstantNames = $constants;

        $methods = [];
        foreach ($this->methodsWithParams as $method => $number) {
            $methods[strtolower($method)] = $number;
        }
        $this->methodsWithParams = $methods;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassMethod || $node instanceof Node\Stmt\Function_) {
            $this->scope->push();
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassMethod || $node instanceof Node\Stmt\Function_) {
            $this->scope->pop();
        } elseif ($node instanceof Node\Expr\Assign && $node->var instanceof Node\Expr\Variable) {
            $varName = $this->nodeNameToString($node->var);
            if ($varName !== '') {
                $this->scope->set($varName, $node->expr);
            }
        } elseif ($node instanceof Node\Name && $this->isBannedClass($node->toString())) {
            $this->issues[] = new DbalUpgradeIssue('Removed class usage: ' . $node->toString(), $node->getLine());
            return;
        } elseif ($node instanceof Node\Expr\ClassConstFetch) {
            $class = $node->class;
            $name = $node->name;
            if ($class instanceof Node\Name && $name instanceof Node\Identifier) {
                if ($this->isBannedConstant($class->toString(), $name->toString())) {
                    $this->issues[] = new DbalUpgradeIssue('Removed constant usage: ' . $class->toString() . '::' . $name->toString(), $node->getLine());
                    return;
                }
            }
        } elseif ($node instanceof Node\Expr\MethodCall) {
            $paramsArg = $this->getParamsArgument($node);
            if ($paramsArg !== null) {
                $array = $this->arrayNodeToArray($paramsArg);
                if ($this->hasLeadingSemicolon($array)) {
                    $this->issues[] = new DbalUpgradeIssue('Leading colon in query param name', $node->getLine());
                }
                if ($this->isOneBased($array)) {
                    $this->issues[] = new DbalUpgradeIssue('One-based numeric array of params', $node->getLine());
                }
            }

            if ($this->nodeNameToLowerString($node) === 'setfetchmode') {
                $this->issues[] = new DbalUpgradeIssue('Removed method usage: setFetchMode', $node->getLine());
            } elseif ($this->nodeNameToLowerString($node) === 'closecursor') {
                $this->issues[] = new DbalUpgradeIssue('Removed method usage: closeCursor', $node->getLine());
            } elseif ($this->isOldFetchMethod($this->nodeNameToLowerString($node)) && $this->isDbalResult($node->var)) {
                $this->issues[] = new DbalUpgradeIssue('Removed method usage: ' . $this->nodeNameToString($node), $node->getLine());
            }
        } elseif ($node instanceof Node\Stmt\Foreach_ && $this->isDbalResult($node->expr)) {
            $this->issues[] = new DbalUpgradeIssue('Iteration over statement instead of result', $node->getLine());
            return;
        }
    }

    protected function isBannedClass(string $name): bool
    {
        return in_array(strtolower($name), $this->bannedClassNames);
    }

    protected function isBannedConstant(string $class, string $name)
    {
        return isset($this->bannedConstantNames[$name]) && $this->bannedConstantNames[$name] === strtolower($class);
    }

    protected function getParamsArgument(Node\Expr\MethodCall $node): ?Node\Expr\Array_
    {
        $number = $this->methodsWithParams[$this->nodeNameToLowerString($node)] ?? null;
        if ($number === null) {
            return null;
        }
        $arg = isset($node->args[$number]) ? $node->args[$number]->value : null;
        if ($arg === null) {
            return null;
        }
        if ($arg instanceof Node\Expr\Variable) {
            $arg = $this->resolveVar($arg);
        }
        if ($arg instanceof Node\Expr\Array_) {
            return $arg;
        }
        return null;
    }

    protected function arrayNodeToArray(Node\Expr\Array_ $node): array
    {
        $result = [];
        foreach ($node->items as $item) {
            if ($item->key instanceof Node\Scalar\String_ || $item->key instanceof Node\Scalar\LNumber) {
                $result[$item->key->value] = $item->value; // we only need array keys
            }
        }
        return $result;
    }

    protected function hasLeadingSemicolon(array $array): bool
    {
        foreach ($array as $key => $value) {
            if (is_string($key) && $key[0] === ':') {
                return true;
            }
        }
        return false;
    }

    protected function isOneBased(array $array): bool
    {
        return array_key_exists(1, $array) && !array_key_exists(0, $array);
    }

    /**
     * Check if node is a dbal\result object itself, non-iterated
     */
    protected function isDbalResult(?Node $node): bool
    {
        if ($node === null) {
            return false;
        }
        if ($node instanceof Node\Expr\Variable) {
            return $this->isDbalResult($this->resolveVar($node));
        }
        if ($node instanceof Node\Expr\MethodCall) {
            if ($this->isFetchMethod($node)) {
                return false;
            } elseif ($this->isQueryMethod($node) && $this->isDbalConnection($node->var)) {
                return true;
            } elseif ($this->isQueryMethod($node) && $this->isQBInstance($node->var)) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Methods of Result object, which retrieve/iterate results of performed SQL query
     */
    protected function isFetchMethod(Node\Expr\MethodCall $node): bool
    {
        $methodName = $this->nodeNameToLowerString($node);
        return in_array($methodName, array_map('strtolower', [
            'fetchAssociative',
            'fetchNumeric',
            'fetchOne',
            'fetchAllNumeric',
            'fetchAllAssociative',
            'fetchAllKeyValue',
            'fetchAllAssociativeIndexed',
            'fetchFirstColumn',
            'iterateNumeric',
            'iterateAssociative',
            'iterateKeyValue',
            'iterateAssociativeIndexed',
            'iterateColumn',
        ]));
    }

    /**
     * Methods, which execute SQL query and return Result object
     */
    protected function isQueryMethod(Node\Expr\MethodCall $node): bool
    {
        $methodName = $this->nodeNameToLowerString($node);
        return in_array($methodName, array_map('strtolower', [
            'executeQuery',
            'executeCacheQuery',
            'query',
            'execute',
            'exec',
        ]));
    }

    /**
     * Check if a node is (likely) a DBAL connection. It is very vague, but in the context of checking the subject of
     * execute-like methods it should be good enough
     */
    protected function isDbalConnection(Node $node): bool
    {
        if ($node instanceof Node\Expr\Variable) {
            $node = $this->resolveVar($node);
        }
        if ($node instanceof Node\Expr\MethodCall && $this->nodeNameToLowerString($node) === 'getconnection') {
            return true;
        }
        if ($node instanceof Node\Expr\StaticCall && $this->nodeNameToLowerString($node) === 'getconnection') {
            return true;
        }
        return false;
    }

    protected function isOldFetchMethod(string $name): bool
    {
        return in_array($name, array_map('strtolower', [
            'fetchColumn',
            'fetchArray',
            'fetchAssoc',
        ]));
    }

    protected function resolveVar(Node\Expr\Variable $node, array $tail = []): ?Node
    {
        $name = $this->nodeNameToString($node);
        if ($name === '' || in_array($name, $tail)) {
            return null;
        }
        $tail[] = $name;
        $result = $this->scope->get($name);
        if ($result instanceof Node\Expr\Variable) {
            return $this->resolveVar($result, $tail);
        } else {
            return $result;
        }
    }

    /**
     * to resolve calling invalid method 'toLowerString' for Expr object
     * @param Node $node
     * @return string
     */
    protected function nodeNameToLowerString(Node $node): string
    {
        if (is_string($node->name)) {
            return \strtolower($node->name);
        }
        if (method_exists($node->name, 'toLowerString')) {
            return $node->name->toLowerString();
        }
        return '';
    }

    /**
     * to resolve calling invalid method 'toString' for Expr object
     * @param Node $node
     * @return string
     */
    protected function nodeNameToString(Node $node): string
    {
        if (is_string($node->name)) {
            return $node->name;
        }
        if (method_exists($node->name, 'toString')) {
            return $node->name->toString();
        }
        return '';
    }

    protected function isQBInstance(Node $node): bool
    {
        if ($node instanceof Node\Expr\Variable) {
            $node = $this->resolveVar($node);
        }
        if ($node instanceof Node\Expr\MethodCall) {
            if ($this->nodeNameToLowerString($node) === 'createquerybuilder') {
                return true;
            } else {
                return $this->isQBInstance($node->var);
            }
        }
        return false;
    }
}

final class DbalUpgradeIssue
{
    /** @var string */
    private $message;
    /** @var int */
    private $line;

    public function __construct(string $message, int $line)
    {
        $this->message = $message;
        $this->line = $line;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getLine(): int
    {
        return $this->line;
    }
}

final class VariableScope
{
    private $vars = [];
    /** @var self */
    private $parent;

    public function __construct(?VariableScope $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent(): self
    {
        return $this->parent;
    }

    public function set($name, $value)
    {
        $this->vars[$name] = $value;
    }

    public function get($name)
    {
        return $this->vars[$name] ?? null;
    }
}

final class VariableScopeStack
{
    /** @var VariableScope */
    private $scope;

    public function __construct()
    {
        $this->scope = new VariableScope();
    }

    public function push()
    {
        $this->scope = new VariableScope($this->scope);
    }

    public function pop()
    {
        $this->scope = $this->scope->getParent();
    }

    public function set($name, $value)
    {
        $this->scope->set($name, $value);
    }

    public function get($name)
    {
        return $this->scope->get($name);
    }
}
