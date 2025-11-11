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
$_object_name = '<_object_name>';
$viewdefs[$module_name]['DetailView'] = [
    'templateMeta' => ['maxColumns' => '2',
        'form' => [],
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [

        [

            [
                'name' => 'document_name',
                'label' => 'LBL_DOC_NAME',
            ],
            [
                'name' => 'uploadfile',
                'displayParams' => ['link' => 'uploadfile', 'id' => 'id'],
            ],


        ],
        [
            'category_id',
            'subcategory_id',
        ],

        [

            'status',

        ],
        [
            'active_date',
            'exp_date',
        ],

        [
            'team_name',
            ['name' => 'assigned_user_name', 'label' => 'LBL_ASSIGNED_TO'],
        ],

        [

            [
                'name' => 'description',
                'label' => 'LBL_DOC_DESCRIPTION',
            ],
        ],

    ],
];
