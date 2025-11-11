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
    'metadata' => [
        'components' => [
            [
                'rows' => [
                    [
                        [
                            'view' => [
                                'type' => 'dashablelist',
                                'label' => 'LBL_GROUP_USERS',
                                'display_columns' => [
                                    'name',
                                    'user_name',
                                    'status',
                                ],
                                'filter_id' => 'group_users',
                            ],
                            'context' => [
                                'module' => 'Users',
                            ],
                            'width' => 12,
                        ],
                        [
                            'view' => [
                                'type' => 'dashablelist',
                                'label' => 'LBL_ACTIVE_USERS',
                                'display_columns' => [
                                    'name',
                                    'user_name',
                                    'title',
                                ],
                                'filter_id' => 'active_users',
                            ],
                            'context' => [
                                'module' => 'Users',
                            ],
                            'width' => 12,
                        ],
                    ],
                ],
                'width' => 12,
            ],
        ],
    ],
    'name' => 'LBL_USERS_LIST_DASHBOARD',
];
