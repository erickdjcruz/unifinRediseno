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
$viewdefs['Shifts']['base']['layout']['subpanels'] = [
    'components' => [
        [
            'layout' => 'subpanel',
            'label' => 'LBL_USERS_SUBPANEL_TITLE',
            'override_subpanel_list_view' => 'subpanel-for-shifts',
            'context' => [
                'link' => 'shifts_users',
            ],
        ],
    ],
];
