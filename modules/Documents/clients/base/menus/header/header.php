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
$module_name = 'Documents';
$viewdefs[$module_name]['base']['menu']['header'] = [
    [
        'route' => '#' . $module_name . '/create',
        'label' => 'LNK_NEW_DOCUMENT',
        'acl_action' => 'create',
        'acl_module' => $module_name,
        'icon' => 'sicon-plus',
    ],
    [
        'route' => '#' . $module_name,
        'label' => 'LNK_DOCUMENT_LIST',
        'acl_action' => 'list',
        'acl_module' => $module_name,
        'icon' => 'sicon-list-view',
    ],
    //TODO look at old file and deal with this
    /*array(
        'route'=>'#bwc/index.php?module=MailMerge&action=index&reset=true',
        'label' =>'LNK_NEW_MAIL_MERGE',
        'acl_action'=>'',
        'acl_module'=>'',
        'icon' => '',
    ),*/
];
