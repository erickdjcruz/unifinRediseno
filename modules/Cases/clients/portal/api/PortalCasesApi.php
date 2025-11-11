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

/**
 * Class PortalCasesApi
 *
 */
class PortalCasesApi extends ModulePortalApi
{
    /**
     * {@inheritDoc}
     */
    public function registerApiRest(): array
    {
        return [
            'request_close' => [
                'reqType' => 'PUT',
                'path' => ['Cases', '?', 'request_close'],
                'pathVars' => ['module', 'record', ''],
                'method' => 'requestCloseCase',
                'shortHelp' => 'This method sets the the case as "requested for closure"',
                'longHelp' => 'include/api/help/cases_portal_request_close_help.html',
            ],
        ];
    }

    /**
     * Sets the request_close status to true and records the date and time the request was made
     *
     * @param ServiceBase $api
     * @param array $args
     * @return bool
     * @throws SugarApiExceptionNotFound
     */
    public function requestCloseCase(ServiceBase $api, array $args)
    {
        global $timedate;
        $bean = $this->loadBean($api, $args);
        $bean->request_close = true;
        $bean->request_close_date = $timedate->nowDb();
        $bean->save();

        return $this->getLoadedAndFormattedBean($api, $args);
    }
}
