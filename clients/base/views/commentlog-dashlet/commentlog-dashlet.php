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

$viewdefs['base']['view']['commentlog-dashlet'] = [
    'dashlets' => [
        [
            'label' => 'LBL_DASHLET_COMMENTLOG_NAME',
            'description' => 'LBL_DASHLET_COMMENTLOG_DESCRIPTION',
            'filter' => [
                'view' => 'record',
                'blacklist' => [
                    'module' => [
                        'Emails',
                        'Home',
                        'KBArticles',
                        'KBContentTemplates',
                        'KBDocuments',
                        'ProductCategories',
                        'Metrics',
                    ],
                ],
            ],
            'config' => [],
        ],
    ],
    'fields' => [
        [
            'name' => 'commentlog',
            'type' => 'commentlog',
            'dashlet' => true,
        ],
    ],
];
