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

use Sugarcrm\Sugarcrm\CustomerJourney\Bean\WebHook\Request;
use Sugarcrm\Sugarcrm\CustomerJourney\ConfigurationManager;

class CJ_WebHooksApi extends SugarApi
{
    /**
     * @inheritdoc
     */
    public function registerApiRest()
    {
        return [
            'sendRequest' => [
                'reqType' => 'GET',
                'path' => ['CJ_WebHooks', '?', 'send-request'],
                'pathVars' => ['module', 'record'],
                'method' => 'sendRequest',
                'shortHelp' => 'Make curl request according to webhook bean attributes using custom Request class',
                'longHelp' => '/include/api/help/customer_journeyCJ_WebHookssendRequest.html',
                'noEtag' => true,
                'minVersion' => '11.19',
            ],
        ];
    }

    /**
     * Call send method of Custom Request class which makes a curl request
     * according to webhook bean attributes
     *
     * @param ServiceBase $api
     * @param array $args
     * @return string|void
     * @throws SugarApiExceptionError
     * @throws SugarApiException
     */
    public function sendRequest(ServiceBase $api, array $args)
    {
        // Ensure this end-point must be accessible to Sugar Automate Users
        ConfigurationManager::ensureAutomateUser();

        $this->requireArgs($args, ['module', 'record']);

        $webhookBean = BeanFactory::retrieveBean($args['module'], $args['record']);

        try {
            if (!empty($webhookBean->id)) {
                $request = new Request($webhookBean);
                $response = $request->send([]);
                return $response;
            }
        } catch (\SugarApiExceptionError $e) {
            return $e->getMessage();
        } catch (\SugarApiException $e) {
            return "{$e->getHttpCode()}: {$e->getMessage()}";
        }
    }
}
