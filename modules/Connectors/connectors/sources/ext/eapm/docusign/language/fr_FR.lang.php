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
    'LBL_LICENSING_INFO' => "Étapes pour utiliser le connecteur DocuSign :
        <br> - Générer une clé d'intégration
        <br> - Activer le connecteur DocuSign pour les enveloppes
        (c'est-à-dire le webhook utilisé par DocuSign pour s'abonner à un point d'entrée Sugar)
        <br> - Configurer une nouvelle application dans DocuSign et s'assurer d'insérer l'uri de redirection et de générer une clé secrète.
        L'uri de redirection doit être https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect
        <br> En cas de restrictions d'IP sur l'instance Sugar, mettre sur liste blanche les adresses IP de DocuSign",
    'environment' => 'Environnement',
    'integration_key' => 'Clé d\'intégration',
    'client_secret' => 'Client secret',
];
