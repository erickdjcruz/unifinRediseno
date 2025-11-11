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

$viewdefs['Reports']['base']['layout']['drillthrough-pane'] = [
    'name' => 'drillthrough-pane',
    'css_class' => 'dashboard drillthrough-pane pb-4',
    'components' => [
        [
            'view' => [
                'name' => 'drillthrough-pane-headerpane',
                'template' => 'headerpane',
                'buttons' => [
                    [
                        'type' => 'button',
                        'icon' => 'sicon-refresh',
                        'css_class' => 'btn mr-4',
                        'tooltip' => 'LBL_REFRESH_LIST_AND_CHART',
                        'events' => [
                            'click' => 'click:refresh_list_chart',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
