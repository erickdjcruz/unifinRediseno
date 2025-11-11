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
$viewdefs['Calendar']['base']['view']['add-calendars'] = [
    'template' => 'flex-list',
    'selection' => [
        'type' => 'single',
        'actions' => [],
        'disable_select_all_alert' => true,
        'label' => 'LBL_LINK_SELECT',
    ],
    'panels' => [
        [
            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'fullname',
                    'label' => 'LBL_LIST_NAME',
                    'enabled' => true,
                    'default' => true,
                    'link' => false,
                ],
                [
                    'name' => '_module',
                    'label' => 'LBL_CALEDNAR_ADD_LIST_MODULE',
                    'type' => 'module',
                    'sortable' => false,
                    'enabled' => true,
                    'default' => true,
                    'link' => false,
                ],
            ],
        ],
    ],
    'rowactions' => [],
];
