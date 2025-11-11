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
    'LBL_LICENSING_INFO' => "Passos per a utilitzar el connector de DocuSign:
        <br> - Genereu una clau d'integració
        <br> - Habiliteu DocuSign Connect per als sobres
        (és a dir, el \"webhook\" que utilitza DocuSign per subscriure's a un punt d'entrada de Sugar)
        <br> - Configureu una nova aplicació a DocuSign i assegureu-vos d'inserir l'uri de redirecció i de generar una clau secreta..
        L'uri de refirecció ha de ser https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect
        <br> En cas de restriccions d'IP a la instància de Sugar, afegiu a la llista segura les adreces IP de DocuSign",
    'environment' => 'Entorn',
    'integration_key' => 'Clau d\'integració',
    'client_secret' => 'Secret de client',
];
