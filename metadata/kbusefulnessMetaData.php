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

$dictionary['kbusefulness'] = [
    'table' => 'kbusefulness',
    'fields' => [
        'id' => [
            'name' => 'id',
            'type' => 'id',
            'required' => true,
        ],
        'kbarticle_id' => [
            'name' => 'kbarticle_id',
            'type' => 'id',
            'required' => true,
        ],
        'user_id' => [
            'name' => 'user_id',
            'type' => 'id',
            'required' => true,
        ],
        'contact_id' => [
            'name' => 'contact_id',
            'type' => 'id',
            'required' => false,
            'isnull' => true,
        ],
        'vote' => [
            'name' => 'vote',
            'type' => 'smallint',
            'isnull' => 'true',
        ],
        'zeroflag' => [
            'name' => 'zeroflag',
            'type' => 'tinyint',
            'isnull' => 'true',
        ],
        'ssid' => [
            'name' => 'ssid',
            'type' => 'id',
            'isnull' => 'true',
        ],

        'date_modified' => [
            'name' => 'date_modified',
            'type' => 'datetime',
        ],

        'deleted' => [
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'default' => '0',
        ],
    ],
    'indices' => [
        [
            'name' => 'kbusefulness_pk',
            'type' => 'primary',
            'fields' => ['id'],
        ],
        [
            'name' => 'kbusefulness_user',
            'type' => 'index',
            'fields' => ['kbarticle_id', 'user_id'],
        ],
    ],

    'relationships' => [
        'kbusefulness' => [
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'KBContents',
            'rhs_table' => 'kbcontents',
            'rhs_key' => 'kbarticle_id',
            'join_key_rhs' => 'kbarticle_id',
            'join_key_lhs' => 'user_id',
            'true_relationship_type' => 'many-to-many',
            'primary_flag_column' => 'zeroflag',
            'relationship_class' => 'KBUsefulnessRelationship',
            'relationship_file' => 'modules/KBContents/KBUsefulnessRelationship.php',
        ],
    ],
];
