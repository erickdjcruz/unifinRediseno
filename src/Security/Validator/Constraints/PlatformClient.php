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

namespace Sugarcrm\Sugarcrm\Security\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 *
 * @see PlatformClientValidator
 *
 */
class PlatformClient extends Constraint
{
    public const ERROR_INVALID_PLATFORM_CLIENT = 0;

    /**
     * {@inheritdoc}
     */
    protected static $errorNames = [
        self::ERROR_INVALID_PLATFORM_CLIENT => 'ERROR_INVALID_PLATFORM_CLIENT',
    ];

    /**
     * Message template
     * @var string
     */
    public string $message = 'Platform Client name violation: %reason% (%platform%:%client%)';

    public string $platform;
}
