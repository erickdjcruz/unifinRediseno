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

$dictionary['calendar_ical_configs'] = [
    'table' => 'calendar_ical_configs',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
        ],
        'date_entered' => [
            'name' => 'date_entered',
            'type' => 'datetime',
        ],
        'calendar_configurations' => [
            'name' => 'calendar_configurations',
            'type' => 'text',
        ],
    ],
    'indices' => [
        [
            'name' => 'calendar_ical_configspk',
            'type' => 'primary',
            'fields' => [
                'id',
            ],
        ],
    ],
];
