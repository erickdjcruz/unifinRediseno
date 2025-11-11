<?php
// WARNING: The contents of this file are auto-generated.
?>
<?php
// Merged from modules/ForecastWorksheets/Ext/LogicHooks/CheckRelatedName.php

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
 * Define the before_save hook that will check to make sure that opportunity_name and account_name and product_name
 * are all empty before saving if their related_ids are empty
 */
$hook_array['before_save'][] = [
    1,
    'checkRelatedName',
    'modules/ForecastWorksheets/ForecastWorksheetHooks.php',
    'ForecastWorksheetHooks',
    'checkRelatedName',
];

/**
 * Handle removing the names if they id's are removed from the forecast worksheets since how the relationships are
 * updated now it doesn't resave the bean
 */
$hook_array['after_relationship_delete'][] = [
    1,
    'afterRelationshipDelete',
    'modules/ForecastWorksheets/ForecastWorksheetHooks.php',
    'ForecastWorksheetHooks',
    'afterRelationshipDelete',
];

?>
<?php
// Merged from modules/ForecastWorksheets/Ext/LogicHooks/fixDateModified.php

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
 * Reset the date_modified so we have the seconds on it
 */
$hook_array['process_record'][] = [
    1,
    'fixDateModified',
    'modules/ForecastWorksheets/ForecastWorksheetHooks.php',
    'ForecastWorksheetHooks',
    'fixDateModified',
];

$hook_array['after_retrieve'][] = [
    1,
    'fixDateModified',
    'modules/ForecastWorksheets/ForecastWorksheetHooks.php',
    'ForecastWorksheetHooks',
    'fixDateModified',
];

?>
<?php
// Merged from modules/ForecastWorksheets/Ext/LogicHooks/CommitStageNotification.php

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
 * Define the before_save hook that will notify a manager that if a "included" worksheet row gets not included
 */
$hook_array['before_save'][] = [
    1,
    'checkCommitStage',
    'modules/ForecastWorksheets/ForecastWorksheetHooks.php',
    'ForecastWorksheetHooks',
    'managerNotifyCommitStage',
];

?>
