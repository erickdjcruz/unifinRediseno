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
    "produktet={$productCodes}&help_action={$action}&status={$status}&key={$key}&module={$module}";

$connector_strings = [
    'LBL_LICENSING_INFO' => "Hapat për të përdorur lidhësin e DocuSign:
        <br> - Gjenero një çelës integrimi
        <br> - Aktivizo DocuSign Connect për zarfet
        (d.m.th. webhook i përdorur nga DocuSign për abonim në një pikë hyrjeje të Sugar)
        <br> - Konfiguro një aplikacion të ri në DocuSign dhe sigurohu që të vendosësh url-në e ridrejtimit dhe të gjenerosh një kod sekret.
        Url-ja e ridrejtimit duhet të jetë https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect
        <br> Në rastin e kufizimeve të IP-së në rastin e Sugar, përfshi në listën e besuar adresat e IP-së së DocuSign",
    'environment' => 'Mjedisi',
    'integration_key' => 'Kodi i integrimit',
    'client_secret' => 'Kodi sekret i klientit',
];
