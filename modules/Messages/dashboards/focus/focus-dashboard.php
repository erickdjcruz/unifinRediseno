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
    'name' => 'LBL_MESSAGES_FOCUS_DRAWER_DASHBOARD',
    'id' => 'e64ef568-13cb-11eb-a9a2-acde48001123',
    'metadata' => [
        'components' => [
            [
                'width' => 12,
                'rows' => [
                    [
                        [
                            'view' => [
                                'type' => 'dashablerecord',
                                'module' => 'Messages',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'Messages',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'Messages',
                            ],
                            'width' => 6,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
