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

$viewdefs['KBContents']['base']['view']['module-menu'] = [
    'config' => [
        'data_provider' => 'Categories',
        'config_provider' => 'KBContents',
        'root_name' => 'category_root',
    ],
    'label' => 'LNK_LIST_KBCATEGORIES',
    'filterDef' => [
        [
            'active_rev' => [
                '$equals' => '1',
            ],
        ],
    ],
];
