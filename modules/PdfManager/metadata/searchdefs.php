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


$searchdefs['PdfManager'] =
    [
        'layout' => [
            'basic_search' => [
                'name' => [
                    'name' => 'name',
                    'default' => true,
                    'width' => '10%',
                ],
                'base_module' => [
                    'type' => 'enum',
                    'default' => true,
                    'studio' => 'visible',
                    'label' => 'LBL_BASE_MODULE',
                    'width' => '10%',
                    'name' => 'base_module',
                ],
                'published' => [
                    'name' => 'published',
                    'default' => true,
                    'width' => '10%',
                ],
                'team_name' => [
                    'type' => 'relate',
                    'link' => true,
                    'label' => 'LBL_TEAMS',
                    'id' => 'TEAM_ID',
                    'width' => '10%',
                    'default' => true,
                    'name' => 'team_name',
                ],
            ],
            'advanced_search' => [],
        ],
        'templateMeta' => [
            'maxColumns' => '3',
            'maxColumnsBasic' => '4',
            'widths' => [
                'label' => '10',
                'field' => '30',
            ],
        ],
    ];
