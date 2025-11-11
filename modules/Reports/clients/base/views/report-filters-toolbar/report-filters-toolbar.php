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
$viewdefs['Reports']['base']['view']['report-filters-toolbar'] = [
    'panels' => [
        [
            'name' => 'header',
            'buttons' => [
                [
                    'type' => 'actiondropdown',
                    'no_default_action' => true,
                    'name' => 'action_menu',
                    'css_class' => 'btn btn-invisible',
                    'icon' => 'sicon sicon-kebab',
                    'buttons' => [
                        [
                            'type' => 'rowaction',
                            'event' => 'button:reset:filters:click',
                            'name' => 'reset',
                            'label' => 'LBL_RESET_FILTERS_TO_DEFAULT',
                            'acl_action' => 'reset',
                        ],
                        [
                            'type' => 'rowaction',
                            'event' => 'button:copy:filters:click',
                            'name' => 'copy',
                            'label' => 'LBL_COPY_FILTER_SUMMARY',
                            'acl_action' => 'copy',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
