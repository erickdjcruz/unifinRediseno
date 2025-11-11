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
$viewdefs['Reports']['base']['layout']['report-chart'] = [
    'components' => [
        [
            'view' => [
                'name' => 'report-chart',
                'css_class' => 'report-chart-container multi-line-list-view bg-[--dashlet-background] overflow-auto',
            ],
        ],
        [
            'view' => [
                'name' => 'report-panel-footer',
            ],
        ],
    ],
    'css_class' => 'flex-records-layout h-[calc(100%-55px)]',
];
