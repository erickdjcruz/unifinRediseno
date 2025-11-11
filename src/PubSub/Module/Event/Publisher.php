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

namespace Sugarcrm\Sugarcrm\PubSub\Module\Event;

use Exception;
use Psr\Log\LoggerInterface;
use PubSub_ModuleEvent_PushSub;
use SugarBean;
use SugarConfig;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\LogicHooks\Module\EventHandlerInterface;
use Sugarcrm\Sugarcrm\PubSub\Module\Event\PushSubscriptionPublisher;
use TimeDate;

final class Publisher implements EventHandlerInterface
{
    /**
     * Reports if subscriptions to the module are allowed.
     *
     * @param string $module The module name.
     *
     * @return bool
     */
    public static function isModuleAllowed(string $module): bool
    {
        $moduleNameDenylist = [
            'PubSub_ModuleEvent_PushSubs',
        ];

        return !empty($module) && !in_array($module, $moduleNameDenylist);
    }

    /**
     * Handles a module logic hooks event.
     *
     * @param SugarBean $bean The impacted record.
     * @param string $event The event type.
     * @param array $args Additional arguments.
     *
     * @return void
     * @uses Publisher::publishEvent to publish the event to subscribers.
     */
    public function handleEvent(SugarBean $bean, string $event, array $args): void
    {
        $this->publishEvent($bean, $event, $args);
    }

    /**
     * Publishes a module event to subscribers.
     *
     * Relationship events are not published for a record while it is being
     * deleted. An after_delete event will be published for the record when the
     * operation is complete and subscribers can use this event as a signal to
     * remove any relationships they have with the deleted record. While
     * relationship events are muted for the deleted record, those events are
     * published for related records because there may be subscribers who only
     * subscribe to the modules of the related records. This ensures that those
     * subscribers get the message.
     *
     * @param SugarBean $bean The impacted record.
     * @param string $event The event type.
     * @param array $args Additional arguments.
     *
     * @return void
     */
    public function publishEvent(SugarBean $bean, string $event, array $args): void
    {
        $container = Container::getInstance();
        $logger = $container->get(LoggerInterface::class);
        $recordIdentifier = '';

        // Don't let any exceptions bubble up.
        try {
            $config = $container->get(SugarConfig::class);
            $timedate = $container->get(TimeDate::class);
            $moduleName = $bean->getModuleName();
            $recordIdentifier = "{$moduleName}/{$bean->id}";
            $relationshipEvents = [
                'after_relationship_add',
                'after_relationship_delete',
                'after_relationship_update',
            ];

            if (!static::isModuleAllowed($moduleName)) {
                $logger->info("pubsub: skipped module event ({$event}) for {$recordIdentifier} [reason={$moduleName} events are not published]");
                return;
            }

            if (in_array($event, $relationshipEvents)) {
                $relatedModuleName = array_key_exists('related_module', $args) ? $args['related_module'] : '';

                if (!static::isModuleAllowed($relatedModuleName)) {
                    $logger->info("pubsub: skipped module event ({$event}) for {$recordIdentifier} [reason={$relatedModuleName} events are not published]");
                    return;
                }

                // Mute relationship events for a record while it is being
                // deleted.
                if (SugarBean::inOperation('delete') && $bean->deleted) {
                    $logger->info("pubsub: skipped module event ({$event}) for {$recordIdentifier} [reason=deleting record]");
                    return;
                }
            }

            // Construct the payload with the contents that are the same for
            // every subscriber.
            $payload = [
                'timestamp' => $timedate->asIso($timedate->getNow()),
                'site_url' => $config->get('site_url'),
                'data' => [
                    'module_name' => $moduleName,
                    'id' => $bean->id,
                    'change_type' => $event,
                    'arguments' => $args,
                ],
            ];

            // Publish events to push subscribers.
            $pushPublisher = $container->get(PushSubscriptionPublisher::class);
            $pushPublisher->publishEvent($payload);
        } catch (Exception $e) {
            $logger->alert("pubsub: publish module event ({$event}) for {$recordIdentifier}: {$e->getMessage()}: {$e->getTraceAsString()}");
        }
    }
}
