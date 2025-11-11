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
$viewdefs['Emails']['base']['view']['search-list'] = [
    'panels' => [
        [
            'name' => 'primary',
            'fields' => [
                [
                    'name' => 'picture',
                    'type' => 'avatar',
                    'size' => 'medium',
                    'readonly' => true,
                    'css_class' => 'pull-left',
                ],
                [
                    'name' => 'name',
                    'type' => 'name',
                    'link' => true,
                    'label' => 'LBL_SUBJECT',
                ],
            ],
        ],
        [
            'name' => 'secondary',
            'fields' => [
                [
                    'name' => 'from_addr_name',
                    'label' => 'LBL_FROM',
                ],
                [
                    'name' => 'date_sent',
                    'label' => 'LBL_DATE',
                ],
            ],
        ],
    ],
];
