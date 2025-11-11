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

namespace Sugarcrm\Sugarcrm\UserUtils;

use Sugarcrm\Sugarcrm\UserUtils\CommandInterface;
use Sugarcrm\Sugarcrm\UserUtils\Invoker\InvokerPayload;
use Sugarcrm\Sugarcrm\UserUtils\Managers\ManagerFactory;

/**
 * The Command base class.
 */
class Command implements CommandInterface
{
    public $type;
    /**
     * The manager
     *
     * @var Manager
     */
    private $manager;

    /**
     * Command constructor
     *
     * @param InvokerPayload $payload
     */
    public function __construct(InvokerPayload $payload)
    {
        $this->manager = ManagerFactory::getManager($this->type, $payload);
        $this->manager->setType($this->type);
    }

    /**
     * Getter for managers
     *
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Setter for mananger
     *
     * @param Manager $manager
     */
    public function setManager($manager): void
    {
        $this->manager = $manager;
    }

    /**
     * Performs command
     */
    public function execute(): void
    {
    }
}
