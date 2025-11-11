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
$viewdefs['CJ_Forms']['base']['menu']['header'] = [
    [
        'label' => 'LNK_NEW_RECORD',
        'acl_action' => 'create',
        'acl_module' => 'CJ_Forms',
        'icon' => 'sicon-plus',
        'route' => '#CJ_Forms/create',
    ],
    [
        'route' => '#CJ_Forms',
        'label' => 'LNK_CJ_FORM_LIST',
        'acl_action' => 'list',
        'acl_module' => 'CJ_Forms',
        'icon' => 'sicon-list-view',
    ],
];
