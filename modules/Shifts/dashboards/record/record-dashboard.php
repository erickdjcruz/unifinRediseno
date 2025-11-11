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
    'metadata' => [
        'dashlets' => [
            [
                'view' => [
                    'type' => 'dashablelist',
                    'label' => 'LBL_MY_SHIFTS',
                    'display_columns' => [
                        'name',
                        'date_start',
                        'date_end',
                        'timezone',
                        'date_modified',
                        'assigned_user_name',
                        'team_name',
                        'modified_by_name',
                        'created_by_name',
                        'date_entered',
                    ],
                    'filter_id' => 'assigned_to_me',
                ],
                'context' => [
                    'module' => 'Shifts',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 0,
            ],
            [
                'view' => [
                    'type' => 'commentlog-dashlet',
                    'label' => 'LBL_DASHLET_COMMENTLOG_NAME',
                ],
                'width' => 12,
                'x' => 0,
                'y' => 6,
            ],
            //FIENDLE SUGARCRM flav=ent ONLY
        ],
    ],
    'name' => 'LBL_SHIFT_RECORD_DASHBOARD',
];
