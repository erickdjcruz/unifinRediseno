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

$viewdefs['Bugs']['base']['view']['activity-timeline'] = [
    'dashlets' => [
        [
            'label' => 'TPL_ACTIVITY_TIMELINE_DASHLET',
            'description' => 'LBL_ACTIVITY_TIMELINE_DASHLET_DESCRIPTION',
            'config' => ['module' => 'Bugs'],
            'preview' => ['module' => 'Bugs'],
            'filter' => [
                'view' => 'record',
                'module' => [
                    'Bugs',
                ],
            ],
        ],
    ],
];
