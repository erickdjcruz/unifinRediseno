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

/**
 * table storing reports filter information */
$dictionary['oauth_nonce'] = [
    'table' => 'oauth_nonce',
    'fields' => [
        'conskey' => [
            'name' => 'conskey',
            'type' => 'varchar',
            'len' => 32,
            'required' => true,
            'isnull' => false,
        ],
        'nonce' => [
            'name' => 'nonce',
            'type' => 'varchar',
            'len' => 32,
            'required' => true,
            'isnull' => false,
        ],
        'nonce_ts' => [
            'name' => 'nonce_ts',
            'type' => 'long',
            'required' => true,
        ],
    ],
    'indices' => [
        [
            'name' => 'oauth_nonce_pk',
            'type' => 'primary',
            'fields' => ['conskey', 'nonce'],
        ],
        [
            'name' => 'oauth_nonce_keyts',
            'type' => 'index',
            'fields' => ['conskey', 'nonce_ts'],
        ],
    ],
];
