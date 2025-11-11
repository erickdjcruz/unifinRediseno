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

return [
    'metadata' => [
        'components' => [
            [
                'rows' => [
                    [
                        [
                            'view' => [
                                'type' => 'pipeline-metrics',
                                'label' => 'LBL_PIPELINE_METRICS_DASHLET_NAME',
                                'module' => 'Forecasts',
                            ],
                            'context' => [
                                'module' => 'Forecasts',
                            ],
                            'width' => 12,
                        ],
                    ],
                ],
                'width' => 12,
            ],
        ],
    ],
    'name' => 'LBL_FORECASTS_DASHBOARD',
    'id' => '5d67396c-7b52-11e9-8826-f218983a1c3e',
];
