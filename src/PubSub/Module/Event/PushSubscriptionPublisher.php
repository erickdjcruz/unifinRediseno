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
use GuzzleHttp\Psr7\Uri;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use PubSub_ModuleEvent_PushSub;
use Sugarcrm\Sugarcrm\PubSub\Client\PushClientInterface;
use Sugarcrm\Sugarcrm\PubSub\PublisherInterface;

final class PushSubscriptionPublisher implements PublisherInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Use this client to send events to subscribers.
     */
    private PushClientInterface $client;

    /**
     * Sends events to subscribers.
     *
     * @param PushClientInterface $client Use this client to send events.
     */
    public function __construct(PushClientInterface $client)
    {
        $this->client = $client;
        $this->setLogger(new NullLogger());
    }

    /**
     * Reports if the webhook URL is allowed. Only webhooks owned and operated
     * by SugarCRM are allowed.
     *
     * @param string $url The webhook URL.
     *
     * @return bool
     */
    public static function isWebhookAllowed(string $url): bool
    {
        $allowedDomains = [
            '.sugarcrm.com',
            '.sugar.build',
        ];

        try {
            $uri = new URI($url);
            $host = $uri->getHost();
            $host = sugarStrToLower($host);

            foreach ($allowedDomains as $domain) {
                if (str_end($host, $domain)) {
                    return true;
                }
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * Sends a module event to each subscriber's webhook.
     *
     * @param array $event The payload to publish.
     *
     * @return void
     */
    public function publishEvent(array $event): void
    {
        $eventName = '';
        $recordIdentifier = '';

        // Don't let any exceptions bubble up.
        try {
            $eventName = $event['data']['change_type'];
            $moduleName = $event['data']['module_name'];
            $recordIdentifier = "{$moduleName}/{$event['data']['id']}";

            // Load the subscriptions.
            $subs = PubSub_ModuleEvent_PushSub::findActiveSubscriptionsByModule($moduleName);

            // Send the event to each subscriber.
            foreach ($subs as $id => $sub) {
                if (!static::isWebhookAllowed($sub->webhook_url)) {
                    $this->logger->warning("pubsub: push: skipped module event ({$eventName}) for {$recordIdentifier} to {$sub->webhook_url} [reason=webhook is not allowed]");
                    continue;
                }

                try {
                    // Add the subscription ID and trusted token to the payload.
                    $payload = array_merge($event, [
                        'subscription_id' => $sub->id,
                        'token' => $sub->token,
                    ]);

                    // Send the event (as a list of one) to the webhook.
                    $this->client->sendEvents($sub->webhook_url, [$payload]);
                } catch (Exception $e) {
                    $this->logger->alert("pubsub: push: publish module event ({$eventName}) for {$recordIdentifier} to {$sub->webhook_url}: {$e->getMessage()}: {$e->getTraceAsString()}");
                }
            }
        } catch (Exception $e) {
            $this->logger->alert("pubsub: push: publish module event ({$eventName}) for {$recordIdentifier} to push subscribers: {$e->getMessage()}: {$e->getTraceAsString()}");
        }
    }
}
