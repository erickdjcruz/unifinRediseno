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
    'LBL_LICENSING_INFO' => "Pasos para usar el conector DocuSign:
        <br> - Genere una clave de integración.
        <br> - Habilite el conector para sobres de DocuSign (es decir,  el gancho web usado por DocuSign para suscribirse a un punto de entrada de Sugar).
        <br> - Configure una nueva aplicacion en DocuSign y asegúrese de insertar el URI de redirección y generar una clave secreta.
        El URI de redirección debe ser https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect
        <br> En caso de restricciones de la en una instancia de Sugar, incluya la dirección IP de DocuSign en la lista blanca.",
    'environment' => 'Entorno',
    'integration_key' => 'Clave de integración',
    'client_secret' => 'Secreto del cliente',
];
