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
$viewdefs['CJ_WebHooks']['mobile']['view']['edit'] = [
    'templateMeta' => [
        'maxColumns' => '1',
        'widths' => [
            [
                'label' => '10',
                'field' => '30',
            ],
            [
                'label' => '10',
                'field' => '30',
            ],
        ],
    ],
    'panels' => [
        [
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                'name',
                'parent_name',
                'active',
                'sort_order',
                [
                    'name' => 'trigger_event',
                    'label' => 'LBL_TRIGGER_EVENT',
                    'type' => 'trigger-event',
                ],
                'request_method',
                'ignore_errors',
                [
                    'name' => 'url',
                    'label' => 'LBL_URL',
                    'span' => 12,
                ],
                'request_format',
                'response_format',
                'request_body',
                'custom_post_body',
                [
                    'name' => 'headers',
                    'label' => 'LBL_HEADERS',
                    'span' => 6,
                ],
                [
                    'name' => 'description',
                    'comment' => 'Full text of the note',
                    'label' => 'LBL_DESCRIPTION',
                    'span' => 6,
                ],
                'team_name',
                'date_entered',
                'created_by_name',
                'date_modified',
                'modified_by_name',
            ],
        ],
    ],
];
