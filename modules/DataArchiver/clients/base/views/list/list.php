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

$viewdefs['DataArchiver']['base']['view']['list'] = [
    'panels' => [
        [
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'link' => true,
                    'label' => 'LBL_LIST_ARCHIVE_NAME',
                    'enabled' => true,
                    'default' => true,
                    'width' => 'xlarge',
                ],
                [
                    'name' => 'filter_module_name',
                    'link' => false,
                    'label' => 'LBL_MODULE_FIELD',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'process_type',
                    'link' => false,
                    'label' => 'LBL_PROCESS_TYPE_FIELD',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'date_modified',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'date_entered',
                    'type' => 'datetime',
                    'label' => 'LBL_DATE_ENTERED',
                    'enabled' => true,
                    'default' => false,
                    'readonly' => true,
                ],
                [
                    'name' => 'filter_def',
                    'label' => 'LBL_FILTER_DEF_FIELD',
                    'enabled' => true,
                    'default' => false,
                ],
            ],
        ],
    ],
];
