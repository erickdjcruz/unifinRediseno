<?php
// WARNING: The contents of this file are auto-generated.
?>
<?php
// Merged from modules/DataPrivacy/Ext/LogicHooks/RemoveUnlinkedRecordsFromErase.php

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
$hook_array['after_relationship_delete'][] = [
    1,
    'unlinkRecordsFromErase',
    'modules/DataPrivacy/DataPrivacyHooks.php',
    'DataPrivacyHooks',
    'unlinkRecordsFromErase',
];

?>
<?php
// Merged from modules/DataPrivacy/Ext/LogicHooks/PerformActivityStreamErasure.php

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
 * Initiate the Activity Stream erasure process if DataPrivacy status transitions to Closed
 */
$hook_array['after_save'][] = [
    1,
    'performActivityStreamErasure',
    'modules/DataPrivacy/DataPrivacyHooks.php',
    'DataPrivacyHooks',
    'performActivityStreamErasure',
];

?>
