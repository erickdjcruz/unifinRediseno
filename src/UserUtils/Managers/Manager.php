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

namespace Sugarcrm\Sugarcrm\UserUtils\Managers;

use SchedulersJob;
use Sugarcrm\Sugarcrm\Util\Uuid;
use SugarJobQueue;

/**
 * The Manager base class.
 */
class Manager
{
    /** @var bool */
    protected $useScheduledJob;
    /**
     * The maximum number of users without using a scheduled job
     */
    protected const MAX_USER = 100;

    /**
     * The manager type
     *
     * @var string
     */
    protected $type;

    /**
     * The payload
     *
     * @var InvokerPayload
     */
    protected $payload;

    /**
     * Clones using a schedule job
     */
    public function cloneWithScheduledJob(): void
    {
        global $current_user;

        $payload = $this->payload->serialize();

        $job = new SchedulersJob();
        $job->name = 'UserUtilities ' . Uuid::uuid4();
        $job->data = base64_encode($payload);
        $job->target = 'class::UserUtilitiesJob';
        $job->assigned_user_id = $current_user->id;
        $jq = new SugarJobQueue();
        $jq->submitJob($job);
    }

    /**
     * Setter for type.
     *
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Sets the useScheduledJob variable to false;
     */
    public function dontUseScheduledJob(): void
    {
        $this->useScheduledJob = false;
    }
}
