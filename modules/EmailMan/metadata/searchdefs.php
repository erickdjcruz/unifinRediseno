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
$searchdefs['EmailMan'] = [
    'templateMeta' => [
        'maxColumns' => '3', 'maxColumnsBasic' => '4',
        'widths' => ['label' => '10', 'field' => '30'],
    ],
    'layout' => [
        'basic_search' => [
            ['name' => 'campaign_name', 'label' => 'LBL_LIST_CAMPAIGN',],
            ['name' => 'current_user_only', 'label' => 'LBL_CURRENT_USER_FILTER', 'type' => 'bool'],
        ],
        'advanced_search' => [
            ['name' => 'campaign_name', 'label' => 'LBL_LIST_CAMPAIGN',],
            ['name' => 'to_name', 'label' => 'LBL_LIST_RECIPIENT_NAME'],
            ['name' => 'to_email', 'label' => 'LBL_LIST_RECIPIENT_EMAIL'],
            ['name' => 'current_user_only', 'label' => 'LBL_CURRENT_USER_FILTER', 'type' => 'bool'],
        ],
    ],
];
