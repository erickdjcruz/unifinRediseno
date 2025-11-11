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
 * Class Mailbox A mailbox name consists of a server and a mailbox path on this server.
 */
final class Mailbox
{
    /**
     * @var RemoteSystemName Internet domain name or IP address of server
     */
    private $remoteSystemName;

    /**
     * @var int TCP port number
     */
    private $port;

    /**
     * @var array optional flags, see https://www.php.net/manual/en/function.imap-open.php for complete list
     */
    private $flags;

    /**
     * @var string Remote mailbox name, default is INBOX
     */
    private $mailboxName;

    private function __construct()
    {
    }

    public static function fromRemoteSystemName(RemoteSystemName $remoteSystemName, int $port, array $flags = [], string $mailboxName = 'INBOX'): self
    {
        $mailbox = new self();
        $mailbox->remoteSystemName = $remoteSystemName;
        $mailbox->port = $port;
        $mailbox->flags = $flags;
        $mailbox->mailboxName = $mailboxName;
        return $mailbox;
    }

    public function value(): string
    {
        return '{' . $this->getHost() . ':' . $this->getPort() . implode('', $this->flags) . '}' . $this->getMailboxName();
    }

    public function getHost(): string
    {
        return $this->remoteSystemName->value();
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getMailboxName(): string
    {
        return $this->mailboxName;
    }

    public function getSecurityProtocol(): string
    {
        return in_array('/ssl', $this->flags) ? 'ssl' : 'none';
    }
}
