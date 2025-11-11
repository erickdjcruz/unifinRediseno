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
$viewdefs['DocumentMerges']['base']['layout']['sidebar-merge-widget'] = [
    'css_class' => 'w-96',
    'components' => [
        [
            'view' => [
                'type' => 'merge-widget-header',
                'template' => 'merge-widget-header.sidebar-merge-widget-header',
            ],
            'context' => [
                'module' => 'DocumentMerges',
            ],
        ],
        [
            'view' => [
                'type' => 'merge-widget-list',
                'template' => 'merge-widget-list.sidebar-merge-widget-list',
            ],
            'context' => [
                'module' => 'DocumentMerges',
            ],
        ],
    ],
];
