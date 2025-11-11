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

$dictionary['EmbeddedFile'] = [
    'table' => 'embedded_files',
    'audited' => false,
    'activity_enabled' => false,
    'comment' => 'Files for KBContent body.',
    'fields' => [
        'filename' => [
            'name' => 'filename',
            'vname' => 'LBL_FILENAME',
            'type' => 'file',
            'dbType' => 'varchar',
            'len' => '255',
            'importable' => false,
        ],
        'file_mime_type' => [
            'name' => 'file_mime_type',
            'vname' => 'LBL_FILE_MIME_TYPE',
            'type' => 'varchar',
            'len' => '100',
            'importable' => false,
        ],
    ],
    'relationships' => [],
    'duplicate_check' => [
        'enabled' => false,
    ],
    'uses' => [
        'basic',
    ],
    'ignore_templates' => [
        'taggable',
        'commentlog',
    ],
    'portal_visibility' => [
        'class' => 'KBContents',
    ],
];

VardefManager::createVardef(
    'EmbeddedFiles',
    'EmbeddedFile'
);
