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
$viewdefs['Documents']['base']['view']['resolve-conflicts-list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'document_name',
                    'label' => 'LBL_LIST_DOCUMENT_NAME',
                    'enabled' => true,
                    'default' => true,
                    'link' => true,
                    'type' => 'name',
                ],
                [
                    'name' => 'filename',
                    'label' => 'LBL_LIST_FILENAME',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'category_id',
                    'label' => 'LBL_LIST_CATEGORY',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'doc_type',
                    'label' => 'LBL_LIST_DOC_TYPE',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'status_id',
                    'label' => 'LBL_LIST_STATUS',
                    'enabled' => true,
                    'default' => false,
                ],
                [
                    'name' => 'active_date',
                    'label' => 'LBL_LIST_ACTIVE_DATE',
                    'enabled' => true,
                    'default' => false,
                ],
            ],
        ],
    ],
];
