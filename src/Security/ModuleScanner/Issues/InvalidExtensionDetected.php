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

namespace Sugarcrm\Sugarcrm\Security\ModuleScanner\Issues;

final class InvalidExtensionDetected implements Issue
{
    private int $line;

    public function __construct(int $line)
    {
        $this->line = $line;
    }

    public function getMessage(): string
    {
        return 'Code attempted to include non .php file on line ' . $this->line;
    }
}
