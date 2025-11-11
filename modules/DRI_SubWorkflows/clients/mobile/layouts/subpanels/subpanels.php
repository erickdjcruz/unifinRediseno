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
$viewdefs['DRI_SubWorkflows']['mobile']['layout']['subpanels'] = [
    'components' => [
        [
            'layout' => 'subpanel',
            'label' => 'LBL_TASKS',
            'context' => [
                'link' => 'tasks',
            ],
            'linkable' => false,
            'creatable' => false,
            'unlinkable' => false,
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_CALLS',
            'context' => [
                'link' => 'calls',
            ],
            'linkable' => false,
            'creatable' => false,
            'unlinkable' => false,
        ],
        [
            'layout' => 'subpanel',
            'label' => 'LBL_MEETINGS',
            'context' => [
                'link' => 'meetings',
            ],
            'linkable' => false,
            'creatable' => false,
            'unlinkable' => false,
        ],
    ],
];
