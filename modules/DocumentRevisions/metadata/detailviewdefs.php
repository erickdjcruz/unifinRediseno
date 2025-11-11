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
$viewdefs['DocumentRevisions']['DetailView'] = [
    'templateMeta' => ['maxColumns' => '2',
        'form' => [
            'buttons' => [],
            'hidden' => ['<input type="hidden" name="old_id" value="{$fields.document_revision_id.value}">']],
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        '' => [
            [
                [
                    'name' => 'document_name',
                    'customCode' => '<a href="index.php?module=DocumentRevisions&action=DetailView&record={$fields.document_id.value|escape:"url"}">{$fields.document_name.value|escape:"html":"UTF-8"}</a>',
                ],
                'latest_revision',
            ],

            [
                'revision',
            ],

            [
                'filename',
                'doc_type',
            ],

            [
                [
                    'name' => 'date_entered',
                    'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value|escape:"html":"UTF-8"}',
                ],
            ],

            [
                'change_log',
            ],
        ],
    ],
];
