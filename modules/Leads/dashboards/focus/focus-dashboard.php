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
    'name' => 'LBL_LEADS_FOCUS_DRAWER_DASHBOARD',
    'id' => 'e64f0a80-13cb-11eb-bb6e-acde48001122',
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
                                'module' => 'Leads',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'Leads',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'Leads',
                            ],
                            'width' => 6,
                            'height' => 8,
                        ],
                        [
                            'view' => [
                                'module' => 'Leads',
                                'type' => 'activity-timeline',
                                'label' => 'LBL_ACTIVITY_TIMELINE_DASHLET',
                            ],
                            'context' => [
                                'module' => 'Leads',
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
