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

$vardefs = [
    'fields' => [

        'document_name' => [
            'name' => 'document_name',
            'vname' => 'LBL_NAME',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => '255',
            'required' => true,
            'unified_search' => true,
            'duplicate_on_record_copy' => 'always',
            'full_text_search' => [
                'enabled' => true,
                'searchable' => true,
                'boost' => 0.82,
            ],
        ],

        'name' => [
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'source' => 'non-db',
            'type' => 'varchar',
            'db_concat_fields' => [0 => 'document_name'],
            'duplicate_on_record_copy' => 'always',
            'fields' => ['document_name'],
        ],

        'filename' => [
            'name' => 'filename',
            'vname' => 'LBL_FILENAME',
            'type' => 'varchar',
            'required' => true,
            'importable' => 'required',
            'len' => '255',
            'studio' => 'false',
            'duplicate_on_record_copy' => 'always',
            // Associating file_ext and file_mime_type to force these fields to be valued
            // when selecting filename
            'fields' => ['file_ext', 'file_mime_type'],
        ],
        'file_ext' => [
            'name' => 'file_ext',
            'vname' => 'LBL_FILE_EXTENSION',
            'type' => 'varchar',
            'len' => 100,
            'duplicate_on_record_copy' => 'always',
        ],
        'file_mime_type' => [
            'name' => 'file_mime_type',
            'vname' => 'LBL_MIME',
            'type' => 'varchar',
            'len' => '100',
            'duplicate_on_record_copy' => 'always',
        ],


        'uploadfile' => [
            'name' => 'uploadfile',
            'vname' => 'LBL_FILE_UPLOAD',
            'type' => 'file',
            'source' => 'non-db',
            'duplicate_on_record_copy' => 'always',
            'fields' => ['filename'],
        ],

        'active_date' => [
            'name' => 'active_date',
            'vname' => 'LBL_DOC_ACTIVE_DATE',
            'type' => 'date',
            'importable' => 'required',
            'display_default' => 'now',
            'duplicate_on_record_copy' => 'always',
        ],

        'exp_date' => [
            'name' => 'exp_date',
            'vname' => 'LBL_DOC_EXP_DATE',
            'type' => 'date',
            'duplicate_on_record_copy' => 'always',
        ],

        'category_id' => [
            'name' => 'category_id',
            'vname' => 'LBL_SF_CATEGORY',
            'type' => 'enum',
            'len' => 100,
            'options' => 'document_category_dom',
            'reportable' => false,
            'duplicate_on_record_copy' => 'always',
        ],

        'subcategory_id' => [
            'name' => 'subcategory_id',
            'vname' => 'LBL_SF_SUBCATEGORY',
            'type' => 'enum',
            'len' => 100,
            'options' => 'document_subcategory_dom',
            'reportable' => false,
            'duplicate_on_record_copy' => 'always',
        ],

        'status_id' => [
            'name' => 'status_id',
            'vname' => 'LBL_DOC_STATUS',
            'type' => 'enum',
            'len' => 100,
            'options' => 'document_status_dom',
            'reportable' => false,
            'duplicate_on_record_copy' => 'always',
        ],

        'status' => [
            'name' => 'status',
            'vname' => 'LBL_DOC_STATUS',
            'type' => 'varchar',
            'source' => 'non-db',
            'duplicate_on_record_copy' => 'always',
            'Comment' => 'Document status for Meta-Data framework',
        ],
    ],
    'uses' => [
        'taggable',
        'audit',
    ],
    'duplicate_check' => [
        'enabled' => true,
        'FilterDuplicateCheck' => [
            'filter_template' => [
                ['document_name' => ['$starts' => '$document_name']],
            ],
            'ranking_fields' => [
                ['in_field_name' => 'document_name', 'dupe_field_name' => 'document_name'],
            ],
        ],
    ],
];
