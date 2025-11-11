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
$viewdefs['DocumentRevisions']['EditView'] = [
    'templateMeta' => ['form' => ['enctype' => 'multipart/form-data',
        'hidden' => ['<input type="hidden" name="return_id" value="{$smarty.request.return_id}">'],
    ],
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'javascript' => '{sugar_getscript file="include/javascript/popup_parent_helper.js"}
{sugar_getscript file="modules/Documents/documents.js"}',
    ],
    'panels' => [
        '' => [
            [
                ['name' => 'document_name', 'type' => 'readonly'],
                ['name' => 'latest_revision', 'type' => 'readonly'],
            ],
            [
                'revision',
            ],

            [
                'filename',
                'doc_type',
            ],

            [
                ['name' => 'change_log', 'size' => '126', 'maxlength' => '255'],
            ],

        ],
    ],
];
