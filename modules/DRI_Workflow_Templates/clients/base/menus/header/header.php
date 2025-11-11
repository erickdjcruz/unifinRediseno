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
$viewdefs['DRI_Workflow_Templates']['base']['menu']['header'] = [
    [
        'label' => 'LNK_NEW_RECORD',
        'acl_action' => 'create',
        'acl_module' => 'DRI_Workflow_Templates',
        'icon' => 'sicon-plus',
        'route' => '#DRI_Workflow_Templates/create',
    ],
    [
        'route' => '#DRI_Workflow_Templates',
        'label' => 'LNK_DRI_WORKFLOW_TEMPLATE_LIST',
        'acl_action' => 'list',
        'acl_module' => 'DRI_Workflow_Templates',
        'icon' => 'sicon-list-view',
    ],
    [
        'route' => '#DRI_Workflow_Templates/layout/template-import',
        'label' => 'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES',
        'acl_action' => 'import',
        'acl_module' => 'DRI_Workflow_Templates',
        'icon' => 'sicon-upload',
    ],
];
