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
/*
 * @deprecated as of 11.2.0 and will be removed in a future release
 * Please use View.Views.Portal.ActivityTimelineView instead
 */
$viewdefs['Notes']['portal']['view']['activity'] = [
    'buttons' => [
        0 =>
            [
                'name' => 'show_more_button',
                'type' => 'button',
                'label' => 'Show More',
                'class' => 'loading wide',
            ],
    ],
    'panels' => [
        0 =>
            [
                'label' => 'LBL_PANEL_DEFAULT',
                'fields' => [
                    0 =>
                        [
                            'name' => 'name',
                            'default' => true,
                            'enabled' => true,
                            'width' => 8,
                        ],
                    1 =>
                        [
                            'name' => 'description',
                            'default' => true,
                            'enabled' => true,
                            'width' => 13,
                        ],
                    2 =>
                        [
                            'name' => 'date_entered',
                            'default' => true,
                            'enabled' => true,
                            'width' => 13,
                        ],
                    3 =>
                        [
                            'name' => 'created_by_name',
                            'default' => true,
                            'enabled' => true,
                            'width' => 13,
                        ],
                    4 =>
                        [
                            'name' => 'filename',
                            'default' => true,
                            'enabled' => true,
                            'sorting' => true,
                            'width' => 35,
                        ],
                ],
            ],
    ],
];
