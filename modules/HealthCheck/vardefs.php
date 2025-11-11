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

$dictionary['HealthCheck'] = [
    'table' => 'healthcheck',
    'fields' => [
        'logfile' => [
            'name' => 'logfile',
            'vname' => 'LBL_LOGFILE',
            'type' => 'varchar',
            'len' => 255,
        ],
        'bucket' => [
            'name' => 'bucket',
            'vname' => 'LBL_BUCKET',
            'type' => 'char',
            'len' => 1,
        ],
        'flag' => [
            'name' => 'flag',
            'vname' => 'LBL_FLAG',
            'type' => 'int',
            'len' => 1,
        ],
        'logmeta' => [
            'name' => 'logmeta',
            'vname' => 'LBL_LOGMETA',
            'type' => 'json',
            //Sugar 6 does not support `type` => `json` so `blob` is needed to skip SugarBean::cleanBean
            'dbType' => 'longtext',
        ],
        'error' => [
            'name' => 'error',
            'vname' => 'LBL_ERROR',
            'type' => 'varchar',
            'len' => 255,
        ],
    ],
    'relationships' => [],
    'optimistic_locking' => false,
    'uses' => [
        'default',
    ],
    'acls' => [
        'SugarACLAdminOnly' => true,
    ],
];

VardefManager::createVardef('HealthCheck', 'HealthCheck');
