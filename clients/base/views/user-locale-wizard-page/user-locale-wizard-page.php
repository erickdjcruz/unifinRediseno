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

$viewdefs['base']['view']['user-locale-wizard-page'] = [
    'title' => 'LBL_WIZ_USER_LOCALE_TITLE',
    'message' => 'LBL_SETUP_USER_LOCALE_INFO',
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'timepref',
                    'type' => 'enum',
                    'label' => 'LBL_WIZ_TIMEFORMAT',
                    'required' => true,
                ],
                [
                    'name' => 'datepref',
                    'type' => 'enum',
                    'label' => 'LBL_WIZ_DATE_FORMAT',
                    'required' => true,
                ],
                [
                    'name' => 'default_locale_name_format',
                    'type' => 'enum',
                    'label' => 'LBL_WIZ_NAME_FORMAT',
                    'required' => true,
                    //Define something other than comma since that is used in name format values
                    'separator' => '|',
                ],
            ],
        ],
    ],
];
