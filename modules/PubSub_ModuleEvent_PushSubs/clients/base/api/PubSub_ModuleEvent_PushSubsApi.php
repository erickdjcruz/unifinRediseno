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

final class PubSub_ModuleEvent_PushSubsApi extends ModuleApi
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        // The expiration date is updated automatically.
        $this->disabledUpdateFields[] = 'expiration_date';

        // These fields can't be updated through the API because they form a
        // unique composite key and are considered immutable.
        $this->disabledUpdateFields[] = 'target_module';
        $this->disabledUpdateFields[] = 'webhook_url';

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function registerApiRest()
    {
        return [
            'create' => [
                'reqType' => 'POST',
                'path' => ['PubSub_ModuleEvent_PushSubs'],
                'pathVars' => ['module'],
                'method' => 'createRecord',
                'minVersion' => '11.20',
            ],
            'retrieve' => [
                'reqType' => 'GET',
                'path' => ['PubSub_ModuleEvent_PushSubs', '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'retrieveRecord',
                'minVersion' => '11.20',
            ],
            'update' => [
                'reqType' => 'PUT',
                'path' => ['PubSub_ModuleEvent_PushSubs', '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'updateRecord',
                'minVersion' => '11.20',
            ],
            'delete' => [
                'reqType' => 'DELETE',
                'path' => ['PubSub_ModuleEvent_PushSubs', '?'],
                'pathVars' => ['module', 'record'],
                'method' => 'deleteRecord',
                'minVersion' => '11.20',
            ],
        ];
    }

    /**
     * Performs an upsert. In the event that a subscription is found with the
     * same target_module and webhook_url, that subscription is updated.
     *
     * @param ServiceBase $api
     * @param array $args API arguments
     *
     * @return array Formatted representation of the bean.
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     */
    public function createRecord(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'target_module', 'token', 'webhook_url']);

        // Pivot to an update if this would create a duplicate.
        $bean = BeanFactory::newBean($args['module']);
        $this->populateBean($bean, $api, $args);
        $duplicates = $bean->findDuplicates();

        if (empty($duplicates['records'])) {
            // Proceed with creating a new subscription.
            return parent::createRecord($api, $args);
        } else {
            // Update the first subscription.
            $beans = $duplicates['records'];
            $bean = array_shift($beans);
            $args['record'] = $bean->id;

            // Delete all other subscriptions just to be safe.
            $deleted = $bean->deleteDuplicates();

            return $this->updateRecord($api, $args);
        }
    }
}
