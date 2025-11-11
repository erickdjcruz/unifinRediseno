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
$dictionary['UserSignature'] = [
    'table' => 'users_signatures',
    'archive' => false,
    'hidden_to_role_assignment' => true,
    'favorites' => false,
    'fields' => [
        'user_id' => [
            'name' => 'user_id',
            'vname' => 'LBL_USER_ID',
            'type' => 'id',
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => 255,
            'unified_search' => true,
            'required' => true,
            'importable' => 'required',
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'selected',
            'duplicate_on_record_copy' => 'always',
        ],
        'signature' => [
            'name' => 'signature',
            'vname' => 'LBL_SIGNATURE',
            'type' => 'text',
            'reportable' => false,
        ],
        'signature_html' => [
            'name' => 'signature_html',
            'vname' => 'LBL_SIGNATURE_HTML',
            'type' => 'text',
            'reportable' => false,
        ],
        'is_default' => [
            'name' => 'is_default',
            'vname' => 'LBL_DEFAULT_SIGNATURE',
            'type' => 'bool',
            'sortable' => false,
            'source' => 'non-db',
            'duplicate_on_record_copy' => 'no',
            'massupdate' => false,
        ],
    ],
    'indices' => [
        [
            'name' => 'idx_usersig_uid',
            'type' => 'index',
            'fields' => ['user_id'],
        ],
        [
            'name' => 'idx_usersig_created_by',
            'type' => 'index',
            'fields' => ['created_by'],
        ],
    ],
    'visibility' => ['OwnerVisibility' => true],
];
VardefManager::createVardef('UserSignatures', 'UserSignature', ['default']);
