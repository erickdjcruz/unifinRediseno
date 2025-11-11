<?php

//FILE SUGARCRM flav=ent
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
/*********************************************************************************
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$viewdefs['portal']['view']['tutorial'] = [
    'record' => [
        'version' => 1,
        'content' => [
            [
                'text' => 'LBL_PORTAL_TOUR_RECORD_DETAILS',
                'full' => true,
            ],
            [
                'name' => '.block h4',
                'text' => 'LBL_PORTAL_TOUR_RECORD_NOTES',
                'full' => true,
            ],
            [
                'name' => 'a.addNote',
                'text' => 'LBL_PORTAL_TOUR_RECORD_ADD_NOTE',
                'full' => true,
            ],
            [
                'name' => 'i.sicon-preview',
                'text' => 'LBL_PORTAL_TOUR_RECORD_VIEW_NOTE',
                'full' => true,
            ],
        ],
    ],
    'dashboard' => [
        'version' => 1,
        'intro' => 'LBL_PORTAL_TOUR_RECORDS_INTRO',
        'content' => [
            [
                'text' => 'LBL_PORTAL_TOUR_RECORDS_PAGE',
            ],
            [
                'name' => '[data-route="#Cases"]',
                'text' => 'LBL_PORTAL_TOUR_RECORDS_CASES',
                'full' => true,
            ],
            [
                'name' => '[data-route="#Bugs"]',
                'text' => 'LBL_PORTAL_TOUR_RECORDS_BUGS',
                'full' => true,
            ],
            [
                'name' => 'input.search-query',
                'text' => 'LBL_PORTAL_TOUR_RECORDS_GLOBAL_SEARCH',
                'full' => true,
            ],
            [
                'name' => 'li#userActions',
                'text' => 'LBL_PORTAL_TOUR_RECORDS_USER',
                'full' => true,
            ],
            [
                'name' => 'li#createList',
                'text' => 'LBL_PORTAL_TOUR_RECORDS_QUICK_CREATE',
                'full' => true,
            ],
            [
                'name' => '.dataTables_filter',
                'text' => 'LBL_PORTAL_TOUR_RECORDS_SEARCH',
                'full' => true,
            ],
            [
                'name' => '[href=#Home]',
                'text' => 'LBL_PORTAL_TOUR_RECORDS_RETURN',
                'full' => true,
            ],
        ],
    ],
];
