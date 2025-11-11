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

$module_name = 'Project';
$viewdefs[$module_name]['base']['menu']['quickcreate'] = [
    'layout' => 'create',
    'visible' => false,
    'related' => [
        [
            'module' => 'Accounts',
            'link' => 'project',
        ],
        [
            'module' => 'Contacts',
            'link' => 'project',
        ],
        [
            'module' => 'Opportunities',
            'link' => 'project',
        ],
        [
            'module' => 'Tasks',
            'link' => 'project',
        ],
        [
            'module' => 'Meetings',
            'link' => 'project',
        ],
        [
            'module' => 'Calls',
            'link' => 'project',
        ],
        [
            'module' => 'Cases',
            'link' => 'project',
        ],
    ],

];
