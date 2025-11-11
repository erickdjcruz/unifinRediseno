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
$viewdefs['Cases']['base']['view']['list'] = [
    'panels' => [
        0 =>
            [
                'label' => 'LBL_PANEL_1',
                'fields' => [
                    [
                        'name' => 'case_number',
                        'label' => 'LBL_LIST_NUMBER',
                        'link' => true,
                        'default' => true,
                        'enabled' => true,
                        'readonly' => true,
                    ],
                    [
                        'name' => 'name',
                        'label' => 'LBL_LIST_SUBJECT',
                        'link' => true,
                        'default' => true,
                        'enabled' => true,
                    ],
                    [
                        'name' => 'account_name',
                        'label' => 'LBL_LIST_ACCOUNT_NAME',
                        'module' => 'Accounts',
                        'id' => 'ACCOUNT_ID',
                        'ACLTag' => 'ACCOUNT',
                        'related_fields' => ['account_id'],
                        'link' => true,
                        'default' => true,
                        'enabled' => true,
                    ],
                    [
                        'name' => 'priority',
                        'label' => 'LBL_LIST_PRIORITY',
                        'default' => true,
                        'enabled' => true,
                    ],
                    [
                        'name' => 'status',
                        'label' => 'LBL_STATUS',
                        'default' => true,
                        'enabled' => true,
                    ],
                    [
                        'name' => 'assigned_user_name',
                        'label' => 'LBL_ASSIGNED_TO_NAME',
                        'id' => 'ASSIGNED_USER_ID',
                        'default' => true,
                        'enabled' => true,
                    ],
                    [
                        'name' => 'date_modified',
                        'enabled' => true,
                        'default' => true,
                    ],
                    [
                        'name' => 'date_entered',
                        'label' => 'LBL_DATE_ENTERED',
                        'default' => true,
                        'enabled' => true,
                        'readonly' => true,
                    ],
                    [
                        'name' => 'team_name',
                        'label' => 'LBL_LIST_TEAM',
                        'default' => false,
                        'enabled' => true,
                    ],
                    [
                        'name' => 'primary_contact_name',
                        'label' => 'LBL_PRIMARY_CONTACT_NAME',
                        'default' => false,
                        'enabled' => true,
                    ],
                    [
                        'name' => 'request_close',
                        'label' => 'LBL_REQUEST_CLOSE',
                        'default' => false,
                        'enabled' => true,
                        'readonly' => true,
                    ],
                    [
                        'name' => 'request_close_date',
                        'label' => 'LBL_REQUEST_CLOSE_DATE',
                        'default' => false,
                        'enabled' => true,
                        'readonly' => true,
                    ],
                    [
                        'name' => 'business_center_name',
                        'label' => 'LBL_BUSINESS_CENTER_NAME',
                        'default' => false,
                        'enabled' => true,
                        'readonly' => true,
                    ],
                    [
                        'name' => 'service_level',
                        'label' => 'LBL_SERVICE_LEVEL',
                        'default' => false,
                        'enabled' => true,
                        'readonly' => true,
                    ],
                    [
                        'name' => 'follow_up_datetime',
                        'label' => 'LBL_FOLLOW_UP',
                        'default' => false,
                        'enabled' => true,
                    ],
                    [
                        'name' => 'first_response_sla_met',
                        'label' => 'LBL_FIRST_RESPONSE_SLA_MET',
                        'default' => false,
                        'enabled' => true,
                        'readonly' => true,
                    ],
                    [
                        'name' => 'is_escalated',
                        'label' => 'LBL_ESCALATED',
                        'badge_label' => 'LBL_ESCALATED',
                        'warning_level' => 'important',
                        'type' => 'badge',
                        'enabled' => true,
                        'default' => false,
                    ],
                ],
            ],
    ],
];
