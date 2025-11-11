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

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\Issue;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\SyntaxError;

class CodeScanner
{
    /**
     * @var NodeVisitor[]
     */
    private array $visitors = [];

    /**
     * @param NodeVisitor ...$visitor
     * @return $this
     */
    public function registerVisitor(NodeVisitor ...$visitor): CodeScanner
    {
        foreach ($visitor as $v) {
            $this->visitors[] = $v;
        }
        return $this;
    }

    /**
     * @param string $code Code to analyze
     * @return  Issue[]
     */
    public function scan(string $code): array
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        foreach ($this->visitors as $visitor) {
            $traverser->addVisitor($visitor);
        }
        try {
            $stmts = $parser->parse($code);
        } catch (Error $error) {
            return [new SyntaxError($error)];
        }
        $traverser->traverse($stmts);
        $issues = [];
        foreach ($this->visitors as $visitor) {
            $issues = array_merge($issues, $visitor->getIssues());
        }
        return $issues;
    }
}
