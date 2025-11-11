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
    'name' => 'LBL_ESCALATIONS_FOCUS_DRAWER_DASHBOARD',
    'id' => '1e357fb6-055b-11ec-8095-acde48001122',
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
                                'module' => 'Escalations',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'Escalations',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'Escalations',
                            ],
                            'width' => 6,
                            'height' => 8,
                        ],
                        [
                            'view' => [
                                'module' => 'Escalations',
                                'type' => 'activity-timeline',
                                'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
                            ],
                            'context' => [
                                'module' => 'Escalations',
                            ],
                            'width' => 6,
                            'height' => 16,
                        ],
                    ],
                    // Row 2
                    [
                        [
                            'view' => [
                                'type' => 'commentlog-dashlet',
                                'label' => 'LBL_DASHLET_COMMENTLOG_NAME',
                            ],
                            'width' => 6,
                            'height' => 8,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
