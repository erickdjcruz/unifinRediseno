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


use Sugarcrm\Sugarcrm\Portal\Factory as PortalFactory;

class CasesFilterApi extends FilterApi
{
    public function registerApiRest()
    {
        return [
            'getContactCases' => [
                'reqType' => 'GET',
                'path' => ['Contact', '?', 'Cases'],
                'pathVars' => ['', 'contact_id', 'module'],
                'method' => 'getContactCases',
                'shortHelp' => 'Get cases accessible to a contact with portal visibility',
                'longHelp' => 'include/api/help/get_contact_cases.html',
                'exceptions' => [
                    'SugarApiExceptionInvalidParameter',
                    'SugarApiExceptionError',
                    'SugarApiExceptionNotAuthorized',
                ],
            ],
        ];
    }

    /**
     * Get cases accessible to a contact with portal visibility.
     *
     * @param ServiceBase $api The Api Class
     * @param array $args Service Call Arguments
     * @return mixed
     * @throws SugarApiExceptionError If retrieving a predefined filter failed.
     * @throws SugarApiExceptionInvalidParameter If any arguments are invalid.
     * @throws SugarApiExceptionNotAuthorized If we lack ACL access.
     */
    public function getContactCases(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, ['module', 'contact_id']);
        $_SESSION['type'] = 'support_portal';
        PortalFactory::getInstance('Session')->setContactId($args['contact_id']);
        $visibility = SugarBean::getDefaultVisibility();
        $visibility['SupportPortalVisibility'] = true;
        SugarBean::setDefaultVisibility($visibility);
        return $this->filterList($api, $args);
    }
}
