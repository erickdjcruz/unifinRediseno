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

class NotesFilterApi extends FilterApi
{
    /**
     * @inheritdoc
     */
    public function registerApiRest()
    {
        $api = parent::registerApiRest();
        $api['filterModuleAll']['path'] = ['Notes'];
        $api['filterModuleAllCount']['path'] = ['Notes', 'count'];
        $api['filterModulePostCount']['path'] = ['Notes', 'filter', 'count'];
        $api['filterModuleCount']['path'] = ['Notes', 'filter', 'count'];

        return $api;
    }

    /**
     * @inheritdoc
     */
    public function filterListSetup(ServiceBase $api, array $args, $acl = 'list')
    {
        $args['filter'][0] = array_merge($args['filter'][0] ?? [], [
            // not an attachment or an attachment for KB
            '$or' => [
                [
                    'attachment_flag' => [
                        '$equals' => 0,
                    ],
                ],
                [
                    'parent_type' => [
                        '$equals' => 'KBContents',
                    ],
                ],
            ],
        ]);

        return parent::filterListSetup($api, $args, $acl);
    }
}
