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

global $sugar_version;
global $sugar_flavor;
global $current_language;

$moduleName = 'PortalUser';

$viewdefs['portal']['view']['profileactions'] = [
    [
        'route' => '#profile',
        'label' => 'LBL_PROFILE',
        'css_class' => 'profileactions-profile',
        'acl_action' => 'view',
        'icon' => 'sicon-user',
    ],
    [
        'route' => get_help_url($sugar_flavor, $sugar_version, $current_language, $moduleName),
        'label' => 'LBL_HELP',
        'icon' => 'sicon-help',
        'openwindow' => true,
    ],
    [
        'route' => '#logout/?clear=1',
        'label' => 'LBL_LOGOUT',
        'icon' => 'sicon-logout',
    ],
];
