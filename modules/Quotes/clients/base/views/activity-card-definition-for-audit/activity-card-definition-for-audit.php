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

$viewdefs['Quotes']['base']['view']['activity-card-definition-for-audit'] = [
    'module' => 'Audit',
    'record_date' => 'date_created',
    'fields' => [
        'quote_stage',
        'assigned_user_id',
        'date_quote_expected_closed',
        'total_usdollar',
    ],
];
