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

$viewdefs['Emails']['base']['view']['activity-card-definition'] = [
    'module' => 'Emails',
    'record_date' => 'date_sent',
    'fields' => [
        'name',
        'date_sent',
        'date_entered_by',
        'from_collection',
        'to_collection',
        'cc_collection',
        'bcc_collection',
        'description_html',
        'attachments_collection',
        'assigned_user_name',
        'state',
    ],
    'card_menu' => [
        [
            'name' => 'reply_icon',
            'type' => 'reply-action',
            'tplName' => 'activity-card-emailaction',
            'icon' => 'sicon-arrow-left',
            'tooltip' => 'LBL_EMAIL_REPLY',
        ],
        [
            'name' => 'reply_all_icon',
            'type' => 'reply-all-action',
            'tplName' => 'activity-card-emailaction',
            'icon' => 'sicon-reply-all',
            'tooltip' => 'LBL_EMAIL_REPLY_ALL',
        ],
        [
            'name' => 'forward_icon',
            'type' => 'forward-action',
            'tplName' => 'activity-card-emailaction',
            'icon' => 'sicon-arrow-right',
            'tooltip' => 'LBL_EMAIL_FORWARD',
        ],
    ],
];
