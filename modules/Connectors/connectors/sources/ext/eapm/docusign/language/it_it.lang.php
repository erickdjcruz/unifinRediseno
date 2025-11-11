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
    'LBL_LICENSING_INFO' => "Passaggi per utilizzare il connettore DocuSign:
        <br> - Genera una chiave di integrazione
        <br> - Abilita DocuSign Connect per le buste
        (ovvero il webhook utilizzato da DocuSign per la sottoscrizione a un punto di accesso Sugar)
        <br> - Imposta una nuova applicazione in DocuSign e assicurati di inserire l'uri di reindirizzamento e generare una chiave segreta.
        L'uri di reindirizzamento deve essere https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect
        <br> In caso di restrizioni IP sull'istanza di Sugar, inserisci nella whitelist gli indirizzi IP di DocuSign",
    'environment' => 'Ambiente',
    'integration_key' => 'Chiave di integrazione',
    'client_secret' => 'Client Secret',
];
