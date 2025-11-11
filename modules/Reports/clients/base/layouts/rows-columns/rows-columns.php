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

$viewdefs['Reports']['base']['layout']['rows-columns'] = [
    'css_class' => 'paginated-flex-list notLoaded',
    'components' => [
        [
            'view' => [
                'name' => 'rows-columns',
            ],
            'primary' => true,
        ],
        [
            'view' => [
                'name' => 'report-table-pagination',
                'css_class' => 'flex-table-pagination bg-[--primary-content-background] relative w-full z-30',
            ],
        ],
    ],
];
