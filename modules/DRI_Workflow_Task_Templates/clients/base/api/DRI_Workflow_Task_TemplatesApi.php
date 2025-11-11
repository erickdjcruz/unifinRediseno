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

class DRI_Workflow_Task_TemplatesApi extends SugarApi
{
    /**
     * @inheritdoc
     */
    public function registerApiRest()
    {
        return [
            'getTemplateAvailableModules' => [
                'reqType' => 'GET',
                'path' => ['DRI_Workflow_Task_Templates', 'available-modules'],
                'pathVars' => ['module'],
                'method' => 'getTemplateAvailableModules',
                'shortHelp' => 'Get modules for which template is enabled   ',
                'longHelp' => '/include/api/help/customer_journeyTask_TemplatesgetTemplateModules.html',
                'minVersion' => '11.19',
            ],
        ];
    }

    /**
     * Return the Available Modules In Activity Template against the selected
     * template
     *
     * @param ServiceBase $api
     * @param array $args
     * @return array
     *
     * @throws SugarApiExceptionMissingParameter
     * @throws SugarApiExceptionNotAuthorized
     * @throws SugarApiExceptionNotFound
     * @throws Exception
     */
    public function getTemplateAvailableModules(ServiceBase $api, array $args)
    {
        // Ensure this end-point must be accessible to Sugar Automate Users
        ConfigurationManager::ensureAutomateUser();

        $this->requireArgs($args, ['module', 'template_id']);

        if (empty($args['template_id'])) {
            return [];
        }

        $query = new SugarQuery();
        $query->from(BeanFactory::newBean('DRI_Workflow_Templates'), ['team_security' => false]);
        $query->select(['available_modules']);

        $query->where()->equals('id', $args['template_id']);
        $result = $query->getOne();
        return (!empty($result)) ? unencodeMultienum($result) : [];
    }
}
