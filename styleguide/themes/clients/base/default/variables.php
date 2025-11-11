<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
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

/**
 * Variables.less
 * Variables to customize the look and feel of Bootstrap
 */

$lessdefs = [

    'colors' => [
        /**
         * Primary Color:
         * 3 pixel line on the navbar
         * - Matches Sugar's @gray30 (default)
         * - Uses Sugar's @gray80 in dark mode
         */
        'BorderColor' => '#e5eaed',

        /**
         * Secondary Color:
         * Color used for navbar background
         * - Matches Sugar's @white (default)
         * - Uses Sugar's @black in dark mode
         */
        'NavigationBar' => '#ffffff',

        /**
         * Primary Button Color:
         * color of the primary button
         * - Matches Sugar's @blue (default)
         * - Uses the same color in dark mode
         */
        'PrimaryButton' => '#0679c8',

        /**
         * Link Color:
         * color of link text
         * - Matches Sugar's @blue (default)
         * - Uses @darkModeBlue in dark mode
         */
        'LinkColor' => '#0679c8',
    ],
];
