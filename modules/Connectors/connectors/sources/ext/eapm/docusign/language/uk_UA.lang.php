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
    'LBL_LICENSING_INFO' => "Для використання з’єднувача DocuSign виконайте такі кроки:
         <br> - Згенеруйте ключ інтеграції
         <br> - Увімкніть опцію \"Підключення DocuSign для конвертів\"
         (тобто вебперехоплювач, який використовується DocuSign для підписки на точку входу Sugar)
         <br> - Налаштуйте нову програму в DocuSign і переконайтеся, що ви вставили URI переспрямування та згенерували секретний ключ.
         URI переспрямування має бути https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect
         <br> У разі обмежень IP-адреси для екземпляра Sugar додайте в білий список IP-адреси DocuSign",
    'environment' => 'Середовище',
    'integration_key' => 'Ключ інтеграції',
    'client_secret' => 'Секретний ключ клієнта',
];
