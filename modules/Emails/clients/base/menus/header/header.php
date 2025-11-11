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
$module_name = 'Emails';
$viewdefs[$module_name]['base']['menu']['header'] = [
    [
        'route' => '#' . $module_name . '/compose',
        'label' => 'LBL_COMPOSE_MODULE_NAME_SINGULAR',
        'acl_action' => 'create',
        'acl_module' => $module_name,
        'icon' => 'sicon-plus',
    ],
    [
        'route' => '#' . $module_name . '/create',
        'label' => 'LBL_CREATE_ARCHIVED_EMAIL',
        'acl_action' => 'create',
        'acl_module' => $module_name,
        'icon' => 'sicon-plus',
    ],
    [
        'route' => '#' . $module_name,
        'label' => 'LNK_EMAIL_LIST',
        'acl_action' => 'list',
        'acl_module' => $module_name,
        'icon' => 'sicon-list-view',
    ],
    [
        'route' => '#EmailTemplates/create',
        'label' => 'LNK_NEW_EMAIL_TEMPLATE',
        'acl_action' => 'create',
        'acl_module' => 'EmailTemplates',
        'icon' => 'sicon-plus',
    ],
    [
        'route' => '#EmailTemplates',
        'label' => 'LNK_EMAIL_TEMPLATE_LIST',
        'acl_action' => 'list',
        'acl_module' => 'EmailTemplates',
        'icon' => 'sicon-list-view',
    ],
    [
        'route' => '#UserSignatures/create',
        'label' => 'LNK_NEW_EMAIL_SIGNATURE',
        'acl_action' => 'create',
        'acl_module' => 'Emails',
        'icon' => 'sicon-plus',
    ],
    [
        'route' => '#UserSignatures',
        'label' => 'LNK_EMAIL_SIGNATURE_LIST',
        'acl_action' => 'create',
        'acl_module' => 'Emails',
        'icon' => 'sicon-list-view',
    ],
    [
        'route' => '#OutboundEmail',
        'label' => 'LNK_EMAIL_SETTINGS_LIST',
        'acl_action' => 'list',
        'acl_module' => 'OutboundEmail',
        'icon' => 'sicon-settings',
    ],
];
