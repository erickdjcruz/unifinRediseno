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
$viewdefs['KBContents']['mobile']['view']['edit'] = [
    'templateMeta' => [
        'maxColumns' => '1',
        'widths' => [
            [
                'label' => '10',
                'field' => '30',
            ],
        ],
    ],
    'panels' => [
        [
            'fields' => [
                'name',
                'kbdocument_body',
                [
                    'name' => 'attachment_list',
                    'label' => 'LBL_ATTACHMENTS',
                ],
                'tag',
                'category_name',
                'active_rev',
                'is_external',
                'active_date',
                'exp_date',
                'date_entered',
                'date_modified',
            ],
        ],
    ],
];
