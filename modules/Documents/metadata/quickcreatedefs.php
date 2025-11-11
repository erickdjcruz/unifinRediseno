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
$viewdefs['Documents']['QuickCreate'] = [
    'templateMeta' => ['form' => ['enctype' => 'multipart/form-data',
        'hidden' => ['<input type="hidden" name="old_id" value="{$fields.document_revision_id.value}">',
            '<input type="hidden" name="parent_id" value="{$smarty.request.parent_id}">',
            '<input type="hidden" name="parent_type" value="{$smarty.request.parent_type}">',]],

        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'includes' => [
            ['file' => 'include/javascript/popup_parent_helper.js'],
            ['file' => 'modules/Documents/documents.js'],
        ],
    ],
    'panels' => [
        'default' => [

            [
                'doc_type',
                'status_id',
            ],
            [
                ['name' => 'filename',
                    'displayParams' => ['required' => true, 'onchangeSetFileNameTo' => 'document_name'],
                ],
            ],

            [
                'document_name',
                ['name' => 'revision',
                    'customCode' => '<input name="revision" type="text" value="{$fields.revision.value}" {$DISABLED}>',
                ],
            ],

            [
                ['name' => 'active_date', 'displayParams' => ['required' => true]],
                'category_id',
            ],

            [
                ['name' => 'assigned_user_name',],
                ['name' => 'team_name', 'displayParams' => ['required' => true]],
            ],

            [
                ['name' => 'description', 'displayParams' => ['rows' => 10, 'cols' => 120]],
            ],
        ],
    ],

];
