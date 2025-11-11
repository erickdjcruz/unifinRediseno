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
$viewdefs['KBContents']['base']['view']['recordlist'] = [
    'favorite' => true,
    'following' => true,
    'rowactions' => [
        'actions' => [
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
                'label' => 'LBL_EDIT_BUTTON',
                'event' => 'list:editrow:fire',
                'acl_action' => 'edit',
            ],
            [
                'type' => 'rowaction',
                'event' => 'button:create_localization_button:click',
                'name' => 'create_localization_button',
                'label' => 'LBL_CREATE_LOCALIZATION_BUTTON_LABEL',
                'acl_action' => 'edit',
            ],
            [
                'type' => 'rowaction',
                'event' => 'button:create_revision_button:click',
                'name' => 'create_revision_button',
                'label' => 'LBL_CREATE_REVISION_BUTTON_LABEL',
                'acl_action' => 'edit',
            ],
            [
                'type' => 'follow',
                'name' => 'follow_button',
                'event' => 'list:follow:fire',
                'acl_action' => 'view',
            ],
            [
                'type' => 'rowaction',
                'name' => 'delete_button',
                'event' => 'list:deleterow:fire',
                'label' => 'LBL_DELETE_BUTTON',
                'acl_action' => 'delete',
            ],
        ],
    ],
    'selection' => [
        'type' => 'multi',
        'actions' => [
            [
                'name' => 'massupdate_button',
                'type' => 'button',
                'label' => 'LBL_MASS_UPDATE',
                'primary' => true,
                'events' => [
                    'click' => 'list:massupdate:fire',
                ],
                'acl_action' => 'massupdate',
            ],
            [
                'name' => 'calc_field_button',
                'type' => 'button',
                'label' => 'LBL_UPDATE_CALC_FIELDS',
                'events' => [
                    'click' => 'list:updatecalcfields:fire',
                ],
                'acl_action' => 'massupdate',
            ],
            [
                'name' => 'massdelete_button',
                'type' => 'button',
                'label' => 'LBL_DELETE',
                'acl_action' => 'delete',
                'primary' => true,
                'events' => [
                    'click' => 'list:massdelete:fire',
                ],
            ],
            /* Should be disabled in 7.7
                        array(
                            'name' => 'export_button',
                            'type' => 'button',
                            'label' => 'LBL_EXPORT',
                            'acl_action' => 'export',
                            'primary' => true,
                            'events' => array(
                                'click' => 'list:massexport:fire',
                            ),
                        ), */
        ],
    ],
    'last_state' => [
        'id' => 'record-list',
    ],
];
