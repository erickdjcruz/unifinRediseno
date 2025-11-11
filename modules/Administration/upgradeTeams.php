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


//Create User Teams
$globalteam = BeanFactory::getBean('Teams', '1');
if (isset($globalteam->name)) {
    echo 'Global ' . $mod_strings['LBL_UPGRADE_TEAM_EXISTS'] . '<br>';
    if ($globalteam->deleted) {
        $globalteam->mark_undeleted($globalteam->id);
    }
} else {
    $globalteam->create_team('Global', $mod_strings['LBL_GLOBAL_TEAM_DESC'], $globalteam->global_team);
}

/** @var \Sugarcrm\Sugarcrm\Dbal\Connection $connection */
$connection = $GLOBALS['db']->getConnection();
$sql = "SELECT id, user_name FROM users WHERE default_team != '' AND default_team IS NOT NULL AND user_name NOT IN (?, ?)";
$result = $connection->executeQuery($sql, [SugarSNIP::SNIP_USER, 'SugarCustomerSupportPortalUser']);

$team = BeanFactory::newBean('Teams');
$user = BeanFactory::newBean('Users');
foreach ($result->iterateAssociative() as $row) {
    $row2 = $connection->fetchAssociative('SELECT id, name FROM teams WHERE associated_user_id = ?', [$row['id']]);
    if (false === $row2) {
        $user->retrieve($row['id']);
        $team->new_user_created($user);
        // BUG 10339: do not display messages for upgrade wizard
        if (!isset($_REQUEST['upgradeWizard'])) {
            printf('%s %s<br>', htmlspecialchars($mod_strings['LBL_UPGRADE_TEAM_CREATE'], ENT_COMPAT), htmlspecialchars($row['user_name'], ENT_COMPAT));
        }
    } else {
        printf('%s %s<br>', htmlspecialchars($row2['name'], ENT_COMPAT), htmlspecialchars($mod_strings['LBL_UPGRADE_TEAM_EXISTS'], ENT_COMPAT));
    }

    $globalteam->add_user_to_team($row['id']);
}

echo '<br>' . htmlspecialchars($mod_strings['LBL_DONE'], ENT_COMPAT);
