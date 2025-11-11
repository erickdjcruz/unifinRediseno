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
/*********************************************************************************
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$module_name = '<module_name>';

$viewdefs[$module_name]['EditView'] = [
    'templateMeta' => ['form' => ['enctype' => 'multipart/form-data',
        'hidden' => []],

        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
        'javascript' =>
            '{sugar_getscript file="include/javascript/popup_parent_helper.js"}
	{sugar_getscript file="modules/Documents/documents.js"}',
    ],
    'panels' => [
        'default' => [

            [
                'document_name',
                [
                    'name' => 'uploadfile',
                    'displayParams' => ['onchangeSetFileNameTo' => 'document_name'],
                ],

            ],

            [
                'category_id',
                'subcategory_id',
            ],

            [
                'assigned_user_name',
                ['name' => 'team_name', 'displayParams' => ['required' => true]],
            ],

            [
                'active_date',
                'exp_date',
            ],

            ['status_id'],
            [

                ['name' => 'description'],

            ],
        ],
    ],
];
