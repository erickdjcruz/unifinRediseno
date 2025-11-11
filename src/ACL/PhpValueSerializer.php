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

namespace Sugarcrm\Sugarcrm\ACL;

final class PhpValueSerializer implements ValueSerializer
{
    public function serialize($value): string
    {
        return serialize($value);
    }

    public function unserialize(string $value)
    {
        return unserialize($value, ['allowed_classes' => false]);
    }
}
