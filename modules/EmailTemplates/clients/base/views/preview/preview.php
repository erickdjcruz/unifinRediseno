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
$viewdefs['EmailTemplates']['base']['view']['preview'] = [
    'templateMeta' => [
        'maxColumns' => 1,
        'useTabs' => false,
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
            'newTab' => false,
            'panelDefault' => 'expanded',
            'fields' => [
                'type',
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
                'description',
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
                'subject',
                'team_name',
                [
                    'name' => 'text_only',
                ],
                'assigned_user_name',
                [
                    'name' => 'body_set',
                    'type' => 'fieldset',
                    'label' => 'LBL_BODY',
                    'readonly' => true,
                    'fields' => [
                        [
                            'name' => 'body_html',
                            'type' => 'htmleditable_tinymce',
                            'label' => 'LBL_BODY',
                            'readonly' => true,
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
                    'rows' => 11,
                    'css_class' => 'collapsed-plain-text',
                    'readonly' => true,
                ],
                [
                    'name' => 'attachments_collection',
                    'type' => 'email-attachments',
                    'label' => 'LBL_ATTACHMENTS',
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
                ],
            ],
        ],
    ],
];
