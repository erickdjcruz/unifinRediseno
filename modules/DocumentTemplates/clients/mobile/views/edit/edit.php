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

$viewdefs['DocumentTemplates']['mobile']['view']['edit'] = [
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
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'filename',
                    'label' => 'LBL_FILENAME',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'template_module',
                    'label' => 'LBL_TEMPLATE_MODULE',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'use_revisions',
                    'label' => 'LBL_USE_REVISIONS',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'label_merging',
                    'label' => 'LBL_LABEL_MERGING',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'prefix',
                    'label' => 'LBL_PREFIX',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'postfix',
                    'label' => 'LBL_POSTFIX',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO_NAME',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'team_name',
                    'label' => 'LBL_LIST_TEAM',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'date_entered',
                    'label' => 'LBL_DATE_ENTERED',
                    'default' => true,
                    'enabled' => true,
                ],
                [
                    'name' => 'date_modified',
                    'default' => true,
                    'enabled' => true,
                ],
            ],
        ],
    ],
];
