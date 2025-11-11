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
$viewdefs['Emails']['base']['view']['compose-email'] = [
    'template' => 'record',
    'buttons' => [
        [
            'name' => 'cancel_button',
            'type' => 'button',
            'label' => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
            'events' => [
                'click' => 'button:cancel_button:click',
            ],
        ],
        [
            'name' => 'save_button',
            'type' => 'button',
            'label' => 'LBL_SAVE_AS_DRAFT_BUTTON_LABEL',
            'events' => [
                'click' => 'button:save_button:click',
            ],
        ],
        [
            'name' => 'send_button',
            'type' => 'button',
            'label' => 'LBL_SEND_BUTTON_LABEL',
            'primary' => true,
            'events' => [
                'click' => 'button:send_button:click',
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
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'recipients',
                    'type' => 'recipients-fieldset',
                    'css_class' => 'email-recipients',
                    'dismiss_label' => true,
                    'fields' => [
                        [
                            'name' => 'outbound_email_id',
                            'type' => 'outbound-email',
                            'label' => 'LBL_FROM',
                            'span' => 12,
                            'css_class' => 'inherit-width',
                            'searchBarThreshold' => -1,
                        ],
                        [
                            'name' => 'to_collection',
                            'type' => 'email-recipients',
                            'label' => 'LBL_TO_ADDRS',
                            'span' => 12,
                            'max_num' => -1,
                            'fields' => [
                                'email_address_id',
                                'email_address',
                                'parent_type',
                                'parent_id',
                                'parent_name',
                                'invalid_email',
                                'opt_out',
                            ],
                        ],
                        [
                            'name' => 'cc_collection',
                            'type' => 'email-recipients',
                            'label' => 'LBL_CC',
                            'span' => 12,
                            'max_num' => -1,
                            'fields' => [
                                'email_address_id',
                                'email_address',
                                'parent_type',
                                'parent_id',
                                'parent_name',
                                'invalid_email',
                                'opt_out',
                            ],
                        ],
                        [
                            'name' => 'bcc_collection',
                            'type' => 'email-recipients',
                            'label' => 'LBL_BCC',
                            'span' => 12,
                            'max_num' => -1,
                            'fields' => [
                                'email_address_id',
                                'email_address',
                                'parent_type',
                                'parent_id',
                                'parent_name',
                                'invalid_email',
                                'opt_out',
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'name',
                    'label' => 'LBL_SUBJECT',
                    'dismiss_label' => true,
                    'placeholder' => 'LBL_SUBJECT',
                    'span' => 12,
                    'css_class' => 'ellipsis_inline',
                    'related_fields' => [
                        'state',
                    ],
                ],
                [
                    'name' => 'description_html',
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
                    'name' => 'attachments_collection',
                    'type' => 'email-attachments',
                    'dismiss_label' => true,
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
            ],
        ],
        [
            'name' => 'panel_hidden',
            'hide' => true,
            'columns' => 1,
            'labelsOnTop' => true,
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
                [
                    'name' => 'tag',
                    'span' => '12',
                ],
            ],
        ],
    ],
];
