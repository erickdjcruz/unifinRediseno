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
                                'label' => 'TPL_DASHLET_MY_MODULE',
                                'display_columns' => [
                                    'escalation_number',
                                    'name',
                                    'status',
                                    'parent_type',
                                    'parent_name',
                                    'reason',
                                    'date_modified',

                                ],
                            ],
                            'context' => [
                                'module' => 'Escalations',
                            ],
                            'width' => 12,
                        ],
                    ],
                    [
                        [
                            'view' => [
                                'type' => 'dashablelist',
                                'label' => 'LBL_RECENTLY_CREATED_ESCALATIONS_DASHLET',
                                'display_columns' => [
                                    'escalation_number',
                                    'name',
                                    'status',
                                    'parent_type',
                                    'parent_name',
                                    'reason',
                                    'date_modified',

                                ],
                                'filter_id' => 'recently_created',
                            ],
                            'context' => [
                                'module' => 'Escalations',
                            ],
                            'width' => 12,
                        ],
                    ],
                ],
                'width' => 12,
            ],
        ],
    ],
    'name' => 'LBL_ESCALATIONS_LIST_DASHBOARD',
];
