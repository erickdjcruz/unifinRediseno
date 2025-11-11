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

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriNormalizer;
use Sugarcrm\Sugarcrm\DependencyInjection\Container;
use Sugarcrm\Sugarcrm\PubSub\Module\Event\Publisher;
use Sugarcrm\Sugarcrm\PubSub\Module\Event\PushSubscriptionPublisher;

final class PubSub_ModuleEvent_PushSubsApiHelper extends SugarBeanApiHelper
{
    /**
     * @inheritdoc
     *
     * Adds the application's site_url. Clients can use this to verify the
     * source of a notification, in combination with the subscription's ID and
     * the secure token. Each notification includes all three identifiers. The
     * token is always removed from the response. It should only be known to the
     * subscriber.
     */
    public function formatForApi(SugarBean $bean, array $fieldList = [], array $options = [])
    {
        $container = Container::getInstance();
        $config = $container->get(SugarConfig::class);

        // Never let the token be seen. The name and description fields serve no
        // purpose.
        $allowedFields = [
            'created_by',
            'created_by_name',
            'date_entered',
            'date_modified',
            'expiration_date',
            'id',
            'modified_by_name',
            'modified_user_id',
            'target_module',
            'webhook_url',
        ];

        if (empty($fieldList)) {
            $fieldList = $allowedFields;
        } else {
            $allowedFieldList = [];

            foreach ($fieldList as $field) {
                if (in_array($field, $allowedFields)) {
                    $allowedFieldList[] = $field;
                }
            }

            $fieldList = $allowedFieldList;
        }

        $data = parent::formatForApi($bean, $fieldList, $options);

        // Add the site_url.
        $data['site_url'] = $config->get('site_url');

        return $data;
    }

    /**
     * @inheritdoc
     *
     * Sets the expriation date to 7 days from now. Normalizes the webhook url.
     *
     * @throws SugarApiExceptionNotAuthorized
     */
    public function populateFromApi(SugarBean $bean, array $submittedData, array $options = [])
    {
        // Set the expiration date.
        $container = Container::getInstance();
        $timedate = $container->get(TimeDate::class);
        $now = $timedate->getNow();
        $expiresIn7Days = $now->modify('+7 days');
        $submittedData['expiration_date'] = $timedate->asIso($expiresIn7Days);

        // Normalize the webhook url for comparing webhooks when finding
        // duplicates.
        if (array_key_exists('webhook_url', $submittedData)) {
            try {
                $uri = new URI($submittedData['webhook_url']);
                $uri = UriNormalizer::normalize($uri);
                $submittedData['webhook_url'] = (string)$uri;
            } catch (Exception $e) {
                throw new SugarApiExceptionInvalidParameter('ERR_PUBSUB_MODULEEVENT_PUSHSUBS_WEBHOOK_URL_INVALID');
            }
        }

        $result = parent::populateFromApi($bean, $submittedData, $options);

        if (!Publisher::isModuleAllowed($bean->target_module)) {
            throw new SugarApiExceptionNotAuthorized(
                'ERR_PUBSUB_MODULEEVENT_PUSHSUBS_TARGET_MODULE_NOT_ALLOWED',
                $bean->target_module
            );
        }

        if (!PushSubscriptionPublisher::isWebhookAllowed($bean->webhook_url)) {
            throw new SugarApiExceptionNotAuthorized('ERR_PUBSUB_MODULEEVENT_PUSHSUBS_WEBHOOK_URL_NOT_ALLOWED');
        }

        return $result;
    }
}
