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
$viewdefs['Emails']['base']['filter']['default'] = [
    'default_filter' => 'all_records',
    'fields' => [
        'name' => [],
        'state' => [
            'vname' => 'LBL_LIST_STATUS',
        ],
        'date_sent' => [
            'vname' => 'LBL_LIST_DATE_COLUMN',
        ],
        'assigned_user_name' => [],
        'parent_name' => [],
        'direction' => [],
        'tag' => [],
        'mailbox_name' => [],
        'total_attachments' => [],
        '$owner' => [
            'predefined_filter' => true,
            'vname' => 'LBL_CURRENT_USER_FILTER',
        ],
        '$favorite' => [
            'predefined_filter' => true,
            'vname' => 'LBL_FAVORITES_FILTER',
        ],
        'from_collection' => [
            'type' => 'email-recipients',
            'decorate_invalid' => false,
            'decorate_opt_out' => false,
        ],
        'to_collection' => [
            'type' => 'email-recipients',
            'decorate_invalid' => false,
            'decorate_opt_out' => false,
        ],
        'cc_collection' => [
            'type' => 'email-recipients',
            'decorate_invalid' => false,
            'decorate_opt_out' => false,
        ],
        'bcc_collection' => [
            'type' => 'email-recipients',
            'decorate_invalid' => false,
            'decorate_opt_out' => false,
        ],
    ],
];
