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

$viewdefs['Leads']['base']['view']['activity-card-definition-for-audit'] = [
    'module' => 'Audit',
    'record_date' => 'date_created',
    'fields' => [
        'salutation',
        'first_name',
        'last_name',
        'assigned_user_id',
        'title',
        'lead_source',
        'status',
        'phone_work',
        'email',
        'ai_conv_score_classification',
    ],
];
