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
$viewdefs['base']['view']['subpanel-list-create'] = [
    'rowactions' => [
        'actions' => [
            [
                'type' => 'rowaction',
                'css_class' => 'btn',
                'icon' => 'sicon-plus',
                'event' => 'list:addrow:fire',
            ],
            [
                'type' => 'rowaction',
                'css_class' => 'btn',
                'icon' => 'sicon-minus',
                'event' => 'list:deleterow:fire',
            ],
        ],
    ],
    'last_state' => [
        'id' => 'subpanel-list-create',
    ],
];
