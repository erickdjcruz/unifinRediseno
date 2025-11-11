<?php
// WARNING: The contents of this file are auto-generated.
?>
<?php
// Merged from modules/Opportunities/Ext/LogicHooks/SyncBestWorstWithLikely.php

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
 * Before we save an opportunity, if the sales stage is in one of the Forecasts Closed States, then we need to
 * make sure we sync the values
 */
$hook_array['before_save'][] = [
    1,
    'setBestWorstEqualToLikelyAmount',
    'modules/Forecasts/ForecastHooks.php',
    'ForecastHooks',
    'setBestWorstEqualToLikelyAmount',
];

?>
<?php
// Merged from modules/Opportunities/Ext/LogicHooks/SetCommitStageForClosedWon.php

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
 * Before we save an opp, check if we need to set the commit stage
 */
$hook_array['before_save'][] = [
    10,
    'beforeSaveIncludedCheck',
    'modules/Opportunities/OpportunityHooks.php',
    'OpportunityHooks',
    'beforeSaveIncludedCheck',
];

?>
<?php
// Merged from modules/Opportunities/Ext/LogicHooks/FixWorksheetAccountAssignment.php

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
 * After we update the relationship of an opportunity, we need to resave so the worksheet gets updated as well.
 */
$hook_array['after_relationship_add'][] = [
    10,
    'fixWorksheetAccountAssignment',
    'modules/Opportunities/OpportunityHooks.php',
    'OpportunityHooks',
    'fixWorksheetAccountAssignment',
];

?>
<?php
// Merged from modules/Opportunities/Ext/LogicHooks/GenerateRenewalOpportunity.php

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
 * Define the after_save hook that will generate a renewal opportunity
 * when an opportunity containing services is closed won
 */
$hook_array['after_save'][] = [
    1,
    'generateRenewalOpportunity',
    'modules/Opportunities/OpportunityHooks.php',
    'OpportunityHooks',
    'generateRenewalOpportunity',
];

?>
<?php
// Merged from modules/Opportunities/Ext/LogicHooks/QueuePurchaseGeneration.php


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
 * Define the after_save hook that will queue generating purchases from an
 * Opportunity's RLIs when the Opportunity is closed won.
 */
$hook_array['after_save'][] = [
    2,
    'queueRLItoPurchaseJob',
    'modules/Opportunities/OpportunityHooks.php',
    'OpportunityHooks',
    'queueRLItoPurchaseJob',
];

?>
<?php
// Merged from modules/Opportunities/Ext/LogicHooks/DeleteOpportunity.php

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
 * Define the after_delete hook that will resave the related worksheet if forecasts is setup
 */
$hook_array['after_delete'][] = [
    1,
    'saveworksheet',
    'modules/Opportunities/OpportunityHooks.php',
    'OpportunityHooks',
    'saveWorksheet',
];

/**
 * Before we delete an Opp, delete all the RLI's
 */
$hook_array['before_delete'][] = [
    1,
    'deleteRLI',
    'modules/Opportunities/OpportunityHooks.php',
    'OpportunityHooks',
    'deleteOpportunityRevenueLineItems',
];

?>
<?php
// Merged from modules/Opportunities/Ext/LogicHooks/OpportunitySyncWorksheet.php

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
 * Define the after_save hook that will sync the opportunity the related worksheet if forecasts is setup
 */
$hook_array['after_save'][] = [
    1,
    'saveworksheet',
    'modules/Opportunities/OpportunityHooks.php',
    'OpportunityHooks',
    'saveWorksheet',
];

?>
<?php
// Merged from modules/Opportunities/Ext/LogicHooks/SetForecastCommitStage.php

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
 * Before we save an opportunity, check if we need to set the commit stage
 */
$hook_array['before_save'][] = [
    1,
    'setCommitStageIfEmpty',
    'modules/Forecasts/ForecastHooks.php',
    'ForecastHooks',
    'setCommitStageIfEmpty',
];

?>
<?php
// Merged from modules/Opportunities/Ext/LogicHooks/OpportunitySalesStatus.php

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
 * Define the before_save hook that will set the Opportunity Sales Status in Ent Only
 */
$hook_array['before_save'][] = [
    1,
    'setSalesStatus',
    'modules/Opportunities/OpportunityHooks.php',
    'OpportunityHooks',
    'setSalesStatus',
    'Before Opportunity Save',
];

?>
