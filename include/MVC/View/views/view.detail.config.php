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
    'actions' => [
        'detailview' => [
            'show_header' => true,
            'view_print' => false,
            'show_title' => true,
            'show_subpanels' => true,
            'show_javascript' => true,
            'show_search' => true,
            'json_output' => false,
        ],
    ],
    'req_params' => [
        'ajax_load' => [
            'param_value' => true,
            'config' => [
                'show_header' => false,
                'view_print' => false,
                'show_title' => true,
                'show_subpanels' => true,
                'show_javascript' => false,
                'show_search' => true,
                'json_output' => true,
            ],
        ],
    ],
];
