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
$viewdefs['DocumentRevisions']['base']['view']['preview'] = [
    'templateMeta' => [
        'maxColumns' => 1,
    ],
    'panels' => [
        [
            'name' => 'panel_header',
            'fields' => [
                [
                    'name' => 'picture',
                    'type' => 'avatar',
                    'size' => 'large',
                    'dismiss_label' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'document_name',
                    'readonly' => true,
                ],
            ],
        ],
        [
            'name' => 'panel_body',
            'label' => 'LBL_RECORD_BODY',
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'filename',
                    'readonly' => true,
                ],
                [
                    'name' => 'revision',
                    'readonly' => true,
                ],
                [
                    'name' => 'doc_type',
                    'readonly' => true,
                ],
                [
                    'name' => 'change_log',
                    'readonly' => true,
                ],
            ],
        ],
        [
            'name' => 'panel_hidden',
            'label' => 'LBL_RECORD_SHOWMORE',
            'hide' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'date_modified_by',
                    'readonly' => true,
                    'inline' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_ENTERED',
                    'fields' => [
                        [
                            'name' => 'date_entered',
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
