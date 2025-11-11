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

$vardefs = [
    'fields' => [
        'aws_comprehend_data' => [
            'name' => 'aws_comprehend_data',
            'vname' => 'LBL_AWS_COMPREHEND_DATA',
            'type' => 'text',
            'reportable' => false,
            'studio' => false,
            'comment' => 'Raw data from the aws comprehend service',
        ],
        'sentiment' => [
            'name' => 'sentiment',
            'vname' => 'LBL_SENTIMENT',
            'type' => 'varchar',
            'len' => 36,
            'studio' => false,
            'comment' => 'The sentiment string value (e.g POSITIVE, NEGATIVE, NEUTRAL, MIXED)',
        ],
    ],
];
