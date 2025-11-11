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

$view_config = [
    'req_params' => [
        'print' => ['param_value' => true,
            'config' => [
                'show_header' => true,
                'view_print' => true,
                'show_title' => false,
                'show_subpanels' => false,
                'show_javascript' => true,
                'show_search' => false,],
        ],
        'to_pdf' => ['param_value' => true,
            'config' => [
                'show_all' => false,
            ],
        ],
        'to_csv' => ['param_value' => true,
            'config' => [
                'show_all' => false,
            ],
        ],
    ],
];
