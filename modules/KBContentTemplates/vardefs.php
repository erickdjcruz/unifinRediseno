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

$dictionary['KBContentTemplate'] = [
    'table' => 'kbcontent_templates',
    'audited' => true,
    'activity_enabled' => true,
    'comment' => 'A template is used as a body for KBContent.',
    'fields' => [
        'body' => [
            'name' => 'body',
            'vname' => 'LBL_TEXT_BODY',
            'type' => 'longtext',
            'comment' => 'Template body',
            'audited' => true,
        ],
    ],
    'relationships' => [],
    'duplicate_check' => [
        'enabled' => false,
    ],
    'uses' => [
        'basic',
        'team_security',
    ],
    'ignore_templates' => [
        'taggable',
        'commentlog',
    ],
    'acls' => [
        'SugarACLKB' => true,
        'SugarACLDeveloperOrAdmin' => [
            'aclModule' => 'KBContents',
            'allowUserRead' => true,
        ],
    ],
];

VardefManager::createVardef(
    'KBContentTemplates',
    'KBContentTemplate'
);
$dictionary['KBContentTemplate']['fields']['name']['audited'] = true;
