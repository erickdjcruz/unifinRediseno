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
    'LBL_LICENSING_INFO' => "Schritte zur Verwendung des DocuSign Connectors:
        <br> - Generieren Sie einen Integrationsschlüssel.
        <br> - Aktivieren Sie DocuSign Connect für Envelopes
        (d.h. den Webhook, der von DocuSign für das Abonnieren eines Sugar-Einstiegspunkts verwendet wird)
        <br> - Richten Sie eine neue Anwendung in DocuSign ein und stellen Sie sicher, dass Sie die Redirect-URI einfügen und einen geheimen Schlüssel erzeugen.
        Die Redirect-URI muss https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect lauten.
        <br> Falls es IP-Beschränkungen auf der Sugar-Instanz gibt, nehmen Sie die IP-Adressen von DocuSign in die Whitelist auf.",
    'environment' => 'Umgebung',
    'integration_key' => 'Integrationsschlüssel',
    'client_secret' => 'Client Secret',
];
