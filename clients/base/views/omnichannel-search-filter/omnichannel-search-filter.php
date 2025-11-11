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

$viewdefs['base']['view']['omnichannel-search-filter'] = [
    'facets' => [
        [
            'facet_id' => 'assigned_user_id',
            'label' => 'LBL_FACET_ASSIGNED_TO_ME',
        ],
        [
            'facet_id' => 'favorite_link',
            'label' => 'LBL_FACET_MY_FAVORITES',
        ],
        [
            'facet_id' => 'created_by',
            'label' => 'LBL_FACET_CREATED_BY_ME',
        ],
        [
            'facet_id' => 'modified_user_id',
            'label' => 'LBL_FACET_MODIFIED_BY_ME',
        ],
    ],
];
