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

$viewdefs['Forecasts']['base']['layout']['metrics-help'] = [
    'components' => [
        [
            'view' => 'help-header',
        ],
        [
            'view' => [
                'name' => 'metrics-info',
                'css_class' => 'popover-body overflow-y-scroll',
            ],
        ],
        [
            'view' => [
                'name' => 'metrics-help-footer',
                'css_class' => 'px-3.5 border-t border-[--border-color]',
            ],
        ],
    ],
];
