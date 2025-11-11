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
$viewdefs['EmailTemplates']['base']['view']['record'] = [
    'css_class' => 'emailtemplates-record',
    'buttons' => [
        [
            'type' => 'button',
            'name' => 'cancel_button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
            'showOn' => 'edit',
            'events' => [
                'click' => 'button:cancel_button:click',
            ],
        ],
        [
            'type' => 'rowaction',
            'event' => 'button:save_button:click',
            'name' => 'save_button',
            'label' => 'LBL_SAVE_BUTTON_LABEL',
            'css_class' => 'btn btn-primary',
            'showOn' => 'edit',
            'acl_action' => 'edit',
        ],
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'primary' => true,
            'showOn' => 'view',
            'buttons' => [
                [
                    'type' => 'rowaction',
                    'event' => 'button:edit_button:click',
                    'name' => 'edit_button',
                    'label' => 'LBL_EDIT_BUTTON_LABEL',
                    'acl_action' => 'edit',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:duplicate_button:click',
                    'name' => 'duplicate_button',
                    'label' => 'LBL_DUPLICATE_BUTTON_LABEL',
                    'acl_module' => 'EmailTemplates',
                    'acl_action' => 'create',
                ],
                [
                    'name' => 'delete_button',
                    'type' => 'rowaction',
                    'event' => 'button:delete_button:click',
                    'label' => 'LBL_DELETE_BUTTON',
                    'acl_action' => 'delete',
                ],
            ],
        ],
        [
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_header',
            'header' => true,
            'fields' => [
                [
                    'name' => 'picture',
                    'type' => 'avatar',
                    'size' => 'large',
                    'dismiss_label' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'name',
                    'type' => 'name',
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                'type',
                'team_name',
                [
                    'name' => 'description',
                    'span' => 12,
                    'rows' => 5,
                ],
                [
                    'name' => 'subject',
                    'span' => 12,
                ],
                'text_only',
                'assigned_user_name',
                [
                    'name' => 'insert-variable',
                    'type' => 'insert-variable',
                    'label' => 'LBL_INSERT_VARIABLE',
                    'span' => 12,
                    'moduleList' => [
                        [
                            'value' => 'Contacts',
                            'label' => ['Contacts', 'Leads', 'Prospects'],
                            'variable_source' => ['Contacts', 'Leads', 'Prospects'],
                            'variable_prefix' => 'contact_',
                        ],
                        [
                            'value' => 'Accounts',
                            'label' => 'LBL_ACCOUNT',
                            'variable_source' => ['Accounts'],
                            'variable_prefix' => 'account_',
                        ],
                        [
                            'value' => 'Users',
                            'label' => 'LBL_USER',
                            'variable_source' => ['Users'],
                            'variable_prefix' => 'contact_user_',
                        ],
                        [
                            'value' => 'Current User',
                            'label' => 'LBL_CURRENT_USER',
                            'variable_source' => ['Users'],
                            'variable_prefix' => 'user_',
                        ],
                        [
                            'value' => 'Cases',
                            'label' => 'LBL_CASE',
                            'variable_source' => ['Cases'],
                            'variable_prefix' => 'case_',
                        ],
                    ],
                ],
                [
                    'name' => 'body_set',
                    'type' => 'fieldset',
                    'label' => 'LBL_BODY',
                    'span' => 12,
                    'fields' => [
                        [
                            'name' => 'body_html',
                            'type' => 'htmleditable_tinymce',
                            'label' => 'LBL_BODY',
                            'span' => 12,
                            'tinyConfig' => [
                                'toolbar' => 'code help | bold italic underline strikethrough | alignleft aligncenter alignright ' .
                                    'alignjustify | forecolor backcolor | fontfamily fontsize blocks |' .
                                    'cut copy paste pastetext | search searchreplace | bullist numlist | ' .
                                    'outdent indent | ltr rtl | undo redo | link unlink anchor image | subscript ' .
                                    'superscript | charmap | table | hr removeformat | insertdatetime | ' .
                                    'sugarattachment',
                            ],
                        ],
                        [
                            'name' => 'plaintext',
                            'type' => 'show-plain-text',
                            'label' => 'LBL_SHOW_ALT_TEXT',
                            'label2' => 'LBL_HIDE_ALT_TEXT',
                        ],
                    ],
                ],
                [
                    'name' => 'body',
                    'type' => 'text',
                    'label' => 'LBL_PLAIN_TEXT',
                    'span' => 12,
                    'rows' => 11,
                    'css_class' => 'collapsed-plain-text',
                ],
                [
                    'name' => 'attachments_collection',
                    'type' => 'email-attachments',
                    'label' => 'LBL_ATTACHMENTS',
                    'span' => 12,
                    'max_num' => -1,
                    'fields' => [
                        'name',
                        'filename',
                        'file_size',
                        'file_source',
                        'file_mime_type',
                        'file_ext',
                        'upload_id',
                    ],
                ],
                [
                    'name' => 'tag',
                    'span' => 12,
                ],
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_MODIFIED',
                    'fields' => [
                        [
                            'name' => 'date_modified',
                            'required' => false,
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'modified_by_name',
                        ],
                    ],
                ],
                [
                    'name' => 'date_entered_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_ENTERED',
                    'fields' => [
                        [
                            'name' => 'date_entered',
                            'required' => false,
                        ],
                        [
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ],
                        [
                            'name' => 'created_by_name',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
