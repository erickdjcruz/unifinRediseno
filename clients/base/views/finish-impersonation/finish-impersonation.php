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

use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config;

$idpConfig = new Config(\SugarConfig::getInstance());
$isIDMModeEnabled = $idpConfig->isIDMModeEnabled();

$comeBackUrl = '';

if ($idpConfig->isIDMModeEnabled()) {
    $comeBackUrl = $idpConfig->buildCloudConsoleUrl('/');
}

$viewdefs['base']['view']['finish-impersonation'] = [
    'comeBackUrl' => $comeBackUrl,
];
