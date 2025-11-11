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

return [
    'name' => 'LBL_MEETINGS_FOCUS_DRAWER_DASHBOARD',
    'id' => 'e64f13ea-13cb-11eb-a924-acde48001122',
    'metadata' => [
        'components' => [
            [
                'width' => 12,
                'rows' => [
                    // Row 1
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'module' => 'Meetings',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'Meetings',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'Meetings',
                            ],
                            'width' => 6,
                            'height' => 16,
                        ],
                        [
                            'view' => [
                                'type' => 'dashablelist',
                                'label' => 'LBL_MY_SCHEDULED_MEETINGS',
                                'display_columns' => [
                                    'date_start',
                                    'name',
                                    'parent_name',
                                ],
                                'filter_id' => 'my_scheduled_meetings',
                            ],
                            'context' => [
                                'module' => 'Meetings',
                            ],
                            'width' => 6,
                            'height' => 16,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
