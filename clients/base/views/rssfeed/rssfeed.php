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

$viewdefs['base']['view']['rssfeed'] = [
    'dashlets' => [
        [
            'label' => 'LBL_RSS_FEED_DASHLET',
            'description' => 'LBL_RSS_FEED_DASHLET_DESCRIPTION',
            'config' => [
                'limit' => 5,
                'auto_refresh' => 0,
            ],
            'preview' => [
                'limit' => 5,
                'auto_refresh' => 0,
                'feed_url' => 'http://blog.sugarcrm.com/feed/',
            ],
            'filter' => [
                'blacklist' => [
                    'module' => 'Administration',
                ],
            ],
        ],
    ],
    'panels' => [
        [
            'name' => 'panel_body',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => [
                [
                    'name' => 'feed_url',
                    'label' => 'LBL_RSS_FEED_URL',
                    'type' => 'text',
                    'span' => 12,
                    'required' => true,
                ],
                [
                    'name' => 'limit',
                    'label' => 'LBL_RSS_FEED_ENTRIES_COUNT',
                    'type' => 'enum',
                    'options' => 'tasks_limit_options',
                ],
                [
                    'name' => 'auto_refresh',
                    'label' => 'LBL_DASHLET_REFRESH_LABEL',
                    'type' => 'enum',
                    'options' => 'sugar7_dashlet_reports_auto_refresh_options',
                ],
            ],
        ],
    ],
];
