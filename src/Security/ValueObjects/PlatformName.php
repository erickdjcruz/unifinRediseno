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

namespace Sugarcrm\Sugarcrm\Security\ValueObjects;

use Assert\Assertion;

final class PlatformName
{
    private $name;

    private function __construct()
    {
    }

    public static function base(): self
    {
        $platformName = new self();
        $platformName->name = 'base';
        return $platformName;
    }

    public static function fromString(string $name): self
    {
        Assertion::betweenLength($name, 1, 127);
        Assertion::regex($name, '/^[a-z0-9\-_]*$/i', sprintf('Invalid Platform Name "%s" (a-z, 0-9, dash and underscore allowed)', $name));
        $platformName = new self();
        $platformName->name = $name;
        return $platformName;
    }

    public function value(): string
    {
        return $this->name;
    }

    public function isBase(): bool
    {
        return $this->name === 'base';
    }
}
