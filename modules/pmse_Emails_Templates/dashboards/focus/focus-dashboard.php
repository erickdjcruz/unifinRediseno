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
    'name' => 'LBL_PMSE_EMAILS_TEMPLATES_FOCUS_DRAWER_DASHBOARD',
    'id' => 'e64f3f1e-13cb-11eb-bc68-acde48001122',
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
                                'module' => 'pmse_Emails_Templates',
                                'tabs' => [
                                    [
                                        'active' => true,
                                        'label' => 'LBL_MODULE_NAME_SINGULAR',
                                        'link' => '',
                                        'module' => 'pmse_Emails_Templates',
                                    ],
                                ],
                            ],
                            'context' => [
                                'module' => 'pmse_Emails_Templates',
                            ],
                            'width' => 6,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
