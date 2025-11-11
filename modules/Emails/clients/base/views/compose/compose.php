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
$viewdefs['Emails']['base']['view']['compose'] = [
    'template' => 'record',
    'buttons' => [
        [
            'type' => 'button',
            'name' => 'cancel_button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
            'events' => [
                'click' => 'button:cancel_button:click',
            ],
        ],
        [
            'type' => 'actiondropdown',
            'name' => 'main_dropdown',
            'primary' => true,
            'buttons' => [
                [
                    'name' => 'send_button',
                    'type' => 'rowaction',
                    'label' => 'LBL_SEND_BUTTON_LABEL',
                    'events' => [
                        'click' => 'button:send_button:click',
                    ],
                ],
                [
                    'name' => 'draft_button',
                    'type' => 'rowaction',
                    'label' => 'LBL_SAVE_AS_DRAFT_BUTTON_LABEL',
                    'events' => [
                        'click' => 'button:draft_button:click',
                    ],
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
            'name' => 'panel_body',
            'label' => 'LBL_PANEL_2',
            'columns' => 1,
            'labels' => true,
            'labelsOnTop' => false,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'email_config',
                    'label' => 'LBL_FROM',
                    'type' => 'sender',
                    'span' => 12,
                    'css_class' => 'inherit-width',
                    'endpoint' => [
                        'module' => 'OutboundEmailConfiguration',
                        'action' => 'list',
                    ],
                ],
                [
                    'name' => 'to_addresses',
                    'type' => 'recipients',
                    'label' => 'LBL_TO_ADDRS',
                    'span' => 12,
                    'required' => true,
                ],
                [
                    'name' => 'cc_addresses',
                    'type' => 'recipients',
                    'label' => 'LBL_CC',
                    'span' => 12,
                ],
                [
                    'name' => 'bcc_addresses',
                    'type' => 'recipients',
                    'label' => 'LBL_BCC',
                    'span' => 12,
                ],
                [
                    'name' => 'name',
                    'label' => 'LBL_SUBJECT',
                    'span' => 12,
                ],
                [
                    'name' => 'description_html',
                    'type' => 'htmleditable_tinymce',
                    'dismiss_label' => true,
                    'span' => 12,
                    'tinyConfig' => [
                        'toolbar' => 'code | bold italic underline strikethrough | alignleft aligncenter alignright ' .
                            'alignjustify | forecolor backcolor | fontfamily fontsize blocks | ' .
                            'cut copy paste pastetext | search searchreplace | bullist numlist | ' .
                            'outdent indent | ltr rtl | undo redo | link unlink anchor image | subscript ' .
                            'superscript | charmap | table | hr removeformat | insertdatetime | ' .
                            'sugarattachment sugarsignature sugartemplate',
                    ],
                ],
                [
                    'name' => 'attachments',
                    'type' => 'attachments',
                    'dismiss_label' => true,
                ],
            ],
        ],
        [
            'name' => 'panel_hidden',
            'hide' => true,
            'columns' => 1,
            'labelsOnTop' => false,
            'placeholders' => true,
            'fields' => [
                [
                    'type' => 'teamset',
                    'name' => 'team_name',
                    'span' => 12,
                ],
                [
                    'name' => 'parent_name',
                    'span' => 12,
                ],
            ],
        ],
    ],
];
