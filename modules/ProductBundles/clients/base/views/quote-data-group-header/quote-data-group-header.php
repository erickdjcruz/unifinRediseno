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
$viewdefs['ProductBundles']['base']['view']['quote-data-group-header'] = [
    'buttons' => [
        [
            'type' => 'quote-data-actiondropdown',
            'name' => 'create-dropdown',
            'icon' => 'sicon-plus',
            'no_default_action' => true,
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'css_class' => 'btn-invisible',
                    'icon' => 'sicon-plus',
                    'name' => 'create_qli_button',
                    'label' => 'LBL_CREATE_QLI_BUTTON_LABEL',
                    'tooltip' => 'LBL_CREATE_QLI_BUTTON_TOOLTIP',
                    'acl_action' => 'create',
                ],
                [
                    'type' => 'rowaction',
                    'css_class' => 'btn-invisible',
                    'icon' => 'sicon-plus',
                    'name' => 'create_comment_button',
                    'label' => 'LBL_CREATE_COMMENT_BUTTON_LABEL',
                    'tooltip' => 'LBL_CREATE_COMMENT_BUTTON_TOOLTIP',
                    'acl_action' => 'create',
                ],
            ],
        ],
        [
            'type' => 'quote-data-actiondropdown',
            'name' => 'edit-dropdown',
            'icon' => 'sicon-kebab',
            'no_default_action' => true,
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'name' => 'edit_bundle_button',
                    'label' => 'LBL_EDIT_BUTTON',
                    'tooltip' => 'LBL_EDIT_BUNDLE_BUTTON_TOOLTIP',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'name' => 'delete_bundle_button',
                    'label' => 'LBL_DELETE_GROUP_BUTTON',
                    'tooltip' => 'LBL_DELETE_BUNDLE_BUTTON_TOOLTIP',
                    'acl_action' => 'delete',
                ],
            ],
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_quote_data_group_header',
            'label' => 'LBL_QUOTE_DATA_GROUP_HEADER',
            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'quote-group-title',
                    'css_class' => 'group-name',
                ],
            ],
        ],
    ],
];
