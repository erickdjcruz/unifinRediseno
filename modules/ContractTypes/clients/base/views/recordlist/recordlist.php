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

$viewdefs['ContractTypes']['base']['view']['recordlist'] = [
    'selection' => [
        'type' => 'multi',
        'actions' => [
            [
                'name' => 'merge_button',
                'type' => 'button',
                'label' => 'LBL_MERGE',
                'primary' => true,
                'events' => [
                    'click' => 'list:mergeduplicates:fire',
                ],
                'acl_action' => 'edit',
            ],
            [
                'name' => 'calc_field_button',
                'type' => 'button',
                'label' => 'LBL_UPDATE_CALC_FIELDS',
                'events' => [
                    'click' => 'list:updatecalcfields:fire',
                ],
                'acl_action' => 'massupdate',
            ],
            [
                'name' => 'delete_button',
                'type' => 'button',
                'label' => 'LBL_DELETE',
                'acl_action' => 'delete',
                'primary' => true,
                'events' => [
                    'click' => 'list:massdelete:fire',
                ],
            ],
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
];
