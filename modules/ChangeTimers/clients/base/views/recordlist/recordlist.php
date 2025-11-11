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
$viewdefs['ChangeTimers']['base']['view']['recordlist'] = [
    'sticky_resizable_columns' => true,
    'selection' => [
        'type' => 'multi',
        'actions' => [
            [
                'name' => 'export_button',
                'type' => 'button',
                'label' => 'LBL_EXPORT',
                'acl_action' => 'export',
                'primary' => true,
                'events' => [
                    'click' => 'list:massexport:fire',
                ],
            ],
        ],
    ],
    'rowactions' => [
        'actions' => [
        ],
    ],
];
