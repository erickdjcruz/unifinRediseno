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
    'LBL_LICENSING_INFO' => "Pași pentru a utiliza conectorul DocuSign:
        <br> - Generați o cheie de integrare
        <br> - Activați conectarea DocuSign pentru plicuri
        (mai exact, webhook-ul folosit de DocuSign pentru abonarea la un punct de intrare Sugar)
        <br> - Configurați o nouă aplicație în DocuSign, asigurați-vă că introduceți URI de redirecționare și generați o cheie secretă.
        URI de redirecționare trebuie să fie https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect
        <br> În cazul restricțiilor IP pentru instanța Sugar, puneți pe lista albă adresele IP ale DocuSign",
    'environment' => 'Mediu',
    'integration_key' => 'Cheie de integrare',
    'client_secret' => 'Secret client',
];
