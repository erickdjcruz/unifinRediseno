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

$viewdefs['base']['view']['cloud-drive'] = [
    'dashlets' => [
        [
            'label' => 'LBL_DASHLET_CLOUD_DRIVE_NAME',
            'description' => 'LBL_DASHLET_CLOUD_DRIVE_DESCRIPTION',
            'config' => [],
            'preview' => [],
            'filter' => [
                'blacklist' => [
                    'view' => [
                        'portaltheme-config',
                    ],
                ],
            ],
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_body',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'drive_type',
                    'label' => 'LBL_CLOUD_PROVIDER',
                    'type' => 'enum',
                    'options' => 'drive_types',
                ],
            ],
        ],
    ],
];
