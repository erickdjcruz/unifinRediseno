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

namespace Sugarcrm\Sugarcrm\SugarConnect\Event;

use Sugarcrm\Sugarcrm\SugarConnect\Publisher;
use Sugarcrm\Sugarcrm\SugarConnect\Configuration\ConfigurationInterface;

/**
 * @deprecated Will be removed in the next release.
 */
final class Event
{
    /**
     * Creates an Event publisher for the specific event.
     *
     * The Nop publisher is used if an event publisher strategy does not exist
     * for the specified event.
     *
     * @param ConfigurationInterface $config The SugarConnect configuration.
     * @param string $event The type of event.
     *
     * @return Publisher
     * @throws \Exception Throws if the instance does not implement Publisher.
     *
     */
    public static function getInstance(ConfigurationInterface $config, string $event): Publisher
    {
        // Event names are transformed from snake_case to PascalCase to match
        // PHP class names.
        $classname = str_replace('_', '', ucwords($event, '_'));

        if (!$classname) {
            $classname = 'Nop';
        }

        $fqcn = __NAMESPACE__ . '\\' . $classname;
        $publisher = class_exists($fqcn) ? new $fqcn() : new Nop();

        if (!($publisher instanceof Publisher)) {
            throw new \Exception("{$fqcn} does not implement Publisher");
        }

        $publisher->setConfiguration($config);

        return $publisher;
    }

    /**
     * Sends the event to the Sugar Connect webhook.
     *
     * @param ConfigurationInterface $config The SugarConnect configuration.
     * @param array $event The final event or message to
     *                                       publish.
     *
     * @return void
     */
    public static function publish(ConfigurationInterface $config, array $event): void
    {
        // Every notification declares where it came from.
        $event['source'] = \SugarConfig::getInstance()->get('site_url');

        $client = $config->getClient();
        $client->send([$event]);
    }

    /**
     * Returns the names of all fields on the bean, excluding link fields.
     *
     * @param \SugarBean $bean The bean that was changed.
     *
     * @return array
     */
    public static function getFields(\SugarBean $bean): array
    {
        return array_keys(
            array_filter(
                $bean->getFieldDefinitions(),
                function (array $def): bool {
                    return !isset($def['type']) || $def['type'] !== 'link';
                }
            )
        );
    }
}
