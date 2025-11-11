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

namespace Sugarcrm\Sugarcrm\inc\Entitlements\Exception;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Sugarcrm\Sugarcrm\Logger\Factory as LoggerFactory;
use Throwable;

class SubscriptionException extends \RuntimeException implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        $this->setLogger(LoggerFactory::getLogger('subscription'));
        $this->logger->alert($message);

        parent::__construct($message, $code, $previous);
    }
}
