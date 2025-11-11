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
    'LBL_LICENSING_INFO' => "Stappen voor gebruik DocuSign connector:
        <br> - Genereer een integratiesleutel
        <br> - Schakel DocuSign Connect in voor Enveloppen
        (bijv. de webhook die DocuSign gebruikt voor aanmelding voor een Sugar toegangspunt)
        <br> - Instellen van een nieuwe toepassing in DocuSign en ervoor zorgen dat de herleidings-URL wordt ingevoegd en een geheime sleutel wordt gegenereerd.
        De herleidings-URL moet https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect zijn
        <br> In geval van IP-beperkingen van Sugar-exemplaar, zet u de IP-adressen van DocuSign op de witte lijst",
    'environment' => 'Omgeving',
    'integration_key' => 'Integratiesleutel',
    'client_secret' => 'Geheim client',
];
