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

namespace Sugarcrm\Sugarcrm\Deprecation;

use Psr\Log\LoggerInterface;

/**
 * Handler to catch deprecation notices, emitted by Symfony components, and log them with provided logger
 */
class Symfony
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        set_error_handler([$this, 'errorHandler'], \E_USER_DEPRECATED);
    }

    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        // check for message format of symfony/deprecation-contracts
        if (0 !== strncmp($errstr, 'Since symfony/', strlen('Since symfony/'))) {
            return false;
        }
        $this->logger->warning($errstr);
    }
}
