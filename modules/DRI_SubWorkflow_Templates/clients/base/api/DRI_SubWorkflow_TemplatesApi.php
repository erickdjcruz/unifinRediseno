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

use Sugarcrm\Sugarcrm\CustomerJourney\ConfigurationManager;

class DRI_SubWorkflow_TemplatesApi extends SugarApi
{
    /**
     * @inheritdoc
     */
    public function registerApiRest()
    {
        return [
            'getLastStage' => [
                'reqType' => 'GET',
                'path' => ['DRI_SubWorkflow_Templates', '?', 'last-task'],
                'pathVars' => ['module', 'record'],
                'method' => 'getLastTask',
                'shortHelp' => 'Get the last task of the stage template',
                'longHelp' => '/include/api/help/customer_journeyDRI_SubWorkflow_TemplatesgetLastTask.html',
                'noEtag' => true,
                'minVersion' => '11.19',
            ],
        ];
    }

    /**
     * Get the last task of the stage template
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     */
    public function getLastTask(ServiceBase $api, array $args)
    {
        // Ensure this end-point must be accessible to Sugar Automate Users
        ConfigurationManager::ensureAutomateUser();

        $this->requireArgs($args, ['module', 'record']);

        /** @var DRI_SubWorkflow_Template $bean */
        $bean = $this->loadBean($api, $args);

        return $this->formatBean($api, $args, $bean->getLastTask());
    }
}
