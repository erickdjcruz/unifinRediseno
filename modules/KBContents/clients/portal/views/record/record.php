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
$viewdefs['KBContents']['portal']['view']['record'] = [
    'buttons' => [
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
                    'related_fields' => [
                        'useful',
                        'notuseful',
                        'usefulness_user_vote',
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
                'kbdocument_body' => [
                    'name' => 'kbdocument_body',
                    'type' => 'html',
                    'span' => 12,
                ],
                'attachment_list' => [
                    'name' => 'attachment_list',
                    'label' => 'LBL_ATTACHMENTS',
                    'type' => 'multi-attachments',
                    'link' => 'attachments',
                    'module' => 'Notes',
                    'modulefield' => 'filename',
                    'bLable' => 'LBL_ADD_ATTACHMENT',
                    'bIcon' => 'sicon-attach',
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
                'category_name' => [
                    'name' => 'category_name',
                    'label' => 'LBL_CATEGORY_NAME',
                ],
                'language' => [
                    'name' => 'language',
                    'type' => 'enum-config',
                    'key' => 'languages',
                ],
                'date_entered' => [
                    'name' => 'date_entered',
                ],
                'active_date' => [
                    'name' => 'active_date',
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
