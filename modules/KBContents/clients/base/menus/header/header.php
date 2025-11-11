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

$module_name = 'KBContents';
$viewdefs[$module_name]['base']['menu']['header'] = [
    [
        'route' => "#{$module_name}/create",
        'label' => 'LNK_NEW_ARTICLE',
        'acl_action' => 'create',
        'acl_module' => $module_name,
        'icon' => 'sicon-plus',
    ],
    [
        'route' => '#KBContentTemplates/create',
        'label' => 'LNK_NEW_KBCONTENT_TEMPLATE',
        'acl_action' => 'admin',
        'acl_module' => $module_name,
        'icon' => 'sicon-plus',
    ],
    [
        'route' => "#{$module_name}",
        'label' => 'LNK_LIST_ARTICLES',
        'acl_action' => 'list',
        'acl_module' => $module_name,
        'icon' => 'sicon-list-view',
    ],
    [
        'route' => '#KBContentTemplates',
        'label' => 'LNK_LIST_KBCONTENT_TEMPLATES',
        'acl_action' => 'admin',
        'acl_module' => $module_name,
        'icon' => 'sicon-list-view',
    ],
    [
        'event' => 'tree:list:fire',
        'label' => 'LNK_LIST_KBCATEGORIES',
        'acl_action' => 'list',
        'acl_module' => $module_name,
        'icon' => 'sicon-list-view',
        'target' => 'view',
    ],
    [
        'route' => "#{$module_name}/config",
        'label' => 'LNK_KNOWLEDGE_BASE_ADMIN_MENU',
        'acl_action' => 'admin',
        'acl_module' => $module_name,
        'icon' => 'sicon-settings',
    ],
];
