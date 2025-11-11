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

$dictionary['DocumentTemplate'] = [
    'table' => 'document_templates',
    'audited' => true,
    'color' => 'coral',
    'icon' => 'sicon-document-lg',
    'favorites' => true,
    'comment' => 'Document Templates are used with Document Merging',
    'duplicate_check' => [
        'enabled' => false,
    ],
    'fields' => [
        'filename' => [
            'name' => 'filename',
            'vname' => 'LBL_FILENAME',
            'type' => 'file',
            'duplicate_on_record_copy' => 'always',
            'required' => true,
        ],
        'file_ext' => [
            'name' => 'file_ext',
            'vname' => 'LBL_FILE_EXTENSION',
            'type' => 'varchar',
            'len' => 100,
        ],
        'file_mime_type' => [
            'name' => 'file_mime_type',
            'vname' => 'LBL_MIME',
            'type' => 'varchar',
            'len' => '100',
        ],
        'file_size' => [
            'name' => 'file_size',
            'vname' => 'LBL_FILE_SIZE',
            'type' => 'int',
            'comment' => 'The size of the file',
            'importable' => false,
            'massupdate' => false,
        ],
        'template_module' => [
            'duplicate_merge_dom_value' => 0,
            'name' => 'template_module',
            'vname' => 'LBL_TEMPLATE_MODULE',
            'type' => 'enum',
            'default' => 'Accounts',
            'len' => 100,
            'size' => 20,
            'options' => '',
            'function' => [
                'name' => 'getTargetModules',
                'include' => 'src/DocumentMerge/DocumentTemplateHelper.php',
            ],
        ],
        'use_revisions' => [
            'name' => 'use_revisions',
            'vname' => 'LBL_USE_REVISIONS',
            'type' => 'bool',
            'default' => true,
        ],
        'label_merging' => [
            'name' => 'label_merging',
            'vname' => 'LBL_LABEL_MERGING',
            'type' => 'bool',
            'default' => false,
        ],
        'prefix' => [
            'name' => 'prefix',
            'vname' => 'LBL_PREFIX',
            'type' => 'varchar',
            'len' => 120,
        ],
        'postfix' => [
            'name' => 'postfix',
            'vname' => 'LBL_POSTFIX',
            'type' => 'varchar',
            'len' => 120,
        ],
    ],
    'optimistic_lock' => true,
];

VardefManager::createVardef('DocumentTemplates', 'DocumentTemplate', ['basic', 'assignable', 'team_security']);
