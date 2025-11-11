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

$viewdefs['Cases']['portal']['view']['tutorial'] = [
    'records' => [
        'version' => 1,
        'intro' => 'LBL_PORTAL_TOUR_RECORDS_INTRO',
        'content' => [
            [
                'text' => 'LBL_PORTAL_TOUR_RECORDS_PAGE',
            ],
            [
                'name' => '.dataTables_filter',
                'text' => 'LBL_PORTAL_TOUR_RECORDS_FILTER',
                'full' => true,
            ],
            [
                'name' => '.dataTables_filter',
                'text' => 'LBL_PORTAL_TOUR_RECORDS_FILTER_EXAMPLE',
                'full' => true,
            ],
            [
                'name' => '.btn-primary[name="create_button"]',
                'text' => 'LBL_PORTAL_TOUR_RECORDS_CREATE',
                'full' => true,
            ],
            [
                'name' => '[data-route="#Cases"]',
                'text' => 'LBL_PORTAL_TOUR_RECORDS_RETURN',
                'full' => true,
            ],
        ],
    ],
];
