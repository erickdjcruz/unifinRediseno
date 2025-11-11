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

$viewdefs['DRI_Workflows']['base']['layout']['configure-modules'] = [
    'components' => [
        [
            'layout' => [
                'components' => [
                    [
                        'view' => 'configure-modules-headerpane',
                    ],
                    [
                        'view' => 'configure-modules-content',
                    ],
                ],
                'type' => 'simple',
                'name' => 'main-pane',
                'span' => 12,
            ],
        ],
    ],
    'type' => 'simple',
    'name' => 'base',
    'span' => 12,
];
