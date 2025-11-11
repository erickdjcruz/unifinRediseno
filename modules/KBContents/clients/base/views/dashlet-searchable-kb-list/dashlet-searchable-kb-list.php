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
$viewdefs['KBContents']['base']['view']['dashlet-searchable-kb-list'] = [
    'dashlets' => [
        [
            'label' => 'LBL_DASHLET_KB_SEARCH_NAME',
            'description' => 'LBL_DASHLET_KB_SEARCH_DESCRIPTION',
            'config' => [
                'last_state' => [
                    'id' => 'dashlet-searchable-kb-list-kbcontents',
                ],
                'data_provider' => 'Categories',
                'config_provider' => 'KBContents',
                'root_name' => 'category_root',
                'extra_provider' => [
                    'module' => 'KBContents',
                    'field' => 'category_id',
                ],
            ],
            'preview' => [
                'data_provider' => 'Categories',
                'config_provider' => 'KBContents',
                'root_name' => 'category_root',
            ],
        ],
    ],
    'config' => [],
];
