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
/*********************************************************************************
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$tracker_config =
    [
        'tracker' => [
            'bean' => 'Tracker',
            'name' => 'Tracker',
            'metadata' => 'modules/Trackers/vardefs.php',
            'store' => [
                0 => 'DatabaseStore',
            ],
        ],
        'tracker_sessions' => [
            'bean' => 'TrackerSession',
            'name' => 'tracker_sessions',
            'metadata' => 'modules/Trackers/tracker_sessionsMetaData.php',
            'store' => [
                0 => 'TrackerSessionsDatabaseStore',
            ],
        ],
        'tracker_perf' => [
            'bean' => 'TrackerPerf',
            'name' => 'tracker_perf',
            'metadata' => 'modules/Trackers/tracker_perfMetaData.php',
            'store' => [
                0 => 'DatabaseStore',
            ],
        ],
        'tracker_queries' => [
            'bean' => 'TrackerQuery',
            'name' => 'tracker_queries',
            'metadata' => 'modules/Trackers/tracker_queriesMetaData.php',
            'store' => [
                0 => 'TrackerQueriesDatabaseStore',
            ],
        ],
        'tracker_tracker_queries' => [
            'name' => 'tracker_tracker_queries',
            'metadata' => 'modules/Trackers/tracker_tracker_queriesMetaData.php',
            'store' => [
                0 => 'DatabaseStore',
            ],
        ],
    ];
