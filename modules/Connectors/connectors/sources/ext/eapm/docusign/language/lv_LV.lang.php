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
    "produkti={$productCodes}&help_action={$action}&status={$status}&key={$key}&modulis={$module}";

$connector_strings = [
    'LBL_LICENSING_INFO' => "Darbības, lai varētu izmantot DocuSign konektoru:
         <br> – Ģenerējiet integrācijas atslēgu
         <br> – Iespējojiet DocuSign pieslēgumu aploksnēm
         (t.i., tīmekļa pārtvērēju, ko DocuSign izmanto, lai abonētu Sugar ieejas punktu)
         <br> – Iestatiet jaunu lietotni DocuSign, obligāti ievietojiet pārvirzīšanas URI un ģenerējiet slepeno atslēgu.
         Pārvirzīšanas URI ir jābūt: https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect
         <br> Ja ir IP ierobežojumi attiecībā uz Sugar instanci, ievietojiet baltajā sarakstā IP adreses",
    'environment' => 'Vide',
    'integration_key' => 'Integrācijas atslēga',
    'client_secret' => 'Klienta noslēpums',
];
