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

use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;

global $current_user;

$productCodes = $current_user->getProductCodes();
$productCodes = urlencode(implode(',', $productCodes));

$flavor = $GLOBALS['sugar_flavor'] ?? '';
$version = $GLOBALS['sugar_version'] ?? '';
$language = $GLOBALS['current_language'] ?? '';
$action = $GLOBALS['action'] ?? '';
$status = getVersionStatus($version);
$key = $GLOBALS['key'] ?? '';
$module = 'DocuSignAdmin';

$url = "https://www.sugarcrm.com/crm/product_doc.php?edition={$flavor}&version={$version}&lang={$language}&" .
    "products={$productCodes}&help_action={$action}&status={$status}&key={$key}&module={$module}";

$connector_strings = [
    'LBL_LICENSING_INFO' => "A DocuSign kapcsolat használata:
        <br> -Integrációs kulcs létrehozása
        <br> -DocuSign Connect létrehozása a borítékok számára
        (vagyis a DocuSign által a Sugar belépőpontok számára használt webhookok)
        <br> - Új alkalmazás telepítése a DocuSignban, ami biztosítja az átirányító hivatkozások beillesztését és a titkos kulcs létrehozását.
        Az átirányító hivatkozásnak a következőnek kell lennie: https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect
        <br> Ha IP-korlátozások vannak érvényben a Sugar munkameneten, akkor helyezze a DocuSign e-mail címeit fehérlistára",
    'environment' => 'Környezet',
    'integration_key' => 'Integrációs kulcs',
    'client_secret' => 'Ügyféltitok',
];
