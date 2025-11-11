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
use PhpParser\Node\Scalar\String_;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\InvalidExtensionDetected;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\PathTraversalDetected;
use Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues\StreamWrapperUsed;

class IncludesVisitor extends ForbiddenStatementVisitor
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\Include_ && $node->expr instanceof String_) {
            $filename = $node->expr->value;
            if (str_contains($filename, '://')) {
                $this->issues[] = new StreamWrapperUsed($node->getLine());
            }
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if ($ext !== 'php') {
                $this->issues[] = new InvalidExtensionDetected($node->getLine());
            }
            if (str_contains($filename, '..') || is_absolute_path($filename)) {
                $this->issues[] = new PathTraversalDetected($node->getLine());
            }
        }
    }
}
