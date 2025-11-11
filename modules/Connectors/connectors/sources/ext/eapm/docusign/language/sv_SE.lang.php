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
    "produkter={$productCodes}&help_action={$action}&status={$status}&key={$key}&module={$module}";

$connector_strings = [
    'LBL_LICENSING_INFO' => "Steg för att använda DocuSign connector:
        <br> - Generera en integreringsnyckel
        <br> - Aktivera DocuSign Connect för kuvert
        (i.e. den webhook som används av DocuSign för att prenumerera på en Sugar-ingångspunkt)
        <br> - Skapa en ny tillämpning i DocuSign och se till att infoga omdirigeringsadressen och generera en hemlig nyckel.
        Omdirigeringsadressen måste vara https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect
        <br> Om det finns IP-begränsningar för Sugar-förekomsten ska du godkänna DocuSign's IP-adresser",
    'environment' => 'Miljö',
    'integration_key' => 'Integreringsnyckel',
    'client_secret' => 'Klienthemlighet',
];
