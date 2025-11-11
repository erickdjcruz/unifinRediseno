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
$viewdefs['DRI_Workflow_Templates']['base']['view']['recordlist']['selection'] = [
    'type' => 'multi',
    'actions' => [
        [
            'name' => 'massupdate_button',
            'type' => 'button',
            'label' => 'LBL_MASS_UPDATE',
            'primary' => true,
            'events' => [
                'click' => 'list:massupdate:fire',
            ],
            'acl_action' => 'massupdate',
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
            'name' => 'massdelete_button',
            'type' => 'button',
            'label' => 'LBL_DELETE',
            'acl_action' => 'delete',
            'primary' => true,
            'events' => [
                'click' => 'list:massdelete:fire',
            ],
        ],
    ],
];
