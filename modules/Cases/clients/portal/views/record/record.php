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

$viewdefs['Cases']['portal']['view']['record'] = [
    'buttons' => [
        [
            'type' => 'button',
            'name' => 'request_close_button',
            'label' => 'LBL_REQUEST_CLOSE_LABEL',
            'css_class' => 'btn btn-primary hidden',
            'tooltip' => 'LBL_REQUEST_CLOSE_TOOLTIP',
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
                'name',
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'columns' => 2,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'case_number',
                    'span' => 12,
                ],
                [
                    'name' => 'description',
                    'span' => 12,
                ],
                [
                    'name' => 'status',
                    'readonly' => true,
                ],
                'priority',
                [
                    'name' => 'type',
                    'span' => 12,
                ],
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
                [
                    'name' => 'date_entered_by',
                    'readonly' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_ENTERED',
                    'fields' => [
                        [
                            'name' => 'date_entered',
                        ],
                    ],
                ],
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_MODIFIED',
                    'fields' => [
                        [
                            'name' => 'date_modified',
                        ],
                    ],
                ],
                [
                    'name' => 'request_close',
                    'readonly' => true,
                    'label' => 'LBL_REQUEST_CLOSE',
                ],
                [
                    'name' => 'request_close_date',
                    'readonly' => true,
                    'label' => 'LBL_REQUEST_CLOSE_DATE',
                ],
            ],
        ],
    ],
    'dependencies' => [
        [
            'hooks' => ['all'],
            'trigger' => 'true',
            'triggerFields' => ['request_close', 'request_close_date'],
            'onload' => true,
            'actions' => [
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'request_close',
                        'value' => 'equal($request_close, "1")',
                    ],
                ],
                [
                    'action' => 'SetVisibility',
                    'params' => [
                        'target' => 'request_close_date',
                        'value' => 'equal($request_close, "1")',
                    ],
                ],
            ],
        ],
    ],
];
