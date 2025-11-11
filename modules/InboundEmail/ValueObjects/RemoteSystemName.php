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

/**
 * Class RemoteSystemName
 * Represents Internet domain name or IP address of server, which is expected to be used as
 * remote_system_name part of $mailbox parameter of imap_open.
 * @see https://www.php.net/manual/en/function.imap-open.php
 */
final class RemoteSystemName
{
    /**
     * @var string
     */
    private $value;

    private function __construct()
    {
    }

    public static function fromString(string $value): self
    {
        if (!filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new \DomainException(
                sprintf('Expected internet domain name or IP address, "%s" given', $value)
            );
        }

        $remoteSystemName = new self();
        $remoteSystemName->value = $value;
        return $remoteSystemName;
    }

    public function value(): string
    {
        return $this->value;
    }
}
