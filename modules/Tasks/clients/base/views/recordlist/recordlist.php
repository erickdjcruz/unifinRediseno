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
$viewdefs['Tasks']['base']['view']['recordlist']['rowactions']['actions'] = [
    [
        'type' => 'rowaction',
        'css_class' => 'btn',
        'tooltip' => 'LBL_PREVIEW',
        'event' => 'list:preview:fire',
        'icon' => 'sicon-preview',
        'acl_action' => 'view',
    ],
    [
        'type' => 'rowaction',
        'name' => 'edit_button',
        'icon' => 'sicon-edit',
        'label' => 'LBL_EDIT_BUTTON',
        'event' => 'list:editrow:fire',
        'acl_action' => 'edit',
    ],
    [
        'type' => 'follow',
        'name' => 'follow_button',
        'event' => 'list:follow:fire',
        'acl_action' => 'view',
    ],
    [
        'type' => 'closebutton',
        'name' => 'record-close',
        'label' => 'LBL_CLOSE_BUTTON_TITLE',
        'closed_status' => 'Completed',
        'acl_action' => 'edit',
    ],
    [
        'type' => 'rowaction',
        'name' => 'delete_button',
        'icon' => 'sicon-trash',
        'event' => 'list:deleterow:fire',
        'label' => 'LBL_DELETE_BUTTON',
        'acl_action' => 'delete',
    ],
];
