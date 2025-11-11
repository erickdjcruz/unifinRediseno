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

$viewdefs['<module_name>']['base']['filter']['issue'] = [
    'filters' => [
        [
            'id' => 'open_issues',
            'name' => 'LBL_OPEN_ISSUES',
            'filter_definition' => [
                'status' => [
                    '$not_in' => ['Closed', 'Rejected', 'Duplicate'],
                ],
            ],
            'editable' => false,
        ],
    ],
];
