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
    'LBL_LICENSING_INFO' => "DocuSign-liittimen käyttö:
        <br> - Luo integrointiavain.
        <br> - Ota käyttöön DocuSign Connect kirjekuorille.
        (Eli webhook, jota DocuSign käyttää Sugarin pääsypisteen tilaukseen).
        <br> - Luo uusi sovellus DocuSignissa ja muista lisätä uudelleenohjaus-URI ja luoda salainen avain.
        Uudelleenohjaus-URIn on oltava https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect.
        <br>Jos Sugar-instanssissa on IP-rajoituksia, lisää DocuSignin IP-osoitteet sallittujen luetteloon.",
    'environment' => 'Ympäristö',
    'integration_key' => 'Integrointiavain',
    'client_secret' => 'Asiakkaan salaisuus',
];
