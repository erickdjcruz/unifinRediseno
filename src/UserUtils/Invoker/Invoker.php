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

namespace Sugarcrm\Sugarcrm\UserUtils\Invoker;

use Sugarcrm\Sugarcrm\UserUtils\CommandFactory;

/**
 * The Invoker is associated with one or several commands. It sends a request to
 * the command.
 */
class Invoker
{
    /**
     * array of commands to be executed
     *
     * @var array<Command>
     */
    private $commands;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $commandData) {
            $payload = InvokerPayloadFactory::getInvokerPayload($commandData);
            $command = CommandFactory::getCommand($commandData['type'], $payload);

            $this->commands[] = $command;
        }
    }

    /**
     * Executes the commands
     */
    public function execute(): void
    {
        foreach ($this->commands as $command) {
            $command->execute();
        }
    }

    /**
     * Getter for commands
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * Setter for commands
     */
    public function setCommands(array $commands)
    {
        $this->commands = $commands;
    }
}
