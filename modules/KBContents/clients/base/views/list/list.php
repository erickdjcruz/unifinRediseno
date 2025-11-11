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
$viewdefs['KBContents']['base']['view']['list'] = [
    'panels' => [
        [
            'label' => 'LBL_PANEL_1',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                    'related_fields' => [
                        'kbdocument_id',
                        'kbarticle_id',
                    ],
                ],
                [
                    'name' => 'language',
                    'label' => 'LBL_LANG',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                    'type' => 'enum-config',
                    'key' => 'languages',
                ],
                [
                    'name' => 'revision',
                    'label' => 'LBL_REVISION',
                    'enabled' => true,
                    'default' => false,
                    'readonly' => true,
                ],
                [
                    'name' => 'status',
                    'label' => 'LBL_STATUS',
                    'enabled' => true,
                    'default' => true,
                    'type' => 'status',
                    'related_fields' => [
                        'active_date',
                        'exp_date',
                    ],
                ],
                [
                    'name' => 'category_name',
                    'label' => 'LBL_CATEGORY_NAME',
                    'enabled' => true,
                    'default' => true,
                    'css_class' => 'overflow-visible',
                    'initial_filter' => 'by_category',
                    'initial_filter_label' => 'LBL_FILTER_CREATE_NEW',
                    'filter_relate' => [
                        'category_id' => 'category_id',
                    ],
                ],
                [
                    'name' => 'viewcount',
                    'label' => 'LBL_VIEWED_COUNT',
                    'enabled' => true,
                    'default' => true,
                ],
                [
                    'name' => 'date_entered',
                    'label' => 'LBL_DATE_ENTERED',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                ],
                [
                    'name' => 'kbsapprover_name',
                    'label' => 'LBL_KBSAPPROVER',
                    'default' => false,
                    'enabled' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO',
                    'default' => false,
                    'enabled' => true,
                ],
            ],
        ],
    ],
    'orderBy' => [
        'field' => 'date_modified',
        'direction' => 'desc',
    ],
];
