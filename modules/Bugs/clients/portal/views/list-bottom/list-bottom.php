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

$viewdefs['Bugs']['portal']['view']['list-bottom'] = [
    'listNav' => [
        0 =>
            [
                'name' => 'create_new',
                'type' => 'navelement',
                'icon' => 'sicon-plus',
                'label' => ' ',
                'route' => [
                    'action' => 'create',
                    'module' => 'Cases',
                ],
            ],
        1 =>
            [
                'name' => 'show_more_button_back',
                'type' => 'navelement',
                'icon' => 'sicon-chevron-left',
                'label' => ' ',
            ],
        2 =>
            [
                'name' => 'show_more_button_forward',
                'type' => 'navelement',
                'icon' => 'sicon-chevron-right',
                'label' => ' ',
            ],
    ],
];
