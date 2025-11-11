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
        'is_escalated' => [
            'name' => 'is_escalated',
            'vname' => 'LBL_IS_ESCALATED',
            'type' => 'bool',
            'default' => '0',
            'readonly' => true,
            'displayParams' => [
                'type' => 'badge',
                'badge_label' => 'LBL_ESCALATED',
                'warning_level' => 'important',
            ],
            'comment' => 'Is this escalated?',
            'studio' => [
                'previewview' => false,
                'recorddashletview' => false,
                'portalrecordview' => false,
                'portallistview' => false,
                'mobile' => [
                    'wirelesseditview' => false,
                    'wirelessdetailview' => false,
                ],
            ],
        ],
    ],
];
