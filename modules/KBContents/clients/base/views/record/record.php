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
$viewdefs['KBContents']['base']['view']['record'] = [
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
                    'type' => 'divider',
                ],
                [
                    'type' => 'shareaction',
                    'name' => 'share',
                    'label' => 'LBL_RECORD_SHARE_BUTTON',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'pdfaction',
                    'name' => 'download-pdf',
                    'label' => 'LBL_PDF_VIEW',
                    'action' => 'download',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'pdfaction',
                    'name' => 'email-pdf',
                    'label' => 'LBL_PDF_EMAIL',
                    'action' => 'email',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:duplicate_button:click',
                    'name' => 'duplicate_button',
                    'label' => 'LBL_DUPLICATE_BUTTON_LABEL',
                    'acl_module' => 'KBContents',
                    'acl_action' => 'create',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:audit_button:click',
                    'name' => 'audit_button',
                    'label' => 'LNK_VIEW_CHANGE_LOG',
                    'acl_action' => 'view',
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'type' => 'rowaction',
                    'event' => 'button:delete_button:click',
                    'name' => 'delete_button',
                    'label' => 'LBL_DELETE_BUTTON_LABEL',
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
            'label' => 'LBL_PANEL_HEADER',
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
                    'related_fields' => [
                        'useful',
                        'notuseful',
                        'usefulness_user_vote',
                        'kbdocument_id',
                    ],
                ],
                [
                    'name' => 'favorite',
                    'label' => 'LBL_FAVORITE',
                    'type' => 'favorite',
                    'dismiss_label' => true,
                ],
                [
                    'name' => 'follow',
                    'label' => 'LBL_FOLLOW',
                    'type' => 'follow',
                    'readonly' => true,
                    'dismiss_label' => true,
                ],
                'status' => [
                    'name' => 'status',
                    'type' => 'status',
                    'enum_width' => 'auto',
                    'dropdown_width' => 'auto',
                    'dropdown_class' => 'select2-menu-only',
                    'container_class' => 'select2-menu-only',
                    'related_fields' => [
                        'active_date',
                        'exp_date',
                    ],
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'kbdocument_body',
                    'type' => 'htmleditable_tinymce',
                    'dismiss_label' => false,
                    'span' => 12,
                    'fieldSelector' => 'kbdocument_body',
                    'tinyConfig' => [
                        'toolbar' => 'code | bold italic underline strikethrough | alignleft aligncenter alignright ' .
                            'alignjustify | forecolor backcolor | fontfamily fontsize blocks | ' .
                            'cut copy paste pastetext | search searchreplace | bullist numlist | ' .
                            'outdent indent | ltr rtl | undo redo | link unlink anchor image | subscript ' .
                            'superscript | charmap | table | hr removeformat | insertdatetime | ' .
                            'kbtemplate',
                    ],
                ],
                [
                    'name' => 'tag',
                    'span' => 12,
                ],
            ],
        ],
        [
            'name' => 'panel_hidden',
            'label' => 'LBL_SHOW_MORE',
            'hide' => true,
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'attachment_list',
                    'label' => 'LBL_ATTACHMENTS',
                    'type' => 'multi-attachments',
                    'link' => 'attachments',
                    'module' => 'Notes',
                    'modulefield' => 'filename',
                    'bLabel' => 'LBL_ADD_ATTACHMENT',
                    'span' => 12,
                    'max_num' => -1,
                    'related_fields' => [
                        'filename',
                        'file_mime_type',
                    ],
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
                'language' => [
                    'name' => 'language',
                    'type' => 'enum-config',
                    'key' => 'languages',
                    'readonly' => false,
                ],
                'revision' => [
                    'name' => 'revision',
                    'readonly' => true,
                ],
                'category_name' => [
                    'name' => 'category_name',
                    'label' => 'LBL_CATEGORY_NAME',
                    'initial_filter' => 'by_category',
                    'initial_filter_label' => 'LBL_FILTER_CREATE_NEW',
                    'filter_relate' => [
                        'category_id' => 'category_id',
                    ],
                ],
                'active_rev' => [
                    'name' => 'active_rev',
                    'type' => 'bool',
                ],
                'viewcount' => [
                    'name' => 'viewcount',
                ],
                'team_name' => [
                    'name' => 'team_name',
                ],
                'assigned_user_name' => [
                    'name' => 'assigned_user_name',
                ],
                'is_external' => [
                    'name' => 'is_external',
                    'type' => 'bool',
                ],
                'date_entered' => [
                    'name' => 'date_entered',
                ],
                'created_by_name' => [
                    'name' => 'created_by_name',
                ],
                'date_modified' => [
                    'name' => 'date_modified',
                ],
                'kbsapprover_name' => [
                    'name' => 'kbsapprover_name',
                ],
                'active_date' => [
                    'name' => 'active_date',
                ],
                'kbscase_name' => [
                    'name' => 'kbscase_name',
                ],
                'exp_date' => [
                    'name' => 'exp_date',
                ],
            ],
        ],
    ],
    'moreLessInlineFields' => [
        'usefulness' => [
            'name' => 'usefulness',
            'type' => 'usefulness',
            'span' => 6,
            'cell_css_class' => 'pull-right usefulness',
            'readonly' => true,
            'fields' => [
                'usefulness_user_vote',
                'useful',
                'notuseful',
            ],
        ],
    ],
];
