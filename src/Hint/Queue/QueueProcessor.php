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

namespace Sugarcrm\Sugarcrm\Hint\Queue;

use Sugarcrm\Sugarcrm\Hint\HintConstants;
use Sugarcrm\Sugarcrm\Hint\Iss\Commands;
use Sugarcrm\Sugarcrm\Hint\ConfigurationManager;
use Sugarcrm\Sugarcrm\Hint\Logger\Logger as HintLogger;
use Sugarcrm\Sugarcrm\Hint\Manager as HintManager;
use Sugarcrm\Sugarcrm\Hint\Iss\Manager as IssManager;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class QueueProcessor implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    use QueueTrait;

    /**
     * @var EventConverter
     */
    private $eventConverter;

    /**
     * @var ProcessorFactory
     */
    private $processorFactory;

    /**
     * @var IssManager
     */
    private $issManager;


    /**
     * EventQueueProcessor constructor.
     */
    public function __construct()
    {
        $this->eventQueue = $this->getEventQueue();
        $this->eventConverter = $this->getEventConverter();
        $this->processorFactory = $this->getProcessorFactory();
        $this->issManager = $this->getIssManager();
        $this->setLogger(new HintLogger());
    }

    /**
     * Processes queue items and sends results to ISS
     */
    public function processQueue()
    {
        $this->eventQueue->resetStaleEvents();

        $events = $this->eventQueue->getQueuedEvents();
        // nothing to process, exit early
        if (!$events) {
            return;
        }

        $unconditionallyExecutedCommands = [
            Commands::ISS_SYNCHRONIZE_INSTANCE,
            Commands::ISS_RECORD_NEW_INSTANCE,
            Commands::ISS_INIT_CLONE_INSTANCE,
            Commands::ISS_SYNCHRONIZE_INSTANCE_COMPLETED,
            Commands::ISS_RECORD_NEW_INSTANCE_COMPLETED,
            Commands::ISS_INIT_CLONE_INSTANCE_COMPLETED,
            Commands::ISS_DELETE_INSTANCE,
            Commands::ISS_ENABLE_NOTIFICATIONS,
            Commands::ISS_UPDATE_LICENSE,
            Commands::ISS_DISABLE_NOTIFICATIONS,
        ];

        $notificationsRow = ConfigurationManager::getHintConfigEntry(HintConstants::HINT_CONFIG_NOTIFICATION);
        $notificationsDisabled = $notificationsRow ? $notificationsRow['value'] : null;

        $issCommands = [];
        try {
            foreach ($this->eventConverter->convert($events) as $event) {
                $this->logger->info(sprintf('Got event "%s": %s', $event['type'], $event['data']));

                $eventData = json_decode($event['data'], true);
                $command = $this->processorFactory->getProcessor($event['type'])($eventData);

                // If we aren't disabled, push the command
                if ($notificationsDisabled === '' || $notificationsDisabled === null || $notificationsDisabled == false) {
                    $issCommands[] = $command;
                } else {
                    // Make sure the event is one of the commands that are executed unconditionally
                    if (in_array($event['type'], $unconditionallyExecutedCommands) == true) {
                        $this->logger->info(sprintf('Pushing unconditionally executed command %s', $event['type']));
                        $issCommands[] = $command;
                    }
                }
            }

            // Only call send if we have commands to send, for efficiency
            if (safeCount($issCommands) > 0) {
                $this->issManager->sendCommands($issCommands);
            }
            $this->eventQueue->finishedProcessingEvents();
        } catch (\Throwable $e) {
            $this->logger->alert(sprintf('Problem processing event queue, error: %s', $e->getMessage()));
        }
    }

    /**
     * Get processor factory
     *
     * @return ProcessorFactory
     */
    protected function getProcessorFactory(): ProcessorFactory
    {
        return new ProcessorFactory();
    }

    /**
     * Get event converter
     *
     * This is a temporary layer to handle old types of events (from 5.0.2) which
     * already exist in the queue. We can completely remove it after some releases.
     *
     * @return EventConverter
     */
    protected function getEventConverter(): EventConverter
    {
        return new EventConverter();
    }

    /**
     * Get ISS manager
     *
     * @return IssManager
     */
    protected function getIssManager(): IssManager
    {
        $hintManager = HintManager::instance();

        return new IssManager($hintManager->issServiceUrl);
    }
}
