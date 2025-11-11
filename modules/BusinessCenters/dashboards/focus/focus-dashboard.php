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
    'name' => 'LBL_BUSINESS_CENTERS_FOCUS_DRAWER_DASHBOARD',
    'id' => 'e64eab26-13cb-11eb-9aa9-acde48001122',
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
                                'module' => 'BusinessCenters',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'BusinessCenters',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'BusinessCenters',
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
