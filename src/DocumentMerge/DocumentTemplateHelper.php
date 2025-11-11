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

/**
 * Returns a dictionary of all available modules which can be merged against
 *
 * @return array
 */
function getTargetModules()
{
    $controller = new TabController();
    $tabs = $controller->get_tabs_system();
    $modules = $tabs[0];
    $hiddenModules = $tabs[1];

    $badModules = [
        'Home',
        'Calendar',
        'Forecasts',
        'Reports',
        'ops_Backups',
        'Documents',
        'Campaigns',
        'pmse_Inbox',
        'BusinessCenters',
        'DataPrivacy',
        'Emails',
        'OutboundEmail',
        'ProductTemplates',
        'Shifts',
        'Tags',
        'pmse_Business_Rules',
        'pmse_Emails_Templates',
        'pmse_Project',
        'DocumentTemplates',
        'CloudDrivePaths',
        'DocuSignEnvelopes',
    ];

    $allModules = array_diff_key(array_merge($modules, $hiddenModules), array_flip($badModules));
    array_push($allModules, 'Users');
    $translatedModules = [];

    foreach ($allModules as $module) {
        $translatedModules[$module] = translate($module);
    }

    return $translatedModules;
}
