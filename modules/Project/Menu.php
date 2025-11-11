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
 * Side-bar menu for Project
 */
global $current_user;
global $mod_strings, $app_strings;
$module_menu = [];

// Each index of module_menu must be an array of:
// the link url, display text for the link, and the icon name.

// Create Project
if (ACLController::checkAccess('Project', 'edit', true)) {
    $module_menu[] = [
        'index.php?module=Project&action=EditView&return_module=Project&return_action=DetailView',
        $mod_strings['LNK_NEW_PROJECT'] ?? '',
        'CreateProject',
    ];
}

// Create Project Template
if (ACLController::checkAccess('Project', 'edit', true)) {
    $module_menu[] = [
        'index.php?module=Project&action=ProjectTemplatesEditView&return_module=Project&return_action=ProjectTemplatesDetailView',
        $mod_strings['LNK_NEW_PROJECT_TEMPLATES'] ?? '',
        'CreateProjectTemplate',
    ];
}

// Project List
if (ACLController::checkAccess('Project', 'list', true)) {
    $module_menu[] = [
        'index.php?module=Project&action=index',
        $mod_strings['LNK_PROJECT_LIST'] ?? '',
        'Project',
    ];
}

// Project Templates
if (ACLController::checkAccess('Project', 'list', true)) {
    $module_menu[] = [
        'index.php?module=Project&action=ProjectTemplatesListView',
        $mod_strings['LNK_PROJECT_TEMPLATES_LIST'] ?? '',
        'ProjectTemplate',
    ];
}

// Project Tasks
if (ACLController::checkAccess('ProjectTask', 'list', true)) {
    $module_menu[] = [
        'index.php?module=ProjectTask&action=index',
        $mod_strings['LNK_PROJECT_TASK_LIST'] ?? '',
        'ProjectTask',
    ];
}

if (ACLController::checkAccess('Project', 'list', true)) {
    $module_menu[] = [
        'index.php?module=Project&action=Dashboard&return_module=Project&return_action=DetailView',
        $mod_strings['LNK_PROJECT_DASHBOARD'] ?? '',
        'Project',
    ];
}
