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

$subpanel_layout = [
    'list_fields' => [
        'name' => [
            'vname' => 'LBL_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '30%',
            'sortable' => true,
        ],
        'shift_exception_type' => [
            'vname' => 'LBL_TYPE',
            'width' => '10%',
            'sortable' => true,
        ],
        'start_date' => [
            'vname' => 'LBL_START_DATE',
            'width' => '10%',
            'sortable' => true,
        ],
        'end_date' => [
            'vname' => 'LBL_END_DATE',
            'width' => '10%',
            'sortable' => true,
        ],
        'timezone' => [
            'vname' => 'LBL_TIMEZONE',
            'width' => '20%',
            'sortable' => true,
        ],
        'all_day' => [
            'vname' => 'LBL_ALL_DAY',
            'width' => '10%',
            'sortable' => true,
        ],
        'enabled' => [
            'vname' => 'LBL_ENABLED',
            'width' => '10%',
            'sortable' => true,
        ],
    ],
];
