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
$module_name = 'ProductCategories';
$viewdefs[$module_name]['base']['menu']['header'] = [
    [
        'route' => "#$module_name/create",
        'label' => 'LNK_NEW_PRODUCT_CATEGORY',
        'acl_action' => 'create',
        'acl_module' => $module_name,
        'icon' => 'sicon-plus',
    ],
    [
        'route' => '#ProductCategories',
        'label' => 'LNK_VIEW_PRODUCT_CATEGORIES',
        'acl_action' => 'list',
        'acl_module' => 'ProductCategories',
        'icon' => 'sicon-list-view',
    ],
    [
        'route' => '#ProductTemplates',
        'label' => 'LNK_PRODUCT_LIST',
        'acl_action' => 'list',
        'acl_module' => 'ProductTemplates',
        'icon' => 'sicon-list-view',
    ],
    [
        'route' => '#Manufacturers',
        'label' => 'LNK_NEW_MANUFACTURER',
        'acl_action' => 'list',
        'acl_module' => 'Manufacturers',
        'icon' => 'sicon-list-view',
    ],
    [
        'route' => '#ProductTypes',
        'label' => 'LNK_NEW_PRODUCT_TYPE',
        'acl_action' => 'list',
        'acl_module' => 'ProductTypes',
        'icon' => 'sicon-list-view',
    ],
    [
        'route' => '#bwc/index.php?module=Import&action=Step1&import_module=ProductCategories&return_module=ProductCategories&return_action=index',
        'label' => 'LNK_IMPORT_PRODUCT_CATEGORIES',
        'acl_action' => 'create',
        'acl_module' => $module_name,
        'icon' => 'sicon-upload',
    ],

];
