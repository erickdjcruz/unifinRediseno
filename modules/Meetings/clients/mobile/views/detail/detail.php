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
/*********************************************************************************
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$viewdefs['Meetings']['mobile']['view']['detail'] = [
    'templateMeta' => [
        'maxColumns' => '1',
        'widths' => [
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => 'name',
                    'displayParams' => [
                        'required' => true,
                        'wireless_edit_only' => true,]],
                'date_start',
                'status',
                [
                    'name' => 'duration',
                    'type' => 'fieldset',
                    'orientation' => 'horizontal',
                    'related_fields' => ['duration_hours', 'duration_minutes'],
                    'label' => 'LBL_DURATION',
                    'fields' => [
                        [
                            'name' => 'duration_hours',
                        ],
                        [
                            'type' => 'label',
                            'default' => 'LBL_HOURS_ABBREV',
                            'css_class' => 'label_duration_hours hide',
                        ],
                        [
                            'name' => 'duration_minutes',
                        ],
                        [
                            'type' => 'label',
                            'default' => 'LBL_MINSS_ABBREV',
                            'css_class' => 'label_duration_minutes hide',

                        ],
                    ],
                ],
                'description',
                'tag',
                'parent_name',
                'assigned_user_name',
                'team_name',
            ],
        ],
    ],
];
