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
$viewdefs['Notes']['portal']['view']['record'] = [
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
                    'name' => 'description',
                    'span' => 12,
                    'rows' => 5,
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'attachment_list',
                    'default' => true,
                    'enabled' => true,
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
            ],
        ],
    ],
];
