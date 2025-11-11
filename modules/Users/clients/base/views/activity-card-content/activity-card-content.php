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

$viewdefs['Users']['base']['view']['activity-card-content'] = [
    'sort_by' => 'date_entered',
    'sort_order' => 'asc',
    'panels' => [
        [
            'css_class' => 'panel-group flex',
            'fields' => [
                'user_name',
                'title',
                'department',
                'status',
                'phone_work',
                'email',
            ],
        ],
    ],
];
