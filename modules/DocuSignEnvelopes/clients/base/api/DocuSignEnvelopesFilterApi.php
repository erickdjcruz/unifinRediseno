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

class DocuSignEnvelopesFilterApi extends FilterApi
{
    public function registerApiRest()
    {
        return [
            'filterModuleAll' => [
                'reqType' => 'GET',
                'path' => ['DocuSignEnvelopes'],
                'pathVars' => ['module'],
                'method' => 'filterList',
                'jsonParams' => ['filter'],
                'shortHelp' => 'List of all records in this module',
                'longHelp' => 'include/api/help/module_filter_get_help.html',
                'exceptions' => [
                    // Thrown in filterList
                    'SugarApiExceptionInvalidParameter',
                    // Thrown in filterListSetup and parseArguments
                    'SugarApiExceptionNotAuthorized',
                ],
                'minVersion' => '11.16',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function filterListSetup(ServiceBase $api, array $args, $acl = 'list')
    {
        if (isset($args['layout'])) {
            $recordModule = $args['recordModule'] ?? '';

            [$args, $q, $options, $seed] = parent::filterListSetup($api, $args, $acl);

            if (isset($options['id_query'])) {
                $query = $options['id_query'];
            } else {
                $query = $q;
            }
            if (!empty($recordModule) && $recordModule !== 'Home') {
                $andWhere = $query->where()->queryAnd();
                $andWhere->equals('parent_type', $recordModule);
            }
            if (isset($args['record']) && !empty($args['record'])) {
                $andWhere = $query->where()->queryAnd();
                $andWhere->equals('parent_id', $args['record']);
            }
            if (isset($args['status'])) {
                $andWhere = $query->where()->queryAnd();
                $andWhere->equals('status', $args['status']);
            }

            $result = [$args, $q, $options, $seed];
        } else {
            $result = parent::filterListSetup($api, $args, $acl);
        }

        return $result;
    }
}
