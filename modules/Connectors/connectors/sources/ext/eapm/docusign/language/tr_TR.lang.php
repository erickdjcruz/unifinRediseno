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
    "ürünler={$productCodes}&yardım_eylemi={$action}&durum={$status}&tuş={$key}&modül={$module}";

$connector_strings = [
    'LBL_LICENSING_INFO' => "DocuSign konnektörünün kullanımına yönelik adımlar:
        <br> - Bir entegrasyon anahtarı oluşturun
        <br> - Zarflar için DocuSign Connect'i etkinleştirin
        (ör. DocuSign tarafından bir Sugar giriş noktasına abone olmak için kullanılan webhook)
        <br> - DocuSign'da yeni bir uygulama kurun ve yönlendirme uri'sini eklediğinizden ve bir gizli anahtar oluşturduğunuzdan emin olun.
        Yönlendirme uri'si https://SUGAR_URL/oauth-handler/DocuSignOauth2Redirect olmalıdır.
        <br> Sugar örneğinde IP kısıtlamaları olması durumunda, DocuSign'ın IP adreslerini beyaz listeye alın",
    'environment' => 'Ortam',
    'integration_key' => 'Entegrasyon Anahtarı',
    'client_secret' => 'Müşteri Şifresi',
];
