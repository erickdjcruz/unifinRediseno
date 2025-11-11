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
    'LBL_LICENSING_INFO' => "Trin til at bruge DocuSign-forbindelse:
        <br> - Generér en integrationsnøgle
        <br> - Aktivér DocuSign Connect for konvolutter
        (dvs. den webhook, der bruges af DocuSign til tilmelding til et Sugar-indgangspunkt)
        <br> - Konfigurér en ny app i DocuSign, og sørg for at indsætte omdirigerings-URI og generere en hemmelig nøgle.
        Omdirigerings-URI skal være https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect
        <br> ved IP-begrænsninger på Sugar-instansen skal der oprettes en hvidliste med DocuSigns IP-adresser",
    'environment' => 'Miljø',
    'integration_key' => 'Integrationsnøgle',
    'client_secret' => 'Klienthemmelighed',
];
