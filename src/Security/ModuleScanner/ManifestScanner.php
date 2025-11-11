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
use PhpParser\Node\Expr\CallLike;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\ForbiddenManifestExpressionUsed;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\InvalidManifestFormat;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\Issue;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\SyntaxError;
use PhpParser\Node;

class ManifestScanner
{
    /**
     * @param string $code Code to analyze
     * @return  Issue[]
     */
    public function scan(string $code): array
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            return [new SyntaxError($error)];
        }
        $nodeFinder = new NodeFinder;

        $forbiddenNodes = $nodeFinder->find($ast, function ($node) {
            if ($node instanceof CallLike) {
                return true;
            }
            $denyList = [
                Node\Expr\Include_::class,
                Node\Expr\Eval_::class,
                Node\Expr\Print_::class,
                Node\Stmt\Echo_::class,
                Node\Expr\ArrowFunction::class,
                Node\Expr\ShellExec::class,
            ];
            return in_array(get_class($node), $denyList);
        });
        $issues = [];
        if (count($forbiddenNodes)) {
            foreach ($forbiddenNodes as $node) {
                $issues[] = new ForbiddenManifestExpressionUsed($node->getLine());
            }
        }

        $statements = $nodeFinder->findInstanceOf($ast, Node\Expr\Variable::class);
        if (count($statements) < 2) {
            $issues[] = new InvalidManifestFormat('Both $manifest and $installdefs are expected to be defined');
        }
        foreach ($statements as $stmt) {
            if (!in_array($stmt->name, ['installdefs', 'manifest'])) {
                $issues[] = new InvalidManifestFormat('Manifest can contain only two variables: $manifest and $installdefs');
            }
        }
        return $issues;
    }
}
